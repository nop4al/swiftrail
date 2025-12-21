<?php

namespace App\Http\Controllers\Api;

use App\Models\Refund;
use App\Models\Booking;
use App\Models\SwiftPayWallet;
use Illuminate\Http\Request;

class RefundController extends ApiBaseController
{
    // ========== ADMIN ENDPOINTS ==========
    
    // GET /api/v1/admin/refunds
    public function index(Request $request)
    {
        $status = $request->query('status');
        
        $query = Refund::query()->with('user', 'booking', 'swiftPayWallet');
        
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $refunds = $query->orderBy('created_at', 'desc')->get();
        return $this->success($refunds, 'Refunds retrieved');
    }

    // POST /api/v1/admin/refunds
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticket_number' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'status' => 'required|in:Menunggu,Disetujui,Selesai,Ditolak'
        ]);

        try {
            $refund = Refund::create($validated);
            return $this->success($refund, 'Refund created successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create refund: ' . $e->getMessage(), null, 400);
        }
    }

    // GET /api/v1/admin/refunds/{id}
    public function show($id)
    {
        $refund = Refund::with('user', 'booking', 'swiftPayWallet')->find($id);
        
        if (!$refund) {
            return $this->error('Refund not found', null, 404);
        }

        return $this->success($refund, 'Refund retrieved');
    }

    // PUT /api/v1/admin/refunds/{id}
    public function update(Request $request, $id)
    {
        $refund = Refund::find($id);
        
        if (!$refund) {
            return $this->error('Refund not found', null, 404);
        }

        $validated = $request->validate([
            'ticket_number' => 'sometimes|required|string',
            'user_id' => 'sometimes|nullable|exists:users,id',
            'booking_id' => 'sometimes|nullable|exists:bookings,id',
            'amount' => 'sometimes|required|numeric|min:0',
            'reason' => 'sometimes|required|string',
            'status' => 'sometimes|required|in:Menunggu,Disetujui,Selesai,Ditolak'
        ]);

        try {
            $refund->update($validated);
            return $this->success($refund, 'Refund updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update refund: ' . $e->getMessage(), null, 400);
        }
    }

    // PATCH /api/v1/admin/refunds/{id}/status
    public function updateStatus(Request $request, $id)
    {
        $refund = Refund::find($id);
        
        if (!$refund) {
            return $this->error('Refund not found', null, 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:Menunggu,Disetujui,Selesai,Ditolak'
        ]);

        try {
            $refund->update($validated);
            
            // If status is Selesai, transfer amount to SwiftPay wallet
            if ($validated['status'] === 'Selesai' && $refund->swiftPayWallet) {
                $wallet = $refund->swiftPayWallet;
                $wallet->update([
                    'balance' => $wallet->balance + $refund->amount
                ]);
            }
            
            return $this->success($refund, 'Refund status updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update refund status: ' . $e->getMessage(), null, 400);
        }
    }

    // DELETE /api/v1/admin/refunds/{id}
    public function destroy($id)
    {
        $refund = Refund::find($id);
        
        if (!$refund) {
            return $this->error('Refund not found', null, 404);
        }

        try {
            $refund->delete();
            return $this->success(null, 'Refund deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete refund: ' . $e->getMessage(), null, 400);
        }
    }

    // GET /api/v1/admin/refunds/stats
    public function getStats()
    {
        $stats = [
            'menunggu' => Refund::where('status', 'Menunggu')->count(),
            'disetujui' => Refund::where('status', 'Disetujui')->count(),
            'selesai' => Refund::where('status', 'Selesai')->count(),
            'ditolak' => Refund::where('status', 'Ditolak')->count(),
            'total_amount_menunggu' => Refund::where('status', 'Menunggu')->sum('amount'),
            'total_amount_selesai' => Refund::where('status', 'Selesai')->sum('amount'),
        ];

        return $this->success($stats, 'Refund statistics retrieved');
    }

    // ========== USER ENDPOINTS ==========

    // POST /api/refunds - Request refund
    public function requestRefund(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'reason' => 'required|string|min:5',
            'description' => 'required|string|min:10',
            'swift_pay_wallet_id' => 'required|exists:swift_pay_wallets,id'
        ]);

        try {
            // Check if booking belongs to user
            $booking = Booking::find($validated['booking_id']);
            
            if ($booking->user_id !== $user->id) {
                return $this->error('Unauthorized - This booking does not belong to you', null, 403);
            }

            // Check if wallet belongs to user
            $wallet = SwiftPayWallet::find($validated['swift_pay_wallet_id']);
            
            if ($wallet->user_id !== $user->id) {
                return $this->error('Unauthorized - This wallet does not belong to you', null, 403);
            }

            // Check if refund already exists for this booking
            $existingRefund = Refund::where('booking_id', $validated['booking_id'])->first();
            if ($existingRefund && $existingRefund->status !== 'Ditolak') {
                return $this->error('Refund for this booking already exists', null, 400);
            }

            // Calculate refund amount (95% of booking price, 5% admin fee)
            $refundAmount = $booking->price * 0.95;

            // Create refund request
            $refund = Refund::create([
                'user_id' => $user->id,
                'booking_id' => $validated['booking_id'],
                'ticket_number' => $booking->ticket_number,
                'amount' => $refundAmount,
                'reason' => $validated['reason'],
                'refund_reason' => $validated['reason'],
                'description' => $validated['description'],
                'swift_pay_wallet_id' => $validated['swift_pay_wallet_id'],
                'status' => 'Menunggu'
            ]);

            return $this->success($refund, 'Refund request submitted successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to submit refund request: ' . $e->getMessage(), null, 400);
        }
    }

    // GET /api/refunds - Get user's refunds
    public function getUserRefunds(Request $request)
    {
        $user = $request->user();
        $status = $request->query('status');

        $query = Refund::where('user_id', $user->id)->with('booking', 'swiftPayWallet');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $refunds = $query->orderBy('created_at', 'desc')->paginate(10);

        return $this->success($refunds, 'User refunds retrieved');
    }

    // GET /api/refunds/{id} - Get specific refund
    public function getUserRefund($id, Request $request)
    {
        $user = $request->user();
        $refund = Refund::with('booking', 'swiftPayWallet')->find($id);

        if (!$refund) {
            return $this->error('Refund not found', null, 404);
        }

        if ($refund->user_id !== $user->id) {
            return $this->error('Unauthorized', null, 403);
        }

        return $this->success($refund, 'Refund retrieved');
    }

    // GET /api/user/tickets - Get user's bookings for refund request
    public function getUserTickets(Request $request)
    {
        $user = $request->user();

        $tickets = Booking::where('user_id', $user->id)
            ->with(['schedule.route.departureStation', 'schedule.route.arrivalStation', 'schedule.train'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                $route = $booking->schedule->route;
                return [
                    'id' => $booking->id,
                    'code' => $booking->ticket_number,
                    'trainName' => $booking->schedule->train->name ?? 'Unknown',
                    'from' => ($route && $route->departureStation) ? $route->departureStation->name : 'Unknown',
                    'to' => ($route && $route->arrivalStation) ? $route->arrivalStation->name : 'Unknown',
                    'price' => $booking->price,
                    'departureDate' => $booking->schedule->departure_time,
                    'status' => $booking->status
                ];
            });

        return $this->success($tickets, 'User tickets retrieved');
    }

    // GET /api/user/swift-pay-wallets - Get user's SwiftPay wallets
    public function getUserWallets(Request $request)
    {
        $user = $request->user();

        $wallets = SwiftPayWallet::where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        return $this->success($wallets, 'User wallets retrieved');
    }
}

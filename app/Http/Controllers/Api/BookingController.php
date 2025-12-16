<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    /*Get available seats for a schedule*/
    public function getAvailableSeats($scheduleId): JsonResponse
    {
        try {
            $schedule = Schedule::with('train')->find($scheduleId);

            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found'
                ], 404);
            }

            // Get booked seats
            $bookedSeats = Booking::where('schedule_id', $scheduleId)
                ->where('status', '!=', 'cancelled')
                ->pluck('seat_number')
                ->toArray();

            // Generate all seats based on train type
            $allSeats = $this->generateSeats($schedule->train->type, $schedule->train->capacity);

            // Separate available and booked
            $available = array_diff($allSeats, $bookedSeats);
            $booked = array_intersect($allSeats, $bookedSeats);

            return response()->json([
                'success' => true,
                'data' => [
                    'scheduleId' => $scheduleId,
                    'trainType' => $schedule->train->type,
                    'totalSeats' => count($allSeats),
                    'availableSeats' => count($available),
                    'bookedSeats' => count($booked),
                    'seatMap' => [
                        'available' => array_values($available),
                        'booked' => array_values($booked),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving seats: ' . $e->getMessage()
            ], 500);
        }
    }

    /* Create a booking */
    public function createBooking(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'schedule_id' => 'required|exists:schedules,id',
                'user_id' => 'required|exists:users,id',
                'passenger_name' => 'required|string',
                'nik' => 'nullable|string',
                'passenger_type' => 'required|in:Dewasa,Anak,Bayi',
                'seat_number' => 'required|string',
                'class' => 'required|string',
                'price' => 'required|numeric',
            ]);

            // Check if seat is already booked
            $existingBooking = Booking::where('schedule_id', $validated['schedule_id'])
                ->where('seat_number', $validated['seat_number'])
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seat already booked'
                ], 409);
            }

            // Generate booking code
            $bookingCode = 'BK' . date('YmdHis') . random_int(1000, 9999);

            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'user_id' => $validated['user_id'],
                'schedule_id' => $validated['schedule_id'],
                'passenger_name' => $validated['passenger_name'],
                'nik' => $validated['nik'],
                'passenger_type' => $validated['passenger_type'],
                'seat_number' => $validated['seat_number'],
                'class' => $validated['class'],
                'price' => $validated['price'],
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $booking->id,
                    'bookingCode' => $booking->booking_code,
                    'status' => $booking->status,
                    'passengerName' => $booking->passenger_name,
                    'seatNumber' => $booking->seat_number,
                    'price' => $booking->price,
                    'createdAt' => $booking->created_at,
                ],
                'message' => 'Booking created successfully'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /* Get booking details */
    public function getBooking($bookingId): JsonResponse
    {
        try {
            $booking = Booking::with(['user', 'schedule.train', 'schedule.route'])->find($bookingId);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $booking->id,
                    'bookingCode' => $booking->booking_code,
                    'ticketNumber' => $booking->ticket_number,
                    'passengerName' => $booking->passenger_name,
                    'passengerType' => $booking->passenger_type,
                    'seatNumber' => $booking->seat_number,
                    'class' => $booking->class,
                    'price' => $booking->price,
                    'status' => $booking->status,
                    'trainName' => $booking->schedule->train->name,
                    'departureTime' => $booking->schedule->departure_time,
                    'arrivalTime' => $booking->schedule->arrival_time,
                    'fromStation' => $booking->schedule->route->departureStation->name,
                    'toStation' => $booking->schedule->route->arrivalStation->name,
                    'createdAt' => $booking->created_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /* Cancel booking */
    public function cancelBooking($bookingId): JsonResponse
    {
        try {
            $booking = Booking::find($bookingId);

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            if ($booking->status === 'used' || $booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel this booking'
                ], 409);
            }

            $booking->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /* Generate seat layout based on train type */
    private function generateSeats($trainType, $capacity): array
    {
        $seats = [];

        if ($trainType === 'compartment') {
            // Compartment: 2 seats per row (A, B)
            $rows = ceil($capacity / 2);
            for ($i = 1; $i <= $rows; $i++) {
                $seats[] = "{$i}A";
                $seats[] = "{$i}B";
            }
        } else {
            // Standard: 4 seats per row (A, B, C, D)
            $rows = ceil($capacity / 4);
            for ($i = 1; $i <= $rows; $i++) {
                $seats[] = "{$i}A";
                $seats[] = "{$i}B";
                $seats[] = "{$i}C";
                $seats[] = "{$i}D";
            }
        }

        return array_slice($seats, 0, $capacity);
    }
}

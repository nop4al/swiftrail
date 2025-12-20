<?php

namespace App\Http\Controllers\Api;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Train;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Get available trains for a route and date
     */
    public function getAvailableTrains(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'from_station' => 'required|string',
                'to_station' => 'required|string',
                'date' => 'required|date'
            ]);

            $fromStation = trim($request->from_station);
            $toStation = trim($request->to_station);
            $date = $request->date;

            Log::info('Searching trains:', [
                'from_station' => $fromStation,
                'to_station' => $toStation,
                'date' => $date
            ]);

            // Get all active schedules with relationships
            $schedules = Schedule::with(['train', 'route.departureStation', 'route.arrivalStation'])
                ->where('status', 'active')
                ->get();

            Log::info('Total active schedules: ' . count($schedules));

            $filteredTrains = [];
            
            foreach ($schedules as $schedule) {
                // Skip if relationships are missing
                if (!$schedule->train || !$schedule->route || !$schedule->route->departureStation || !$schedule->route->arrivalStation) {
                    Log::warning('Schedule missing relationships: ' . $schedule->id);
                    continue;
                }
                
                // Check if stations match
                $depMatch = strtolower($schedule->route->departureStation->name) === strtolower($fromStation) ||
                           strtolower($schedule->route->departureStation->code) === strtolower($fromStation);
                           
                $arrMatch = strtolower($schedule->route->arrivalStation->name) === strtolower($toStation) ||
                           strtolower($schedule->route->arrivalStation->code) === strtolower($toStation);

                if (!($depMatch && $arrMatch)) {
                    continue;
                }

                // Check if schedule operates on the requested day
                $dayName = \Carbon\Carbon::parse($date)->format('l'); // Get day name (Monday, Tuesday, etc.)
                $days = $schedule->days ? explode(',', str_replace(' ', '', $schedule->days)) : [];
                
                // If no days specified, it operates every day
                // If days specified, check if current day is in the list
                $operatesOnDay = empty($days) || in_array(substr($dayName, 0, 3), $days) || in_array($dayName, $days);
                
                if (!$operatesOnDay) {
                    Log::info('Schedule ' . $schedule->id . ' does not operate on ' . $dayName);
                    continue;
                }
                
                $availableSeats = intval($schedule->train->capacity * 0.75);
                
                $filteredTrains[] = [
                    'schedule_id' => $schedule->id,
                    'train_id' => $schedule->train_id,
                    'train_name' => $schedule->train->name,
                    'train_code' => $schedule->train->code,
                    'departure' => $schedule->departure_time,
                    'arrival' => $schedule->arrival_time,
                    'travel_date' => $date,
                    'duration' => $this->calculateDuration($schedule->departure_time, $schedule->arrival_time),
                    'from_station' => $schedule->route->departureStation->name,
                    'to_station' => $schedule->route->arrivalStation->name,
                    'train_type' => $schedule->train->type,
                    'capacity' => $schedule->train->capacity,
                    'seats_available' => $availableSeats,
                    'min_price' => 250000,
                    'max_price' => 450000,
                    'classes' => [
                        ['type' => 'economy', 'name' => 'Economy', 'price' => 250000, 'available' => intval($availableSeats * 0.5)],
                        ['type' => 'business', 'name' => 'Business', 'price' => 350000, 'available' => intval($availableSeats * 0.3)],
                        ['type' => 'executive', 'name' => 'Executive', 'price' => 450000, 'available' => intval($availableSeats * 0.2)],
                    ]
                ];
            }

            Log::info('Filtered trains count: ' . count($filteredTrains));

            return response()->json([
                'success' => true,
                'data' => $filteredTrains
            ]);
        } catch (\Exception $e) {
            Log::error('BookingController.getAvailableTrains error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

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
                'qr_code' => $bookingCode,
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
                    'qrCode' => $booking->qr_code,
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
            // Try to find by ID first, then by booking_code (from QR scan)
            $booking = Booking::with(['user', 'schedule.train', 'schedule.route.departureStation', 'schedule.route.arrivalStation'])
                ->where(function ($query) use ($bookingId) {
                    $query->where('id', $bookingId)
                        ->orWhere('booking_code', $bookingId);
                })
                ->first();

            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $schedule = $booking->schedule;
            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found for this booking'
                ], 404);
            }

            $train = $schedule->train;
            $route = $schedule->route;
            
            if (!$route) {
                return response()->json([
                    'success' => false,
                    'message' => 'Route not found for this booking'
                ], 404);
            }

            $fromStation = $route->departureStation;
            $toStation = $route->arrivalStation;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'ticket_number' => $booking->ticket_number,
                    'passenger_name' => $booking->passenger_name,
                    'nik' => $booking->nik,
                    'passenger_type' => $booking->passenger_type,
                    'seat_number' => $booking->seat_number,
                    'class' => $booking->class,
                    'price' => $booking->price,
                    'status' => $booking->status,
                    'qr_code' => $booking->qr_code,
                    'created_at' => $booking->created_at,
                    'train' => [
                        'id' => $train ? $train->id : null,
                        'name' => $train ? $train->name : 'N/A',
                        'number' => $train ? ($train->train_number ?? 'N/A') : 'N/A',
                        'type' => $train ? ($train->type ?? 'N/A') : 'N/A'
                    ],
                    'schedule' => [
                        'id' => $schedule->id,
                        'departure_time' => $schedule->departure_time,
                        'arrival_time' => $schedule->arrival_time,
                        'travel_date' => $schedule->travel_date,
                        'available_seats' => $schedule->available_seats ?? 0,
                        'price' => $schedule->price
                    ],
                    'from_station' => [
                        'id' => $fromStation ? $fromStation->id : null,
                        'name' => $fromStation ? $fromStation->name : 'N/A',
                        'code' => $fromStation ? $fromStation->code : 'N/A'
                    ],
                    'to_station' => [
                        'id' => $toStation ? $toStation->id : null,
                        'name' => $toStation ? $toStation->name : 'N/A',
                        'code' => $toStation ? $toStation->code : 'N/A'
                    ]
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

    /**
     * Helper: Calculate duration between times
     */
    private function calculateDuration($departure, $arrival)
    {
        try {
            $dep = \DateTime::createFromFormat('H:i:s', $departure);
            $arr = \DateTime::createFromFormat('H:i:s', $arrival);

            if (!$dep || !$arr) {
                return '5h 30m'; // Default fallback
            }

            if ($arr < $dep) {
                $arr->add(new \DateInterval('P1D'));
            }

            $interval = $dep->diff($arr);
            return $interval->h . 'h ' . $interval->i . 'm';
        } catch (\Exception $e) {
            return '5h 30m'; // Default fallback on error
        }
    }

    /**
     * Helper: Get price for class
     */
    private function getPriceForClass($class)
    {
        $prices = [
            'ekonomi' => 250000,
            'bisnis' => 350000,
            'eksekutif' => 450000
        ];

        return $prices[$class] ?? 250000;
    }

    /**
     * Helper: Match station names flexibly
     * Matches if one string contains the other, or if key words overlap (minimum 1 word)
     */
    private function stationNameMatch($dbName, $searchName)
    {
        // Direct substring match (case insensitive)
        if (stripos($dbName, $searchName) !== false || stripos($searchName, $dbName) !== false) {
            return true;
        }
        
        // Check if key words overlap
        $dbWords = array_filter(preg_split('/\s+/', strtolower($dbName)));
        $searchWords = array_filter(preg_split('/\s+/', strtolower($searchName)));
        
        if (empty($searchWords)) {
            return false;
        }
        
        // If at least 1 or more words match, consider it a match
        $matches = count(array_intersect($dbWords, $searchWords));
        return $matches >= 1;
    }
}

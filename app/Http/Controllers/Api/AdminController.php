<?php

namespace App\Http\Controllers\Api;

use App\Models\{Station, Train, Route, Schedule, Refund, User};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AdminController extends ApiBaseController
{
    // ============ STATION CRUD ============

    /**
     * Get all stations
     */
    public function getStations(): JsonResponse
    {
        try {
            $stations = Station::all();
            return $this->success($stations, 'Stations retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve stations', [], 500);
        }
    }

    /**
     * Get station by ID
     */
    public function getStation($id): JsonResponse
    {
        try {
            $station = Station::findOrFail($id);
            return $this->success($station, 'Station retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Station not found', [], 404);
        }
    }

    /**
     * Create new station
     */
    public function createStation(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|unique:stations,code|max:10',
                'name' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'active' => 'nullable|boolean'
            ]);

            $validated['active'] = $validated['active'] ?? true;
            $station = Station::create($validated);
            
            return $this->success($station, 'Station created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to create station', [], 500);
        }
    }

    /**
     * Update station
     */
    public function updateStation(Request $request, $id): JsonResponse
    {
        try {
            $station = Station::findOrFail($id);
            
            $validated = $request->validate([
                'code' => 'sometimes|string|unique:stations,code,' . $id . '|max:10',
                'name' => 'sometimes|string|max:100',
                'city' => 'sometimes|string|max:100',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'active' => 'nullable|boolean'
            ]);

            $station->update($validated);
            
            return $this->success($station, 'Station updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to update station', [], 500);
        }
    }

    /**
     * Delete station
     */
    public function deleteStation($id): JsonResponse
    {
        try {
            $station = Station::findOrFail($id);
            $station->delete();
            
            return $this->success(null, 'Station deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete station', [], 500);
        }
    }

    // ============ TRAIN CRUD ============

    /**
     * Get all trains
     */
    public function getTrains(): JsonResponse
    {
        try {
            $trains = Train::with('schedules.route')->get();
            return $this->success($trains, 'Trains retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve trains', [], 500);
        }
    }

    /**
     * Get train by ID
     */
    public function getTrain($id): JsonResponse
    {
        try {
            $train = Train::with('schedules.route')->findOrFail($id);
            return $this->success($train, 'Train retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Train not found', [], 404);
        }
    }

    /**
     * Create new train
     */
    public function createTrain(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|unique:trains,code|max:20',
                'name' => 'required|string|max:100',
                'type' => 'required|string|max:50',
                'capacity' => 'required|integer|min:1',
                'status' => 'nullable|in:active,inactive'
            ]);

            $validated['status'] = $validated['status'] ?? 'active';
            $train = Train::create($validated);
            
            return $this->success($train, 'Train created successfully', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to create train', [], 500);
        }
    }

    /**
     * Update train
     */
    public function updateTrain(Request $request, $id): JsonResponse
    {
        try {
            $train = Train::findOrFail($id);
            
            $validated = $request->validate([
                'code' => 'sometimes|string|unique:trains,code,' . $id . '|max:20',
                'name' => 'sometimes|string|max:100',
                'type' => 'sometimes|string|max:50',
                'capacity' => 'sometimes|integer|min:1',
                'status' => 'sometimes|in:active,inactive'
            ]);

            $train->update($validated);
            
            return $this->success($train, 'Train updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to update train', [], 500);
        }
    }

    /**
     * Delete train
     */
    public function deleteTrain($id): JsonResponse
    {
        try {
            $train = Train::findOrFail($id);
            
            // Delete related schedules first
            $train->schedules()->delete();
            $train->delete();
            
            return $this->success(null, 'Train deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete train', [], 500);
        }
    }

    // ============ ROUTE CRUD ============

    /**
     * Get all routes
     */
    public function getRoutes(): JsonResponse
    {
        try {
            $routes = Route::with(['departureStation', 'arrivalStation', 'schedules'])->get();
            return $this->success($routes, 'Routes retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve routes', [], 500);
        }
    }

    /**
     * Get route by ID
     */
    public function getRoute($id): JsonResponse
    {
        try {
            $route = Route::with(['departureStation', 'arrivalStation', 'schedules'])->findOrFail($id);
            return $this->success($route, 'Route retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Route not found', [], 404);
        }
    }

    /**
     * Create new route
     */
    public function createRoute(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'code' => 'required|string|unique:routes,code|max:20',
                'departure_station_id' => 'required|exists:stations,id',
                'arrival_station_id' => 'required|exists:stations,id|different:departure_station_id',
                'distance' => 'nullable|integer|min:1',
                'duration' => 'nullable|integer|min:1',
                'base_price' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:active,inactive'
            ]);

            $validated['status'] = $validated['status'] ?? 'active';
            $route = Route::create($validated);
            
            $route->load(['departureStation', 'arrivalStation']);
            
            return $this->success($route, 'Route created successfully', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to create route', [], 500);
        }
    }

    /**
     * Update route
     */
    public function updateRoute(Request $request, $id): JsonResponse
    {
        try {
            $route = Route::findOrFail($id);
            
            $validated = $request->validate([
                'code' => 'sometimes|string|unique:routes,code,' . $id . '|max:20',
                'departure_station_id' => 'sometimes|exists:stations,id',
                'arrival_station_id' => 'sometimes|exists:stations,id|different:departure_station_id',
                'distance' => 'nullable|integer|min:1',
                'duration' => 'nullable|integer|min:1',
                'base_price' => 'nullable|numeric|min:0',
                'status' => 'sometimes|in:active,inactive'
            ]);

            $route->update($validated);
            $route->load(['departureStation', 'arrivalStation']);
            
            return $this->success($route, 'Route updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to update route', [], 500);
        }
    }

    /**
     * Delete route
     */
    public function deleteRoute($id): JsonResponse
    {
        try {
            $route = Route::findOrFail($id);
            
            // Delete related schedules first
            $route->schedules()->delete();
            $route->delete();
            
            return $this->success(null, 'Route deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete route', [], 500);
        }
    }

    // ============ SCHEDULE CRUD ============

    /**
     * Get all schedules
     */
    public function getSchedules(): JsonResponse
    {
        try {
            $schedules = Schedule::with(['train', 'route.departureStation', 'route.arrivalStation'])->get();
            return $this->success($schedules, 'Schedules retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve schedules', [], 500);
        }
    }

    /**
     * Get schedule by ID
     */
    public function getSchedule($id): JsonResponse
    {
        try {
            $schedule = Schedule::with(['train', 'route.departureStation', 'route.arrivalStation'])->findOrFail($id);
            return $this->success($schedule, 'Schedule retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Schedule not found', [], 404);
        }
    }

    /**
     * Create new schedule
     */
    public function createSchedule(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'train_id' => 'required|exists:trains,id',
                'route_id' => 'required|exists:routes,id',
                'departure_time' => 'required|date_format:H:i',
                'arrival_time' => 'required|date_format:H:i',
                'days' => 'nullable|string|max:255',
                'status' => 'nullable|in:active,inactive'
            ]);

            $validated['status'] = $validated['status'] ?? 'active';
            $validated['days'] = $validated['days'] ?? 'Setiap Hari';
            
            $schedule = Schedule::create($validated);
            $schedule->load(['train', 'route.departureStation', 'route.arrivalStation']);
            
            return $this->success($schedule, 'Schedule created successfully', 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to create schedule', [], 500);
        }
    }

    /**
     * Update schedule
     */
    public function updateSchedule(Request $request, $id): JsonResponse
    {
        try {
            $schedule = Schedule::findOrFail($id);
            
            $validated = $request->validate([
                'train_id' => 'sometimes|exists:trains,id',
                'route_id' => 'sometimes|exists:routes,id',
                'departure_time' => 'sometimes|date_format:H:i',
                'arrival_time' => 'sometimes|date_format:H:i',
                'days' => 'nullable|string|max:255',
                'status' => 'sometimes|in:active,inactive'
            ]);

            $schedule->update($validated);
            $schedule->load(['train', 'route.departureStation', 'route.arrivalStation']);
            
            return $this->success($schedule, 'Schedule updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to update schedule', [], 500);
        }
    }

    /**
     * Delete schedule
     */
    public function deleteSchedule($id): JsonResponse
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->delete();
            
            return $this->success(null, 'Schedule deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete schedule', [], 500);
        }
    }

    // ============ REFUND MANAGEMENT ============

    /**
     * Get all refunds
     */
    public function getRefunds(): JsonResponse
    {
        try {
            $refunds = Refund::with(['booking.user'])->get();
            return $this->success($refunds, 'Refunds retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve refunds', [], 500);
        }
    }

    /**
     * Update refund status
     */
    public function updateRefundStatus(Request $request, $id): JsonResponse
    {
        try {
            $refund = Refund::findOrFail($id);
            
            $validated = $request->validate([
                'status' => 'required|in:pending,approved,rejected,completed'
            ]);

            $refund->update($validated);
            $refund->load('booking.user');
            
            return $this->success($refund, 'Refund status updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Validation failed', $e->errors(), 422);
        } catch (\Exception $e) {
            return $this->error('Failed to update refund status', [], 500);
        }
    }

    // ============ DASHBOARD STATS ============

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'total_stations' => Station::count(),
                'total_trains' => Train::count(),
                'total_routes' => Route::count(),
                'total_schedules' => Schedule::count(),
                'pending_refunds' => Refund::where('status', 'pending')->count(),
            ];
            
            return $this->success($stats, 'Dashboard stats retrieved successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve stats', [], 500);
        }
    }
}

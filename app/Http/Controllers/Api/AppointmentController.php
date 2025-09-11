<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Http\Resources\AppointmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Appointment::with(['services', 'contact']);

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $startTime = strtotime($request->start_date) * 1000;
            $endTime = strtotime($request->end_date) * 1000;
            $query->whereBetween('start_time', [$startTime, $endTime]);
        }

        // Filter by staff
        if ($request->has('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        // Filter by contact
        if ($request->has('contact_id')) {
            $query->where('contact_id', $request->contact_id);
        }

        // Filter by appointment type
        if ($request->has('type_id')) {
            $query->where('type_id', $request->type_id);
        }

        // Search by title
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $appointments = $query->orderBy('start_time')->get();

        return response()->json(AppointmentResource::collection($appointments));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'type_id' => ['nullable', 'string', 'size:10', 'exists:appointment_types,id'],
            'contact_id' => ['nullable', 'string', 'size:10', 'exists:contacts,id'],
            'staff_id' => ['nullable', 'string', 'size:10', 'exists:staff,id'],
            'start' => ['sometimes', 'integer'],
            'end' => ['sometimes', 'integer'],
            'start_time' => ['sometimes', 'integer'],
            'end_time' => ['sometimes', 'integer'],
            'service_ids' => ['sometimes', 'array'],
            'service_ids.*' => ['string', 'size:10', 'exists:services,id'],
        ]);

        // Use start/end from React app, fallback to start_time/end_time
        $startTime = $validated['start'] ?? $validated['start_time'] ?? null;
        $endTime = $validated['end'] ?? $validated['end_time'] ?? null;

        $appointment = Appointment::create([
            'id' => Str::random(10),
            'title' => $validated['title'],
            'type_id' => $validated['type_id'] ?? null,
            'contact_id' => $validated['contact_id'] ?? null,
            'staff_id' => $validated['staff_id'] ?? null,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        // Attach services if provided (snake_case only)
        $serviceIds = $validated['service_ids'] ?? [];
        if (is_string($serviceIds)) {
            $serviceIds = array_filter(array_map('trim', preg_split('/[\s,]+/', $serviceIds)));
        }
        if (!empty($serviceIds)) {
            // sync without detaching existing (should be none on create), safer for duplicates
            $appointment->services()->sync($serviceIds, false);
        }

        $appointment->load(['services', 'contact']);

        return response()->json(new AppointmentResource($appointment), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $appointment = Appointment::with(['services', 'contact'])->findOrFail($id);

        return response()->json(new AppointmentResource($appointment));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:200'],
            'type_id' => ['sometimes', 'nullable', 'string', 'size:10', 'exists:appointment_types,id'],
            'contact_id' => ['sometimes', 'nullable', 'string', 'size:10', 'exists:contacts,id'],
            'staff_id' => ['sometimes', 'nullable', 'string', 'size:10', 'exists:staff,id'],
            'start' => ['sometimes', 'integer'],
            'end' => ['sometimes', 'integer'],
            'start_time' => ['sometimes', 'integer'],
            'end_time' => ['sometimes', 'integer'],
            'service_ids' => ['sometimes', 'array'],
            'service_ids.*' => ['string', 'size:10', 'exists:services,id'],
        ]);

        // Map start/end from React to start_time/end_time if provided
        $payload = [];
        foreach (['title','type_id','contact_id','staff_id','start_time','end_time'] as $field) {
            if (array_key_exists($field, $validated)) {
                $payload[$field] = $validated[$field];
            }
        }
        if (array_key_exists('start', $validated)) {
            $payload['start_time'] = $validated['start'];
        }
        if (array_key_exists('end', $validated)) {
            $payload['end_time'] = $validated['end'];
        }

        $appointment->update($payload);

        // Update services if provided (snake_case only)
        if (array_key_exists('service_ids', $validated)) {
            $serviceIds = $validated['service_ids'] ?? [];
            if (is_string($serviceIds)) { // safety if client sends string
                $serviceIds = array_filter(array_map('trim', preg_split('/[\s,]+/', $serviceIds)));
            }
            $appointment->services()->sync($serviceIds ?? []);
        }

        $appointment->load(['services']);

        return response()->json(new AppointmentResource($appointment));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return response()->json([], 204);
    }
}

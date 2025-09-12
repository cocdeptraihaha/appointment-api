<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Resources\ContactResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Contact::withCount('appointments');

        // Search by first/last name, email, or phone number
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        // Return limited results (5 items max)
        $contacts = $query->orderBy('first_name')->orderBy('last_name')->limit(5)->get();

        return response()->json(ContactResource::collection($contacts));
    }

    /**
     * Paginated listing of contacts (25 per page).
     */
    public function paginated(Request $request): JsonResponse
    {
        $query = Contact::withCount('appointments');

        // Search by first/last name, email, or phone number
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        // Fixed page size: 25 results per page
        $contacts = $query->orderBy('first_name')->orderBy('last_name')->paginate(25);

        // Return paginator with meta & links preserved
        return ContactResource::collection($contacts)->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'string', 'max:200', 'email'],
            'phone_number' => ['required', 'regex:/^\+?[1-9][0-9]{7,14}$/'],
            'avatar' => ['nullable', 'string'],
        ]);

        $contact = Contact::create([
            'id' => Str::random(10),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'] ?? null,
            'phone_number' => $validated['phone_number'] ?? null,
            'avatar' => $validated['avatar'] ?? null,
        ]);

        return response()->json(new ContactResource($contact), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $contact = Contact::with(['appointments.appointment_type', 'appointments.staff', 'appointments.services'])
            ->findOrFail($id);

        return response()->json(new ContactResource($contact));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $contact = Contact::findOrFail($id);

        $validated = $request->validate([
            'first_name' => ['sometimes', 'required', 'string', 'max:100'],
            'last_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'email' => ['sometimes', 'nullable', 'string', 'max:200', 'email'],
            'phone_number' => ['required', 'regex:/^\+?[1-9][0-9]{7,14}$/'],
            'avatar' => ['sometimes', 'nullable', 'string'],
        ]);

        $contact->update($validated);

        return response()->json(new ContactResource($contact));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $contact = Contact::findOrFail($id);
        
        // Check if contact has appointments
        if ($contact->appointments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete contact with existing appointments'
            ], 422);
        }

        $contact->delete();

        return response()->json([], 204);
    }
}

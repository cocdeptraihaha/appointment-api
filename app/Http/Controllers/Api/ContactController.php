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

        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $contacts = $query->orderBy('name')->get();

        return response()->json(ContactResource::collection($contacts));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $contact = Contact::create([
            'id' => Str::random(10),
            'name' => $request->name,
            'avatar' => $request->avatar,
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

        $contact->update($request->only(['name', 'avatar']));

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

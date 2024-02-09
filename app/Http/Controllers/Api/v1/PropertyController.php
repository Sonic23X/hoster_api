<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Properties\AddUserToPropertyRequest;
use App\Http\Requests\Properties\CreatePropertyRequest;
use App\Http\Requests\Properties\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersComboResource;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyService;
use App\Models\PropertyStockholder;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $properties = Property::with(['owner', 'images', 'services'])->get();

        return response()->json([
            'properties' => PropertyResource::collection(($properties))
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreatePropertyRequest $request): JsonResponse
    {
        $data = $request->except(['images', 'services']);
        $owner = User::where('uuid', $request->owner_id)->first();
        $data['owner_id'] = $owner->id;
        $property = Property::create($data);

        $services = $request->services;

        foreach ($services as $serviceRequest) {
            $service = Service::where('uuid', $serviceRequest)->first();

            if ($service == null) {
                continue;
            }

            PropertyService::create([
                'property_id' => $property->id,
                'service_id' => $service->id
            ]);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = $image->getClientOriginalName();
                $image->storeAs('property_images/' . $property->uuid, $imageName, 'public');

                PropertyImage::create([
                    'property_id' => $property->id,
                    'name' => $imageName,
                    'storage' => 'property_images/' . $property->uuid . '/' . $imageName,
                ]);
            }
        }

        return response()->json(['message' => 'Property created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid): JsonResponse
    {
        $property = Property::where('uuid', $uuid)
                        ->with(['owner', 'images', 'services'])->first();

        if ($property == null) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        return response()->json([
            'property' => new PropertyResource($property)
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePropertyRequest $request, string $uuid)
    {
        $property = Property::where('uuid', $uuid)
                        ->with(['owner', 'images', 'services'])->first();

        if ($property == null) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        $data = $request->except(['images', 'services']);
        $property->update($data);

        if ($request->services != null) {
            PropertyService::where('property_id', $property->id)->delete();
            $services = $request->services;

            foreach ($services as $serviceRequest) {
                $service = Service::where('uuid', $serviceRequest)->first();

                if ($service == null) {
                    continue;
                }

                PropertyService::create([
                    'property_id' => $property->id,
                    'service_id' => $service->id
                ]);
            }
        }

        if ($request->hasFile('images')) {
            $dir = 'public/property_images/' . $property->uuid;

            if (Storage::exists($dir)) {
                Storage::deleteDirectory($dir);
            }

            $property->images()->delete();

            foreach ($request->file('images') as $image) {
                $imageName = $image->getClientOriginalName();
                $image->storeAs('property_images/' . $property->uuid, $imageName, 'public');

                PropertyImage::create([
                    'property_id' => $property->id,
                    'name' => $imageName,
                    'storage' => 'property_images/' . $property->uuid . '/' . $imageName,
                ]);
            }
        }

        return response()->json(['message' => 'Property updated successfully'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid): JsonResponse
    {
        $property = Property::where('uuid', $uuid)->first();

        if ($property == null) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        $dir = 'public/property_images/' . $property->uuid;

        if (Storage::exists($dir)) {
            Storage::deleteDirectory($dir);
        }

        $property->images()->delete();

        PropertyService::where('property_id', $property->id)->delete();

        $property->delete();

        return response()->json(['message' => 'Property deleted successfully'], 200);
    }

    public function getUsers(string $uuid): JsonResponse
    {
        $property = Property::where('uuid', $uuid)->first();

        if ($property == null) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        $stockholders = PropertyStockholder::where('property_id', $property->id)
                    ->with('user')->get();

        $users = $stockholders->map(function ($stockholder) {
            return $stockholder->user;
        });

        return response()->json([
            'users' => UserResource::collection($users)
        ], 200);
    }

    public function getAvailableUsers(string $uuid): JsonResponse {
        $property = Property::where('uuid', $uuid)->first();

        if ($property == null) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        $stockholders = PropertyStockholder::where('property_id', $property->id)
                    ->with('user')->get();

        $previusUsers = $stockholders->map(function ($stockholder) {
            return $stockholder->user->id;
        });

        $users = null;
        if (Auth::user()->hasRole('superadmin')) {
            $users = User::whereNotIn('id', $previusUsers)->get();
        } else if (Auth::user()->hasRole('partner')) {
            $users = User::whereNotIn('id', $previusUsers)
                    ->where('user_by_id', Auth::user()->id)->get();
        }

        $users = $users->filter(function ($user) use ($property) {
            return $user->id != $property->owner_id;
        });

        return response()->json([
            'users' => UsersComboResource::collection($users)
        ], 200);
    }

    public function addUser(AddUserToPropertyRequest $request, string $uuid): JsonResponse
    {
        $property = Property::where('uuid', $uuid)->first();

        if ($property == null) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        $user = User::where('uuid', $request->user_id)->first();

        if ($user == null) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        if (PropertyStockholder::where('property_id', $property->id)
                ->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'The property is already assigned to the user'], 400);
        }

        $validation = PropertyStockholder::where('property_id', $property->id)->get();
        if ($validation->count() >= 8) {
            return response()->json(['message' => 'The property has already 8 stockholders'], 400);
        }

        PropertyStockholder::create([
            'property_id' => $property->id,
            'user_id' => $user->id,
        ]);

        return response()->json(['message' => 'Property assigned successfully'], 200);
    }

    public function removeUser(string $uuid, string $userUuid): JsonResponse
    {
        $property = Property::where('uuid', $uuid)->first();

        if ($property == null) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        $user = User::where('uuid', $userUuid)->first();

        if ($user == null) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $stockholder = PropertyStockholder::where('property_id', $property->id)
                    ->where('user_id', $user->id)->first();

        if ($stockholder == null) {
            return response()->json([
                'message' => 'User not found in property'
            ], 404);
        }

        $stockholder->delete();

        return response()->json(['message' => 'User removed from property successfully'], 200);
    }
}

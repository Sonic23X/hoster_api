<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Properties\CreatePropertyRequest;
use App\Http\Requests\Properties\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\PropertyService;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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
                    'storage' => 'public/property_images/' . $property->uuid . '/' . $imageName,
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

    function myProperties(): JsonResponse
    {
        $properties = collect([]);
        if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('partner')) {
            $properties = Auth::user()->properties()->with(['owner', 'images', 'services'])->get();
        }

        if (Auth::user()->hasRole('user')) {
            $properties = Auth::user()->properties()->with(['owner', 'images', 'services'])->get();
            $stockholderProperties = Property::whereHas('stockholders', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })->with(['owner', 'images', 'services'])->get();

            $properties = $properties->merge($stockholderProperties);
        }


        return response()->json([
            'properties' => PropertyResource::collection(($properties))
        ], 200);
    }
}

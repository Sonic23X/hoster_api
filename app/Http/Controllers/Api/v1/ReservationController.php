<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservations\CreateReservationRequest;
use App\Http\Resources\ListReservationResource;
use App\Http\Resources\ReservationResource;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getReservations(string $uuid)
    {
        $property = Property::where('uuid', $uuid)->first();

        if ($property == null) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        $reservations = Reservation::where('property_id', $property->id)
                            ->where('check_out', '>=', date('Y-m-d'))
                            ->orderBy('check_in', 'asc')
                            ->get();

        return response()->json([
            'reservations' => ListReservationResource::collection($reservations),
            'property' => $property->title,
        ], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateReservationRequest $request)
    {
        $property = Property::where('uuid', $request->property_id)->first();

        $user = Auth::user();

        $today = date('Y-m-d');
        $today = date('Y-m-d', strtotime($today . ' + 1 day'));
        if ($request->check_in <= $today || $request->check_out < $today) {
            return response()->json([
                'message' => 'Check in and/or check out dates are not valid'
            ], 400);
        }

        if ($request->check_in >= $request->check_out) {
            return response()->json([
                'message' => 'Check in date must be less than check out date'
            ], 400);
        }

        $check_in = $request->check_in;

        $exitsReservation = DB::select(
            "SELECT * FROM reservations WHERE ('".$check_in."' BETWEEN check_in AND check_out) AND property_id = ".$property->id." AND status = 'activo'"
        );

        if (count($exitsReservation) > 0) {
            return response()->json([
                'message' => 'The property is not available for the selected dates'
            ], 400);
        }

        // Reglas hoster
        if (true) {
            $previusReservations = Reservation::where('user_id', $user->id)
                ->where('status', 'activo')
                ->where('is_free', false)
                ->count();

            if ($previusReservations > 6) {
                return response()->json([
                    'message' => 'You passed the limit of reservations'
                ], 400);
            }
        }

        $is_free = false;
        $validateDate = date('Y-m-d', strtotime($today . ' + 28 day'));
        if ($request->check_in <= $validateDate) {
            $is_free = true;
        }

        $data = [
            'uuid' => Str::uuid(),
            'user_id' => $user->id,
            'property_id' => $property->id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'confirmation_code' => Str::random(6),
            'status' => 'activo',
            'is_free' => $is_free,
        ];

        $reservation = Reservation::create($data);

        return response()->json([
            'message' => 'Reservation created successfully',
            'reservation' => $reservation->uuid,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid): JsonResponse
    {
        $reservation = Reservation::where('uuid', $uuid)->with('property')->first();

        if ($reservation == null) {
            return response()->json([
                'message' => 'Reservation not found'
            ], 404);
        }

        return response()->json([
            'reservation' => new ReservationResource($reservation),
        ], 200);

    }

    function getMyReservations(): JsonResponse {
        $user = Auth::user();

        $reservations = Reservation::where('user_id', $user->id)
            ->orderBy('check_in', 'desc')
            ->get();

        return response()->json([
            'reservations' => ReservationResource::collection($reservations),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $reservation = Reservation::where('uuid', $uuid)->first();

        if ($reservation == null) {
            return response()->json([
                'message' => 'Reservation not found'
            ], 404);
        }

        $reservation->status = 'cancelado';
        $reservation->cancel_date = date('Y-m-d H:i:s');
        $reservation->save();

        return response()->json([
            'message' => 'Reservation canceled successfully',
        ], 200);
    }
}

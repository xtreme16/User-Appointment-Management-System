<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    // 🔹 Lihat daftar appointment milik user login
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Not logged in'], 401);
        }

        $appointments = Appointment::with('users')
            ->where('creator_id', $user->id)
            ->orWhereHas('users', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->orderBy('start', 'asc')
            ->get()
            ->map(function ($appointment) use ($user) {
                $tz = $user->preferred_timezone ?? 'Asia/Jakarta';

                // Parse as UTC (DB) then convert to user's timezone
                $start = Carbon::parse($appointment->start, 'UTC')->setTimezone($tz);
                $end   = Carbon::parse($appointment->end, 'UTC')->setTimezone($tz);

                // overwrite fields or return formatted values
                $appointment->start = $start->format('Y-m-d H:i:s');
                $appointment->end   = $end->format('Y-m-d H:i:s');
                
                    return $appointment;
                });
                
        return response()->json($appointments);
    }

    // 🔹 Membuat appointment baru + undang user lain
    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Not logged in'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
            'users' => 'array', // daftar user_id
            'users.*' => 'exists:users,id',
        ]);

        $tz = $user->preferred_timezone ?? 'Asia/Jakarta';

        try {
            $startLocal = Carbon::parse($validated['start'], $tz);
            $endLocal   = Carbon::parse($validated['end'], $tz);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid datetime format'], 422);
        }

        $minAllowed = $startLocal->copy()->setTime(8, 0, 0);
        $maxAllowed = $startLocal->copy()->setTime(17, 0, 0);

        if ($startLocal->lt($minAllowed) || $endLocal->gt($maxAllowed)) {
            return response()->json([
                'message' => 'Appointments are allowed only between 08:00 and 17:00.'
            ], 422);
        }

        $startUtc = $startLocal->copy()->setTimezone('UTC');
        $endUtc   = $endLocal->copy()->setTimezone('UTC');

        $appointment = Appointment::create([
            'title' => $validated['title'],
            'creator_id' => $user->id,
            'start' => $startUtc,
            'end' => $endUtc,
        ]);

        $appointment->users()->attach($user->id);

        if (!empty($validated['users'])) {
            $appointment->users()->attach($validated['users']);
        }

        return response()->json([
            'message' => 'Appointment created successfully',
            'appointment' => $appointment->load('users')
        ], 201);
    }
}

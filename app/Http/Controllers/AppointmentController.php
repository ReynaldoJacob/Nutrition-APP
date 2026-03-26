<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {        $request->validate([
            'patient_id'  => 'required|integer|exists:users,id',
            'date'        => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'type'        => 'required|in:initial,follow_up,emergency,online',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $scheduledAt = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->start_time);
        $endAt       = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->end_time);
        $duration    = (int) $scheduledAt->diffInMinutes($endAt);

        Appointment::create([
            'patient_id'      => $request->patient_id,
            'nutritionist_id' => $request->user()->id,
            'scheduled_at'    => $scheduledAt,
            'duration'        => $duration,
            'type'            => $request->type,
            'status'          => 'scheduled',
            'notes'           => $request->notes,
        ]);

        return redirect()->back();
    }

    public function cancel(Request $request, Appointment $appointment)
    {
        // Solo el nutriólogo dueño puede cancelarla
        abort_if($appointment->nutritionist_id !== $request->user()->id, 403);

        // Evitar cancelar una cita que ya está cancelada
        if ($appointment->status === 'cancelled') {
            return redirect()->back()->withErrors(['status' => 'Esta cita ya fue cancelada previamente.']);
        }

        $request->validate([
            'cancelled_reason' => 'nullable|string|max:255',
        ]);

        $appointment->update([
            'status'           => 'cancelled',
            'cancelled_reason' => $request->cancelled_reason,
            'cancelled_by_id'  => $request->user()->id,
        ]);

        return redirect()->back();
    }
}

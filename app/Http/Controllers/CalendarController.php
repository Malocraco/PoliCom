<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        // Por defecto, semana actual
        $date = $request->input('date') ? Carbon::parse($request->input('date')) : Carbon::now();
        $startOfWeek = $date->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $date->copy()->endOfWeek(Carbon::SUNDAY);

        $solicitudes = Solicitud::whereBetween('fecha_requerida', [
                $startOfWeek->toDateString(), 
                $endOfWeek->toDateString()
            ])
            ->get();

        return Inertia::render('Calendar/Index', [
            'solicitudes' => $solicitudes,
            'startOfWeek' => $startOfWeek->toDateString(),
            'endOfWeek' => $endOfWeek->toDateString(),
            'currentDate' => $date->toDateString()
        ]);
    }

    public function updateDate(Request $request, $id)
    {
        $request->validate([
            'fecha_requerida' => 'required|date',
        ]);

        $solicitud = Solicitud::findOrFail($id);
        $solicitud->update([
            'fecha_requerida' => $request->fecha_requerida
        ]);

        return back()->with('success', 'Calendario actualizado');
    }
}

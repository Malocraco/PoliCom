<?php

namespace App\Http\Controllers;

use App\Models\ContactoGeneral;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ContactoController extends Controller
{
    public function index()
    {
        $contactos = ContactoGeneral::orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Contactos/Index', [
            'contactos' => $contactos
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $path = $request->file('archivo')->getRealPath();
        $data = array_map('str_getcsv', file($path));
        $header = array_shift($data);

        foreach ($data as $row) {
            if (count($row) < 3) continue;
            
            ContactoGeneral::updateOrCreate(
                ['email' => $row[1]], // Asumimos email en col 2
                [
                    'nombre' => $row[0],
                    'telefono' => $row[2] ?? null,
                    'segmento' => $row[3] ?? 'General',
                    'metadata' => [
                        'importado_el' => now()->toDateString()
                    ]
                ]
            );
        }

        return redirect()->route('contactos.index')->with('success', 'Base de datos actualizada');
    }
}

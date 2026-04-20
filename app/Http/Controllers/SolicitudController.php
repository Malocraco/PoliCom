<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Services\InfobipService;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::with('analista')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Solicitudes/Index', [
            'solicitudes' => $solicitudes
        ]);
    }

    public function create()
    {
        return Inertia::render('Solicitudes/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'area_solicitante' => 'required|string',
            'cliente_nombre' => 'required|string',
            'cliente_email' => 'required|email',
            'com_id' => 'required|string',
            'nombre_envio' => 'required|string|max:255',
            'tipo' => 'required|in:Email,SMS',
            'tipo_target' => 'required|string',
            'detalle_target' => 'nullable|string',
            'target' => 'nullable|string',
            'tiene_evento_noticia' => 'required|boolean',
            'fecha_requerida' => 'required|date|after_or_equal:today',
            'hora_programada' => 'required|string',
            'subject' => 'required_if:tipo,Email|nullable|string',
            'mask' => 'required|string',
            'sms_copy' => 'required_if:tipo,SMS|nullable|string',
            'pieza_creativa' => 'required|file|mimes:html,jpg,jpeg,png|max:5120',
            'base_datos' => 'nullable|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        // Validación extra para la hora si el día es hoy
        if ($request->fecha_requerida === date('Y-m-d')) {
            $currentTime = date('H:i');
            if ($request->hora_programada <= $currentTime) {
                return back()->withErrors(['hora_programada' => 'La hora programada debe ser mayor a la hora actual.'])->withInput();
            }
        }

        $solicitud = new Solicitud($request->all());
        $solicitud->numero_solicitud = $this->generateSolicitudNumber();
        $solicitud->analista_id = Auth::id();
        $solicitud->estado = 'creada';

        if ($request->hasFile('pieza_creativa')) {
            $file = $request->file('pieza_creativa');
            $solicitud->pieza_creativa_path = $file->store('piezas', 'public');
            $solicitud->pieza_creativa_nombre = $file->getClientOriginalName();
        }

        if ($request->hasFile('base_datos')) {
            $file = $request->file('base_datos');
            $solicitud->base_datos_path = $file->store('bases', 'public');
            $solicitud->base_datos_nombre = $file->getClientOriginalName();
        }

        $solicitud->save();

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud creada con éxito');
    }

    public function show($id)
    {
        $solicitud = Solicitud::with('analista')->findOrFail($id);
        return Inertia::render('Solicitudes/Show', [
            'solicitud' => $solicitud
        ]);
    }

    public function sendTest(Request $request, Solicitud $solicitud, InfobipService $infobip)
    {
        $request->validate([
            'email_prueba' => 'required|string'
        ]);

        $recipient = $request->email_prueba;
        $result = null;

        // Preparar contenido
        $content = $solicitud->pieza_creativa_path; // Ruta por defecto para envolver
        if ($solicitud->tipo === 'Email') {
            if ($solicitud->pieza_creativa_path && Storage::disk('public')->exists($solicitud->pieza_creativa_path)) {
                $ext = pathinfo($solicitud->pieza_creativa_path, PATHINFO_EXTENSION);
                if (strtolower($ext) === 'html') {
                    $content = Storage::disk('public')->get($solicitud->pieza_creativa_path);
                }
            }

            $result = $infobip->sendEmail(
                $recipient,
                "[PRUEBA] " . $solicitud->subject,
                $content,
                $solicitud->mask,
                'politecnico@correo.poligran.edu.co',
                'gato@poligran.edu.co'
            );
        } else {
            $result = $infobip->sendSms(
                $recipient,
                "PRUEBA POLICOM: {$solicitud->sms_copy}"
            );
        }

        if ($result) {
            $solicitud->update(['estado' => 'prueba_enviada']);
            return back()->with('success', 'Prueba enviada con éxito');
        }

        return back()->with('error', 'Error al enviar la prueba. Revisa los logs.');
    }

    public function prepareForInfobip(Solicitud $solicitud, InfobipService $infobip)
    {
        // Preparar contenido
        $content = $solicitud->pieza_creativa_path;
        if ($solicitud->pieza_creativa_path && Storage::disk('public')->exists($solicitud->pieza_creativa_path)) {
            $ext = pathinfo($solicitud->pieza_creativa_path, PATHINFO_EXTENSION);
            if (strtolower($ext) === 'html') {
                $content = Storage::disk('public')->get($solicitud->pieza_creativa_path);
            }
        }

        // Crear plantilla en Infobip
        $templateName = "PLANTILLA_" . $solicitud->numero_solicitud;
        $result = $infobip->createEmailTemplate(
            $templateName,
            $solicitud->subject,
            $content,
            $solicitud->mask,
            'politecnico@correo.poligran.edu.co',
            'gato@poligran.edu.co'
        );

        if ($result) {
            $solicitud->update(['estado' => 'borrador_infobip']);
            return back()->with('success', 'Plantilla creada con éxito en Infobip. Busca "' . $templateName . '" en tus Plantillas.');
        }

        return back()->with('error', 'No se pudo crear la plantilla en Infobip. Revisa los logs para más detalles.');
    }

    public function launchCampaign(Solicitud $solicitud, InfobipService $infobip)
    {
        if (!in_array($solicitud->estado, ['prueba_enviada', 'lanzada'])) {
            return back()->with('error', 'Debes enviar una prueba antes de lanzar la campaña.');
        }

        if ($solicitud->tipo === 'Email') {
            // Leer base de datos
            $filePath = storage_path('app/public/' . $solicitud->base_datos_path);
            if (!file_exists($filePath)) {
                return back()->with('error', 'No se encontró el archivo de la base de datos.');
            }

            try {
                $spreadsheet = IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
                $headers = array_shift($rows);

                // Buscar columna de email
                $emailColIndex = -1;
                foreach ($headers as $index => $header) {
                    if (!$header) continue;
                    $h = strtolower($header);
                    if (str_contains($h, 'email') || str_contains($h, 'correo') || str_contains($h, 'contacto')) {
                        $emailColIndex = $index;
                        break;
                    }
                }

                if ($emailColIndex === -1) {
                    return back()->with('error', 'No se encontró una columna de Email/Correo en el archivo.');
                }

                if (count($rows) === 0) {
                    return back()->with('error', 'La base de datos está vacía.');
                }

                // Obtener el primer contacto (según acuerdo con el usuario por cuenta free)
                $firstContact = $rows[0][$emailColIndex];
                
                if (empty($firstContact)) {
                    return back()->with('error', 'El primer registro de email está vacío.');
                }

                // Preparar contenido
                $content = $solicitud->pieza_creativa_path;
                if (Storage::disk('public')->exists($solicitud->pieza_creativa_path)) {
                    $ext = pathinfo($solicitud->pieza_creativa_path, PATHINFO_EXTENSION);
                    if (strtolower($ext) === 'html') {
                        $content = Storage::disk('public')->get($solicitud->pieza_creativa_path);
                    }
                }

                // Enviar
                $result = $infobip->sendEmail(
                    $firstContact,
                    $solicitud->subject,
                    $content,
                    $solicitud->mask,
                    'politecnico@correo.poligran.edu.co',
                    'gato@poligran.edu.co'
                );

                if ($result) {
                    $solicitud->update(['estado' => 'lanzada']);
                    return back()->with('success', 'Envío masivo completado (1 registro procesado por plan Free). Destino: ' . $firstContact);
                }
            } catch (\Exception $e) {
                Log::error('Error procesando Excel: ' . $e->getMessage());
                return back()->with('error', 'Error al leer el archivo Excel.');
            }
        } else {
            // Lógica similar para SMS si fuera necesario
            $solicitud->update(['estado' => 'lanzada', 'finalizada_at' => now()]);
            return back()->with('success', 'Campaña SMS lanzada.');
        }

        return back()->with('error', 'Ocurrió un error al lanzar la campaña.');
    }

    private function generateSolicitudNumber()
    {
        $prefix = 'SOL-' . date('Ym');
        $lastSolicitud = Solicitud::where('numero_solicitud', 'like', $prefix . '%')
            ->orderBy('numero_solicitud', 'desc')
            ->first();

        if (!$lastSolicitud) {
            return $prefix . '-0001';
        }

        $lastNumber = intval(substr($lastSolicitud->numero_solicitud, -4));
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . '-' . $newNumber;
    }

    public function downloadFile($id, $type)
    {
        $solicitud = Solicitud::findOrFail($id);
        $path = $type === 'pieza' ? $solicitud->pieza_creativa_path : $solicitud->base_datos_path;
        $name = $type === 'pieza' ? $solicitud->pieza_creativa_nombre : $solicitud->base_datos_nombre;

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        return Storage::disk('public')->download($path, $name);
    }

    public function updateBase(Request $request, $id)
    {
        $solicitud = Solicitud::findOrFail($id);
        
        $request->validate([
            'base_datos' => 'required|file|mimes:csv,xlsx,xls|max:10240',
        ]);

        if ($solicitud->base_datos_path) {
            Storage::disk('public')->delete($solicitud->base_datos_path);
        }

        $file = $request->file('base_datos');
        $solicitud->base_datos_path = $file->store('bases', 'public');
        $solicitud->base_datos_nombre = $file->getClientOriginalName();
        $solicitud->save();

        return back();
    }
}

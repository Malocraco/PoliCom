<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SolicitudController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $now = now();
    $startOfWeek = $now->copy()->startOfWeek();

    $solicitudesCreadasSemana = \App\Models\Solicitud::where('created_at', '>=', $startOfWeek)->count();
    $campanasLanzadas = \App\Models\Solicitud::where('estado', 'lanzada')->count();
    $pendientesPrueba = \App\Models\Solicitud::where('estado', 'creada')->count(); // Recien creadas, requieren prueba
    
    $recentRequests = \App\Models\Solicitud::orderBy('created_at', 'desc')->take(5)->get()->map(function ($sol) {
        $statusBg = 'bg-[#e2e2e6]';
        $statusText = 'text-[#191c1e]';
        $statusLabel = ucfirst($sol->estado);

        switch ($sol->estado) {
            case 'creada':
                $statusBg = 'bg-[#e2e2e6]';
                $statusText = 'text-[#191c1e]';
                $statusLabel = 'Pendiente Prueba';
                break;
            case 'prueba_enviada':
                $statusBg = 'bg-[#ffdcc5]';
                $statusText = 'text-[#301400]';
                $statusLabel = 'Prueba Enviada';
                break;
            case 'borrador_infobip':
                $statusBg = 'bg-[#ffdad6]';
                $statusText = 'text-[#93000a]';
                $statusLabel = 'Borrador Infobip';
                break;
            case 'lanzada':
                $statusBg = 'bg-[#a3f69c]';
                $statusText = 'text-[#005312]';
                $statusLabel = 'Lanzada';
                break;
        }

        return [
            'id' => $sol->numero_solicitud,
            'title' => $sol->nombre_envio,
            'subtitle' => $sol->area_solicitante ?? 'Sin Área',
            'date' => $sol->created_at->format('d M Y'),
            'status' => $statusLabel,
            'statusBg' => $statusBg,
            'statusText' => $statusText,
        ];
    });

    return Inertia::render('Dashboard', [
        'solicitudesCreadasSemana' => $solicitudesCreadasSemana,
        'campanasLanzadas' => $campanasLanzadas,
        'pendientesPrueba' => $pendientesPrueba,
        'recentRequests' => $recentRequests,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('solicitudes', SolicitudController::class);
    Route::post('/solicitudes/{solicitud}/test', [SolicitudController::class, 'sendTest'])->name('solicitudes.send-test');
    Route::post('/solicitudes/{solicitud}/prepare', [SolicitudController::class, 'prepareForInfobip'])->name('solicitudes.prepare');
    Route::post('/solicitudes/{solicitud}/launch', [SolicitudController::class, 'launchCampaign'])->name('solicitudes.launch');
    Route::get('/solicitudes/{solicitud}/download/{type}', [SolicitudController::class, 'downloadFile'])->name('solicitudes.download');
    Route::post('/solicitudes/{solicitud}/update-base', [SolicitudController::class, 'updateBase'])->name('solicitudes.update-base');

    Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    Route::patch('/calendar/{id}/move', [\App\Http\Controllers\CalendarController::class, 'updateDate'])->name('calendar.move');

    Route::get('/contactos', [\App\Http\Controllers\ContactoController::class, 'index'])->name('contactos.index');
    Route::post('/contactos/upload', [\App\Http\Controllers\ContactoController::class, 'upload'])->name('contactos.upload');
});

require __DIR__.'/auth.php';

Route::get('/test-api', [\App\Http\Controllers\TestController::class, 'testInfobip'])->middleware(['auth']);

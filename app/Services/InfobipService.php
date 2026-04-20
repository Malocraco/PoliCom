<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InfobipService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.infobip.base_url');
        $this->apiKey = config('services.infobip.api_key');
    }

    /**
     * Enviar SMS Masivo
     */
    public function sendSms($to, $message, $from = 'Poli')
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/sms/2/text/advanced', [
                'messages' => [
                    [
                        'from' => $from,
                        'destinations' => [['to' => $to]],
                        'text' => $message,
                    ]
                ]
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error enviando SMS Infobip: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Enviar Email Masivo / Prueba
     */
    public function sendEmail($to, $subject, $content, $fromName = 'Politécnico Grancolombiano', $fromEmail = 'noreply@infobip.com', $replyTo = 'comunicaciones@poligran.edu.co')
    {
        try {
            // Si el contenido no parece HTML, asumimos que puede ser una ruta de imagen y la envolvemos
            if (!str_contains($content, '<html') && !str_contains($content, '<div')) {
                // Si parece una ruta de archivo (contiene .jpg, .png), generamos el HTML
                if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $content)) {
                    $content = $this->wrapImageInHtml($content);
                }
            }

            $response = Http::withHeaders([
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/email/3/send', [
                'from' => "{$fromName} <{$fromEmail}>",
                'to' => $to,
                'subject' => $subject,
                'html' => $content,
                'replyTo' => $replyTo
            ]);

            if (!$response->successful()) {
                Log::error('Error API Infobip Email: ' . $response->body());
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error enviando Email Infobip: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Crear una Plantilla en Infobip (Email Template)
     */
    public function createEmailTemplate($name, $subject, $content, $fromName = 'Politécnico Grancolombiano', $fromEmail = 'noreply@infobip.com', $replyTo = 'comunicaciones@poligran.edu.co')
    {
        try {
            // Envolver imagen si es necesario
            if (!str_contains($content, '<html') && !str_contains($content, '<div')) {
                if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $content)) {
                    $content = $this->wrapImageInHtml($content);
                }
            }

            // Simplificamos el FROM para probar si es el formato lo que falla
            // Pasamos solo el email verificado
            $fromPayload = $fromEmail;

            Log::info("Intentando crear plantilla Infobip - From: {$fromPayload}, Name: {$name}");

            $response = Http::withHeaders([
                'Authorization' => 'App ' . $this->apiKey,
                'Accept' => 'application/json',
            ])->asMultipart()->post($this->baseUrl . '/email/1/templates', [
                [
                    'name' => 'name',
                    'contents' => $name,
                ],
                [
                    'name' => 'from',
                    'contents' => $fromPayload,
                ],
                [
                    'name' => 'subject',
                    'contents' => $subject,
                ],
                [
                    'name' => 'html',
                    'contents' => $content,
                ],
                [
                    'name' => 'replyTo',
                    'contents' => $replyTo,
                ]
            ]);

            if (!$response->successful()) {
                Log::error('Error creando Plantilla Infobip. Status: ' . $response->status() . ' - Body: ' . $response->body());
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Excepción creando Plantilla Infobip: ' . $e.getMessage());
            return null;
        }
    }

    /**
     * Envuelve una imagen en un template HTML básico centrado
     */
    private function wrapImageInHtml($imagePath)
    {
        // En un entorno real, la imagen debería ser una URL pública
        // Como estamos en local, Infobip no podrá acceder a la imagen si solo pasamos el path
        // Por ahora generamos una estructura básica. 
        // TIP: Si el usuario quiere que se vea, la imagen debe estar en un servidor público.
        $url = asset('storage/' . $imagePath);
        
        return "
            <div style='background-color: #f4f4f4; padding: 20px; font-family: Arial, sans-serif;'>
                <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px; border-radius: 8px; text-align: center;'>
                    <img src='{$url}' alt='Imagen de Campaña' style='max-width: 100%; height: auto; border-radius: 4px;'>
                </div>
                <div style='text-align: center; margin-top: 20px; color: #888888; font-size: 12px;'>
                    Enviado desde el sistema de Comunicaciones - Politécnico Grancolombiano
                </div>
            </div>
        ";
    }
}

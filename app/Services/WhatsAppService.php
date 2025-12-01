<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $defaultGroupId;

    public function __construct()
    {
        // URL del bot local (Node.js)
        $this->apiUrl = 'http://localhost:3000/send-message';

        // ID del grupo por defecto (Continental League)
        $this->defaultGroupId = '120363405100869653@g.us';
    }

    /**
     * Envía un mensaje de WhatsApp a través del bot local.
     *
     * @param string $message El mensaje a enviar.
     * @param string|null $number El ID del destinatario (grupo o usuario). Si es null, usa el grupo por defecto.
     * @return bool True si se envió correctamente, False en caso contrario.
     */
    public function sendMessage($message, $number = null)
    {
        $targetNumber = $number ?? $this->defaultGroupId;

        try {
            $response = Http::post($this->apiUrl, [
                'number' => $targetNumber,
                'message' => $message,
            ]);

            if ($response->successful()) {
                // Log::info("WhatsApp enviado a {$targetNumber}: {$message}");
                return true;
            } else {
                // Log::error("Error al enviar WhatsApp: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            // Log::error("Excepción al enviar WhatsApp: " . $e->getMessage());
            return false;
        }
    }
}

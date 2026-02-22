<?php

namespace Tickets;

use Flight;

class CorsUtil
{
    private array $allowedOrigins;

    public function __construct()
    {
        // Define as origens permitidas (incluindo o seu frontend)
        $this->allowedOrigins = [
            'http://localhost:5173', // O seu frontend VITE
            // Adicione outras origens de frontend aqui se necessário
        ];
    }

    public function handleCors(): void
    {
        $request = Flight::request();
        $response = Flight::response();
        $origin = $request->getVar('HTTP_ORIGIN'); // Obtém o header Origin

        if (!empty($origin)) {
            $this->setOriginHeader($origin, $response);
            $response->header('Access-Control-Allow-Credentials', 'true');
            $response->header('Access-Control-Max-Age', '86400'); 
        }

        if ($request->method === 'OPTIONS') {
            
            $response->header(
                'Access-Control-Allow-Methods', 
                'GET, POST, PUT, DELETE, PATCH, OPTIONS'
            );
            
            if (!empty($request->getVar('HTTP_ACCESS_CONTROL_REQUEST_HEADERS'))) {
                $response->header(
                    "Access-Control-Allow-Headers",
                    $request->getVar('HTTP_ACCESS_CONTROL_REQUEST_HEADERS')
                );
            }

            $response->status(204);
            $response->send();
            exit; 
        }
    }

    private function setOriginHeader(string $origin, $response): void
    {
        // Se a origem estiver na lista de permitidas, define o header
        if (in_array($origin, $this->allowedOrigins, true)) {
            $response->header("Access-Control-Allow-Origin", $origin);
        }
    }
}

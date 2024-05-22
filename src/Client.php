<?php

namespace Saulmoralespa\ServientregaEcuador;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Utils;

class Client
{

    const SANDBOX_PORT_GUIDES = "8021";
    const PORT_GUIDES = "5052";
    const SANDBOX_PORT_PRINT = "7777";
    const PORT_PRINT = "5001";
    const SANDBOX_URL_BASE = "https://181.39.87.158";
    const URL_BASE = "https://swservicli.servientrega.com.ec";
    protected static bool $sandbox = false;

    public function __construct(
        private string $user,
        private string $password
    )
    {

    }

    public function sandboxMode($status = false):void
    {
        self::$sandbox = $status;
    }

    public function getBaseUrl(): string
    {
        $url = self::URL_BASE;

        if(self::$sandbox)
            $url = self::SANDBOX_URL_BASE;
        return $url;
    }

    private function client(): GuzzleClient
    {
        return new GuzzleClient([
            //'base_uri' => $this->getBaseUrl(),
            'verify' => !self::$sandbox
        ]);
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function getCities(): array
    {
        try {
            $params = sprintf('["%s", "%s"]', $this->user, $this->password);
            $port = self::$sandbox ? self::SANDBOX_PORT_GUIDES : self::PORT_GUIDES;
            $endpoint = $this->getBaseUrl() . ":$port/api/ciudades/$params";

            $response = $this->client()->get($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);
            $body = self::responseArray($response);
            self::handleErrors($body);
            return $body;
        }catch(RequestException $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function generateGuide(array $params): array
    {
        try {
            $slugUrl = array_key_exists('valor_a_recaudar', $params) ? 'guiarecaudo' : 'guiawebs';

            $port = self::$sandbox ? self::SANDBOX_PORT_GUIDES : self::PORT_GUIDES;
            $endpoint = $this->getBaseUrl() . ":$port/api/$slugUrl";

            $params = [
                ...$params,
                'login_creacion' => $this->user,
                'password' => $this->password
            ];
            $response = $this->client()->post($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => $params
            ]);
            $body = self::responseArray($response);
            self::handleErrors($body);
            return $body;
        }catch(RequestException $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function printGuide(array $params): array
    {
        try {

            if(empty($params['numero_guia'])){
                throw new \Exception('numero_guia es requerido');
            }

            if(empty($params['formato_impresion_guia'])){
                throw new \Exception('formato_impresion_guia es  requerido');
            }

            $formats_allow = ['pdfA4', 'pdf', 'rotulo', 'sticker'];

            if(!in_array($params['formato_impresion_guia'], $formats_allow)){
                throw new \Exception('formato_impresion_guia debe tener un valor válido: ' . implode(', ', $formats_allow));
            }

            if($params['formato_impresion_guia'] === 'pdfA4'){
                $slugUrl = 'GuiasWeb';
            }else if($params['formato_impresion_guia'] === 'pdf'){
                $slugUrl = 'GuiaDigital';
            }else if($params['formato_impresion_guia'] === 'rotulo'){
                $slugUrl = 'ImprimeRotulos';
            }else {
                $slugUrl = 'ImprimeSticker';
            }

            $queryParams = sprintf('["%s","%s","%s", "%s"]', $params['numero_guia'], $this->user, $this->password, '1');
            $port = self::$sandbox ? self::SANDBOX_PORT_PRINT : self::PORT_PRINT;
            $endpoint = $this->getBaseUrl() . ":$port/api/$slugUrl/$queryParams";

            $response = $this->client()->get($endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);
            $body = self::responseArray($response);
            self::handleErrors($body);
            return $body;
        }catch(RequestException $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    private static function handleErrors(array $data): void
    {
        if(isset($data[0]['id']) &&
            isset($data[0]['nombre']) &&
            $data[0]['nombre'] === 'LA AUTENTICACIÓN ES INCORRECTA'
        ){
            throw new \Exception($data[0]['nombre']);
        }
    }

    private static function responseArray($response):array
    {
        return Utils::jsonDecode($response->getBody()->getContents(), true);
    }
}
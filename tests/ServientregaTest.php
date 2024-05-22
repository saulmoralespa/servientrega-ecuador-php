<?php

use PHPUnit\Framework\TestCase;
use Saulmoralespa\ServientregaEcuador\Client;

class ServientregaTest  extends TestCase
{
    protected Client $client;

    protected function setUp(): void
    {
        $dotenv = Dotenv\Dotenv::createMutable(__DIR__ . '/../');
        $dotenv->load();
        $user = $_ENV['USER'];
        $password = $_ENV['PASSWORD'];

        $this->client = new Client($user, $password);
        $this->client->sandboxMode(true);
    }

    public function testGetCities()
    {
        $res = $this->client->getCities();

        $this->assertIsArray($res, 'No is array');
        $this->assertIsList($res);
    }

    public function testGenerateGuide()
    {
       $params = [
            "id_tipo_logistica" => 1,
            "detalle_envio_1" =>"",
            "detalle_envio_2" => "",
            "detalle_envio_3" => "",
            "id_ciudad_origen" => '',
            "id_ciudad_destino" => 42,
            "id_destinatario_ne_cl" => "001dest",
            "razon_social_desti_ne" => "prueba de api s.a",
            "nombre_destinatario_ne" => "gustavo andres",
            "apellido_destinatar_ne" => "tecnologia matriz ",
            "direccion1_destinat_ne" => "panama 306 y thomas y martinez",
            "sector_destinat_ne"  => "",
            "telefono1_destinat_ne" => "3732000 ext 4732",
            "telefono2_destinat_ne" => "",
            "codigo_postal_dest_ne" => "",
            "id_remitente_cl" => "001remi",
            "razon_social_remite" => "servientrega ecuador s.a",
            "nombre_remitente" => "gustavo ",
            "apellido_remite" => "villalba lopez",
            "direccion1_remite" => "panama 306 y thomas y martinez",
            "sector_remite" => "",
            "telefono1_remite" => "123156",
            "telefono2_remite" => "",
            "codigo_postal_remi" => "",
            "id_producto" => 2,
            "contenido" => "laptop",
            "numero_piezas" => 1,
            "valor_mercancia" => 0,
            "valor_asegurado" => 0,
            "largo" => 0,
            "ancho" => 0,
            "alto" => 0,
            "peso_fisico" => 0.5,
       ];
       $res = $this->client->generateGuide($params);
       $this->assertArrayHasKey('id', $res, "Array doesn't contains 'id' as key");
    }

    public function testGenerateGuideCollection()
    {
        $params = [
            "id_tipo_logistica" => 1,
            "detalle_envio_1" =>"1158740281004-01",
            "detalle_envio_2" => "32230",
            "detalle_envio_3" => "",
            "id_ciudad_origen" => 1,
            "id_ciudad_destino" => 1,
            "id_destinatario_ne_cl" => "PRUEBA",
            "razon_social_desti_ne" => "112233445511",
            "nombre_destinatario_ne" => "gustavo andres",
            "apellido_destinatar_ne" => "tecnologia matriz ",
            //"direccion1_destinat_ne" => "panama 306 y thomas y martinez",
            "sector_destinat_ne"  => "",
            "telefono1_destinat_ne" => "3732000 ext 4732",
            "telefono2_destinat_ne" => "",
            "codigo_postal_dest_ne" => "",
            "id_remitente_cl" => "PRUEBA",
            "razon_social_remite" => "servientrega ecuador s.a",
            "nombre_remitente" => "gustavo ",
            "apellido_remite" => "villalba lopez",
            "direccion1_remite" => "panama 306 y thomas y martinez",
            "sector_remite" => "",
            "telefono1_remite" => "123156",
            "telefono2_remite" => "",
            "codigo_postal_remi" => "",
            "id_producto" => 2,
            "contenido" => "laptop",
            "numero_piezas" => 1,
            "valor_mercancia" => 0,
            "valor_asegurado" => 0,
            "largo" => 0,
            "ancho" => 0,
            "alto" => 0,
            "peso_fisico" => 0.5,
            //aditionals params recaudo
            "fecha_factura" => "2021-10-12",
            "numero_factura" => "002584154154",
            "valor_factura" => 150.25,
            "valor_flete " => "100",
            "valor_comision" => "10",
            "valor_seguro" => "100",
            "valor_impuesto" => "10",
            "valor_otros" => "0",
            "valor_a_recaudar" => "200",
            "detalle_items_factura" => "pruebas sistemas",
            "verificar_contenido_recaudo" => "",
            "validador_recaudo" => "D",
            "id_cl" => 0,
            "direccion_recaudo" =>  "daule"
        ];
        $res = $this->client->generateGuide($params);
        $this->assertArrayHasKey('id', $res, "Array doesn't contains 'id' as key");
    }

    public function testPrintGuide()
    {
        $params = [
            "numero_guia" => 32233,
            "formato_impresion_guia" => "rotulo" //pdfA4, pdf, sticker, rotulo
        ];

        $res = $this->client->printGuide($params);
        $this->assertIsArray($res);
        $this->assertArrayHasKey('guia', $res);
    }
}
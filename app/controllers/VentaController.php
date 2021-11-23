<?php

use Slim\Psr7\Response;
use Fpdf\Fpdf;

require_once './models/Venta.php';
require_once './interfaces/IApiUsable.php';

class VentaController extends Venta implements IApiUsable
{
    public function CargarUno($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $archivo = $request->getUploadedFiles();
        $fecha = date("Y-m-d");
        var_dump($fecha);
        $cantidad = intval($parametros['cantidad']);
        $usuarioId = intval($parametros['idUsuario']);
        $cryptoId = intval($parametros['idCrypto']);
        $usr = UsuarioController::obtenerPorId($usuarioId);
        $cry = CryptoController::obtenerCrypto($cryptoId);
        try {
            $foto = $archivo['foto'];
            if (is_null($foto)) {
                throw new Exception("No file");
            }
            $ext = $foto->getClientMediaType();
            $ext = explode("/", $ext)[1];
            $ruta = "./FotosCripto/" . $cry->nombre . " " . $usr->usuario . " " . $fecha . "." . $ext;
            echo ($ruta);
            $foto->moveTo($ruta);
        } catch (Exception $e) {
            $ruta = "";
        }

        $venta = new Venta();
        $venta->cantidad = $cantidad;
        $venta->fecha = $fecha;
        $venta->id_usuario = $usuarioId;
        $venta->id_crypto = $cryptoId;
        $venta->imagen = $ruta;

        $venta->crearVenta();

        $payload = json_encode(array("mensaje" => "Venta realizada con exito"));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $handler, $args)
    {
        $parametros = $request->getQueryParams();
        // Buscamos venta por id
        $id = $parametros['id'];
        $venta = Venta::obtenerVenta(intval($id));
        $payload = json_encode($venta);

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $handler)
    {
        $lista = Venta::obtenerTodos();
        $payload = json_encode(array("listaVenta" => $lista));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $handler)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $c = Venta::obtenerVenta(intval($id));

        $response = new Response();

        if (isset($c->fecha)) {
            if (isset($parametros['cantidad'])) {
                $c->cantidad = intval($parametros['cantidad']);
            }
            if (isset($parametros['idUsuario'])) {
                $c->id_usuario = intval($parametros['idUsuario']);
            }
            if (isset($parametros['idCrypto'])) {
                $c->id_crypto = intval($parametros['idCrypto']);
            }

            $c->modificarVenta();
            $payload = json_encode(array("mensaje" => "Venta modificada con exito"));
            $response->getBody()->write($payload);
        } else {
            $response->withStatus(404, "No se encuentra venta");
        }
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $handler)
    {
        $parametros = $request->getParsedBody();

        $ventaId = intval($parametros['id']);
        Venta::borrarVenta($ventaId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function TraerNacion($request, $handler, $args)
    {
        $parametros = $request->getParsedBody();
        $lista = Venta::obtenerPorNacion($args['nacionalidad']);
        $payload = json_encode(array("listaCrypto" => $lista));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public static function TraerPdf($request, $handler)
    {
        $lista = Venta::obtenerTodos();
        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        foreach ($lista as $key => $value) {
            $pdf->Cell(8, 6, $value->id, 1);
            $pdf->Cell(40, 6, $value->fecha, 1);
            $pdf->Cell(10, 6, $value->cantidad, 1);
            $pdf->Cell(100, 6, $value->imagen, 1);
            $pdf->Cell(10, 6, $value->id_usuario, 1);
            $pdf->Cell(10, 6, $value->id_crypto, 1);
            $pdf->Ln();
        }
        $pdf->Output();


        $response = new Response();
        return $response
            ->withStatus(200);
    }
}

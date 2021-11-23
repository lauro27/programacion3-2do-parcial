<?php

use Slim\Psr7\Response;

require_once './models/Crypto.php';
require_once './interfaces/IApiUsable.php';

class CryptoController extends Crypto implements IApiUsable
{
    public function CargarUno($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $archivo = $request->getUploadedFiles();
        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $nacion = $parametros['nacionalidad'];
        try{
        $foto = $archivo['foto'];
        if(is_null($foto)){ throw New Exception("No file");}
        $ext = $foto->getClientMediaType();
        $ext = explode("/", $ext)[1];
        $ruta = "./Cryptos/". $nombre . "." .$ext;
        $foto->moveTo($ruta);
        }
        catch(Exception $e){
          $ruta = "";
        }

        $crypto = new Crypto();
        $crypto->nombre = $nombre;
        $crypto->precio = $precio;
        $crypto->nacionalidad = $nacion;
        $crypto->foto = $ruta;
        
        $crypto->crearCrypto();

        $payload = json_encode(array("mensaje" => "Crypto creado con exito"));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $handler, $args)
    {
        $parametros = $request->getQueryParams();
        // Buscamos crypto por id
        $id = $parametros['id'];
        $crypto = Crypto::obtenerCrypto(intval($id));
        $payload = json_encode($crypto);

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $handler)
    {
        $lista = Crypto::obtenerTodos();
        $payload = json_encode(array("listaCrypto" => $lista));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $handler)
    {
        $parametros = $request->getParsedBody();
        $archivo = $request->getUploadedFiles();
        $id = $parametros['id'];
        $c = Crypto::obtenerCrypto(intval($id));

        $response = new Response();
        
        if(isset($c->nombre)){
            if(isset($parametros['nombre'])){ $c->nombre = $parametros['nombre']; }
            if(isset($parametros['precio'])){ $c->precio = $parametros['precio']; }
            if(isset($parametros['nacionalidad'])){ $c->nacionalidad = $parametros['nacionalidad']; }
            if(isset($archivo['foto'])){
              try{
                $foto = $archivo['foto'];
                if(is_null($foto)){ throw New Exception("No file");}
                $ext = $foto->getClientMediaType();
                $ext = explode("/", $ext)[1];
                $ruta = "./Cryptos/". $c->nombre ."." . $ext;
                $xp = explode('/', $c->foto);
                var_dump($xp);
                $prevFilename = array_pop($xp);
                if($c->foto != "")
                { rename($c->foto, './Cryptos/Backup/'. $prevFilename);}
                $foto->moveTo($ruta);
                $c->foto = $ruta; 
                }
                catch(Exception $e){
                    var_dump($e->getMessage());

                }
            }
            
            $c->modificarCrypto();
            $payload = json_encode(array("mensaje" => "Crypto modificado con exito"));
            $response->getBody()->write($payload);
        }
        else{
            $response->withStatus(404, "No se encuentra crypto");
        }
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $handler)
    {
        $parametros = $request->getQueryParams();
        $cryptoId = intval($parametros['id']);
        Crypto::borrarCrypto($cryptoId);

        $payload = json_encode(array("mensaje" => "Crypto borrado con exito"));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public static function TraerNacion($request, $handler, $args)
    {
        $parametros = $request->getParsedBody();
        $lista = Crypto::obtenerPorNacion($args['nacionalidad']);
        $payload = json_encode(array("listaCrypto" => $lista));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');        
    }
}

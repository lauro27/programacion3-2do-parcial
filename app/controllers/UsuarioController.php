<?php

use Slim\Psr7\Response;

require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';


class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $handler)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $isAdmin = intval($parametros['isAdmin']); //0 ó 1
        // Creamos el usuario
        $usr = new Usuario();
        $usr->usuario = $usuario;
        $usr->clave = $clave;
        $usr->isAdmin = $isAdmin;
        $usr->crearUsuario();

        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));        
        $response = new Response();
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $handler, $args){
        // Buscamos usuario por nombre
        $parametros = $request->getParsedBody();
        $usr = $parametros['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $handler)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    
    public function ModificarUno($request, $handler)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        Usuario::modificarUsuario($nombre);

        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $handler)
    {
        $parametros = $request->getQueryParams();

        $usuarioId = $parametros['id'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response

          ->withHeader('Content-Type', 'application/json');
    }

    public static function TraerCrypto($request, $handler, $args)
    {
        $parametros = $request->getParsedBody();
        $lista = Usuario::obtenerPorCrypto($args['crypto']);
        $payload = json_encode(array("listaUsuario" => $lista));

        $response = new Response();
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');  
    }      
}

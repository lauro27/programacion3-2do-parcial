<?php

use Slim\Psr7\Response;
use Firebase\JWT;

require_once './models/AuthJWT.php';

class AuthMW
{
    public static function LoginAdmin($request, $handler){
        $header = $request->getHeaderLine("authorization");
        $token = trim(explode('Bearer', $header)[1]);
        
        $response = new Response();
        
        try{
            $payload = json_decode(AutentificadorJWT::ObtenerData($token)); 
            if($payload->isAdmin != 1){ throw new Exception("No autorizado");}
            $response = $handler->handle($request);
        }
        catch(Exception $e){
            $payload = json_encode(array('error'=> $e->getMessage()));
        }
        return $response;
    }

    public static function Login($request, $handler){
        $header = $request->getHeaderLine("authorization");
        $token = trim(explode('Bearer', $header)[1]);
        
        $response = new Response();
        
        try{
            $payload = json_decode(AutentificadorJWT::ObtenerData($token)); 
            $response = $handler->handle($request);
        }
        catch(Exception $e){
            $payload = json_encode(array('error'=> $e->getMessage()));
        }
        return $response;
    }
}
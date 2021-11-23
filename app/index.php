<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Slim\Middleware\BodyParsingMiddleware;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/AuthMW.php';
require_once './middlewares/logger.php';

require_once './controllers/LoginController.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/CryptoController.php';
require_once './controllers/VentaController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Routes
$app->get('/debugvalidar[/]', \LoginController::class . ':ProbarDatos');
$app->post('/login[/]', \LoginController::class . ':IniciarSesion');

$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
  $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(\AuthMW::class . ':LoginAdmin');
});

$app->group('/crypto', function (RouteCollectorProxy $group) {
  $group->get('[/]', \CryptoController::class . ':TraerTodos');
  $group->post('[/]', \CryptoController::class . ':CargarUno')->add(\AuthMW::class . ':LoginAdmin');
  $group->get('/nacion/{nacionalidad}', \CryptoController::class . ':TraerNacion');
  $group->get('/id', \CryptoController::class . ':TraerUno')->add(\AuthMW::class . ':Login');
  $group->delete('[/]', \CryptoController::class . ':BorrarUno')->add(\AuthMW::class . ':LoginAdmin');
  $group->post('/modificar[/]', \CryptoController::class . ':ModificarUno')->add(\AuthMW::class . ':LoginAdmin');
});

$app->post('/compra[/]', \VentaController::class . ':CargarUno')->add(\AuthMW::class . ':Login');

$app->group('/venta', function (RouteCollectorProxy $group) {
  $group->get('[/]', \VentaController::class . ':TraerPdf');
  $group->get('/nacion/{nacionalidad}', \VentaController::class . ':TraerNacion');
  $group->get('/crypto/{crypto}', \UsuarioController::class . ':TraerCrypto');
})->add(\AuthMW::class . ':LoginAdmin');

$app->get('[/]', function (Request $request, Response $response) {
  $response->getBody()->write("Slim Framework 4 PHP");
  return $response;
});



$app->run();

<?php
use SfpDiactoros\Response\SwitchingEmitter;
use SfpDiactoros\Stream\RewindFpassthruStream;
use Zend\Diactoros\Server;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

require_once __DIR__.'/../vendor/autoload.php';

$app = function(ServerRequest $request, Response $response, $done) {
    // $ dd if=/dev/zero of=/tmp/tempfile bs=1M count=10
    
    $image = '/tmp/tempfile';
    $response = $response->withHeader('Content-Type', 'image/jpeg')
        ->withHeader('Content-Length', (string) filesize($image));
    
    $stream = new RewindFpassthruStream($image);
    return $response->withBody($stream);
};

$server = Server::createServer($app, $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$server->setEmitter(new SwitchingEmitter);
$server->listen();

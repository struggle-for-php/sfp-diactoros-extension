<?php
use SfpDiactoros\Response\IteratorResponse;
use Zend\Diactoros\Server;

require_once __DIR__.'/../vendor/autoload.php';

function row2csvline($row) {
    mb_convert_variables('sjis-win', 'UTF-8', $row);
    $line = fopen('php://memory', 'r+w');
    fputcsv($line, $row, ",",  '"');
    rewind($line);
    return stream_get_contents($line);
}

$app = function() {

    $iterator = call_user_func(function () {
        $row = ['あ,　か', 'い', '①'];
        yield row2csvline($row) . "\n";
    });

    return new IteratorResponse($iterator, 200, [
        'content-disposition' => ['attachment; filename=sample.csv'],
        'content-type' => ['text/csv; charset=Shift_JIS']
    ]);
};

$server = Server::createServer($app, $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$server->listen();


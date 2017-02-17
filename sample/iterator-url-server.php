<?php
use SfpDiactoros\Response\IteratorResponse;

require_once __DIR__.'/../vendor/autoload.php';

function csv($row) {
    mb_convert_variables('sjis-win', 'UTF-8', $row);
    $line = fopen('php://memory', 'r+w');
    fputcsv($line, $row, ",",  '"');
    rewind($line);
    return stream_get_contents($line);
}

function uriage ($year, $m, $itemCode = null) {
    return 8000;
}


$app = function() {

    $iterator = call_user_func(function () {

        $items = [
            ['code' => 'A0001', 'name' => 'pen'],
            ['code' => 'A0002', 'name' => 'apple',],
            ['code' => 'A0003', 'name' => 'applepen',],
            ['code' => 'A0004', 'name' => 'ppap',]
        ];

        // 見出し
        yield csv(['年月', '商品コード', '商品名', '売上']);

        foreach (range(2000, 2016) as $year) {
            foreach (range(1,12) as $m) {
                $小計 = 0;
                foreach ($items as $item) {
                    $小計 += $売上 = uriage($year, $m, $item['code']);
                    yield csv([sprintf("%04d-%02d", $year, $m), $item['code'], $item['name'], $売上]);
                }
                yield csv(["","","小計", $小計]);
            }
        }

    });

    return new IteratorResponse($iterator, 200, [
        'content-disposition' => ['attachment; filename=sample.csv'],
        'content-type' => ['application/octet-stream']
    ]);
};

(new \Zend\Diactoros\Response\SapiStreamEmitter)->emit($app());

file_put_contents("php://stdout", "\nMemory Usage: " . formatBytes(memory_get_peak_usage()));

function formatBytes($bytes, $precision = 2) {
    if ( abs($bytes) < 1024 ) $precision = 0;

    $sign = '';
    if ( $bytes < 0 ) {
        $sign = '-';
        $bytes = abs($bytes);
    }
    $exp   = floor(log($bytes) / log(1024));
    $bytes = sprintf('%.'.$precision.'f', ($bytes / pow(1024, floor($exp))));
    return $sign . $bytes .' '. ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'][$exp];
}
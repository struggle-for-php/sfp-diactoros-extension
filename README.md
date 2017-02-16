sfp-diactoros-extension
==============

[![Build Status](https://travis-ci.org/struggle-for-php/sfp-diactoros-extension.png?branch=master)](https://travis-ci.org/struggle-for-php/sfp-diactoros-extension)

extensions for zend-diactoros.



## SwitchingEmitter

`SfpDiactoros\Response\SwitchingEmitter` allows `fpassthru()` with `FpassthruInterface`.

just changed only in `emitBody()` from original `Zend\Diactoros\Response\SapiEmitter`.

```php
// SapiEmitter
echo $response->getBody();
```


```php
// SwitchingEmitter
if ($body instanceof FpassthruInterface) {
    $resource = $body->detach();
    if ($body instanceof RewindFpassthruInterface) {
        rewind($resource);
    }
    fpassthru($resource);
} else {
    echo $response->getBody();
}
```

### Usage

```php
use SfpDiactoros\Response\SwitchingEmitter;

$server = Server::createServer($app, $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$server->setEmitter(new SwitchingEmitter);

```

```php
use SfpDiactoros\Stream\RewindFpassthruStream;

$fp = fopen('/tmp/bigsize', 'r');
$response->withBody(new RewindFpassthruStream($fp));

```
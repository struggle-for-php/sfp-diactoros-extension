<?php

/**
 * The most of methods are derived from code of the Zend Framework, zend-diactoros  (1.0.1 - 2015-06-05).
 *
 * Code subject to the new BSD license (http://framework.zend.com/license/new-bsd).
 *
 * Copyright (c) 2015 Zend Technologies USA Inc. (http://www.zend.com)
 */
namespace SfpDiactoros\Response;

use RuntimeException;
use Psr\Http\Message\ResponseInterface;
use SfpDiactoros\Stream\FpassthruInterface;
use SfpDiactoros\Stream\RewindFpassthruInterface;
use Zend\Diactoros\Response\EmitterInterface;
use Zend\Diactoros\Response\SapiEmitterTrait;

class SwitchingEmitter implements EmitterInterface
{
    use SapiEmitterTrait;

    /**
     * Emits a response for a PHP SAPI environment.
     *
     * Emits the status line and headers via the header() function, and the
     * body content via the output buffer.
     *
     * @param ResponseInterface $response
     * @param null|int          $maxBufferLevel Maximum output buffering level to unwrap.
     */
    public function emit(ResponseInterface $response, $maxBufferLevel = null)
    {
        if (headers_sent()) {
            throw new RuntimeException('Unable to emit response; headers already sent');
        }

        $response = $this->injectContentLength($response);

        $this->emitStatusLine($response);
        $this->emitHeaders($response);
        $this->flush($maxBufferLevel);
        $this->emitBody($response);
    }

    /**
     * Emit the message body.
     *
     * @param ResponseInterface $response
     */
    private function emitBody(ResponseInterface $response)
    {
        $body = $response->getBody();

        if ($body instanceof FpassthruInterface) {
            $resource = $body->detach();
            if ($body instanceof RewindFpassthruInterface) {
                rewind($resource);
            }
            fpassthru($resource);
        } else {
            echo $response->getBody();
        }
    }
}

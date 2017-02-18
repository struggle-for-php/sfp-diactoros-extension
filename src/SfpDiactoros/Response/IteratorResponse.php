<?php
namespace SfpDiactoros\Response;

use Iterator;
use SfpIteratorUrl\IteratorUrl;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class IteratorResponse extends Response
{
    private $iteratorUrl;

    public function __construct(Iterator $iterator, $status = 200, array $headers = [])
    {
        parent::__construct(
            $this->createBody($iterator),
            $status,
            $headers
        );
    }

    private function createBody(Iterator $iterator)
    {
        $fp = $this->getIteratorUrl()->open($iterator);
        return new Stream($fp);
    }

    /**
     * @return IteratorUrl
     */
    private function getIteratorUrl()
    {
        if (!$this->iteratorUrl instanceof IteratorUrl) {
            $this->iteratorUrl = new IteratorUrl();
        }

        return $this->iteratorUrl;
    }
}

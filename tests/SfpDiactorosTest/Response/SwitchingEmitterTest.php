<?php

namespace SfpDiactorosTest\Response;

use PHPUnit_Framework_TestCase;
use PHPUnit_Extension_FunctionMocker;
use Zend\Diactoros\Response;
use SfpDiactoros\Response\SwitchingEmitter;
use SfpDiactoros\Stream\RewindFpassthruStream;

class SwitchingEmitterTest extends PHPUnit_Framework_TestCase
{
    private $emitter;

    public function setUp()
    {
        $this->emitter = new SwitchingEmitter();

        $this->php = PHPUnit_Extension_FunctionMocker::start($this, 'SfpDiactoros\\Response')
            ->mockFunction('headers_sent')
            ->mockFunction('rewind')
            ->getMock();
    }

    /** @runInSeparateProcess */
    public function testSwitchingEmitbodyDontCallFpassthruWhenNonMarkerInterface()
    {
        $expected = 'foo';
        $response = new Response("data://text/html,{$expected}");

        $this->php->expects($this->once())
            ->method('headers_sent')
            ->will($this->returnValue(false));
        $this->php->expects($this->never())
            ->method('fpassthru');

        ob_start();
        $this->emitter->emit($response);
        $this->assertEquals($expected, ob_get_clean());
    }

    /** @runInSeparateProcess */
    public function testSwitchingEmitbodyFpassthruWithMarkerInterface()
    {
        $expected = 'bar';
        $stream = new RewindFpassthruStream("data://text/html,{$expected}");
        $response = new Response($stream);

        $this->php->expects($this->once())
            ->method('headers_sent')
            ->will($this->returnValue(false));
        $this->php->expects($this->once())
            ->method('rewind');

        ob_start();
        $this->emitter->emit($response);
        $this->assertEquals($expected, ob_get_clean());
    }
}

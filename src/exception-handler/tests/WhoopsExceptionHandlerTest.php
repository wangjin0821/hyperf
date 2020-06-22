<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace HyperfTest\ExceptionHandler;

use GuzzleHttp\Psr7\Response;
use Hyperf\ExceptionHandler\Handler\WhoopsExceptionHandler;
use Hyperf\HttpMessage\Server\Request;
use Hyperf\Nats\Exception;
use Hyperf\Utils\Context;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @internal
 * @coversNothing
 */
class WhoopsExceptionHandlerTest extends TestCase
{
    public function testPlainTextWhoops()
    {
        Context::set(ServerRequestInterface::class, new Request('GET', '/'));
        $handler = new WhoopsExceptionHandler();
        $response = $handler->handle(new Exception(), new Response());
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('text/plain', $response->getHeader('Content-Type')[0]);
    }

    public function testHtmlWhoops()
    {
        $request = new Request('GET', '/');
        $request = $request->withHeader('accept', ['text/html,application/json,application/xml']);
        Context::set(ServerRequestInterface::class, $request);
        $handler = new WhoopsExceptionHandler();
        $response = $handler->handle(new Exception(), new Response());
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('text/html', $response->getHeader('Content-Type')[0]);
    }

    public function testJsonWhoops()
    {
        $request = new Request('GET', '/');
        $request = $request->withHeader('accept', ['application/json,application/xml']);
        Context::set(ServerRequestInterface::class, $request);
        $handler = new WhoopsExceptionHandler();
        $response = $handler->handle(new Exception(), new Response());
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeader('Content-Type')[0]);
    }

    public function testXmlWhoops()
    {
        $request = new Request('GET', '/');
        $request = $request->withHeader('accept', ['application/xml']);
        Context::set(ServerRequestInterface::class, $request);
        $handler = new WhoopsExceptionHandler();
        $response = $handler->handle(new Exception(), new Response());
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('application/xml', $response->getHeader('Content-Type')[0]);
    }
}

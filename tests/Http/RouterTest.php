<?php declare(strict_types=1);

namespace Jtl\Shop5Router\Test\Http;

use Jtl\Shop5Router\Http\Router;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class RouterTest extends TestCase
{
    protected Router $router;
    protected string $controllerPath = 'Jtl\Shop5Router\Test\Http\Controller';
    
    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        
        $this->router = new Router($this->controllerPath);
    }
    
    public function testCanGetARoute(): void
    {
        self::expectException(RuntimeException::class);
        $this->router->getRoute();
    }
    
    public function testCanSendMustThrow(): void
    {
        self::expectException(InvalidArgumentException::class);
        $this->router->send(false);
    }
    
    public function testCanGetControllerPath(): void
    {
        self::assertEquals($this->controllerPath, $this->router->getControllerPath());
    }
    
    public function testCanSetControllerPath(): void
    {
        $this->router->setControllerPath('\Zick\Zack');
        self::assertEquals('\Zick\Zack', $this->router->getControllerPath());
    }
    
    public function testCanSend(): void
    {
        $_GET['action'] = 'foo_bar.foo';
        $json = $this->router->send(false);
        
        $response = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($response);
        self::assertArrayHasKey('code', $response);
        self::assertArrayHasKey('data', $response);
        self::assertArrayHasKey('error', $response);
        self::assertEquals(0, $response['code']);
        self::assertNull($response['data']);
        self::assertNotEmpty($response['error']);
    
        $_GET['bar'] = 'foo';
        $json = $this->router->send(false);
    
        $response = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($response['data']);
    }
    
    public function testHasBeforeCallback(): void
    {
        $_GET['action'] = 'foo_bar.foo';

        $this->router->before(static function (Request $request, array $arguments) {
            $request->query->set('bar', 'foo');
        });
    
        $json = $this->router->send(false);
        $response = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($response['data']);
    }
    
    public function testHasAfterCallback(): void
    {
        $_GET['action'] = 'foo_bar.foo';

        $this->router->after(static function (Request $request, array $arguments, $result) {
            self::assertTrue($result);
        });
    
        $json = $this->router->send(false);
        $response = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($response['data']);
    }
}

<?php declare(strict_types=1);

namespace Jtl\Shop5Router\Test\Http\Controller;

use Jtl\Shop5Router\Http\Controller\ControllerInterface;
use PHPUnit\Framework\TestCase;

class AbstractControllerTest extends TestCase
{
    public function testImplmentsControllerInterface(): void
    {
        $controller = new FooBarController();
        self::assertInstanceOf(ControllerInterface::class, $controller);
    }
}

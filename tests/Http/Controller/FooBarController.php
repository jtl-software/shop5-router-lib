<?php declare(strict_types=1);

namespace Jtl\Shop5Router\Test\Http\Controller;

use Jtl\Shop5Router\Http\Controller\AbstractController;
use Illuminate\Http\Request;

class FooBarController extends AbstractController
{
    /**
     * @param string $bar
     * @return bool
     */
    public function foo(Request $request, string $bar): bool
    {
        return $bar !== '';
    }
    
    /**
     * @param string $foo
     * @return bool
     */
    public function bar(string $foo): bool
    {
        return $foo !== '';
    }
    
    /**
     * @param Request $request
     * @return string
     */
    public function fooBar(Request $request): string
    {
        return 'yolo';
    }
}

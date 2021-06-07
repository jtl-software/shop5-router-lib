<?php declare(strict_types=1);

namespace Jtl\Shop5Router\Http\Controller;
use Illuminate\Http\Request;

/**
 * Interface ControllerInterface
 * @package Jtl\Shop5Router\Http\Controller
 */
interface ControllerInterface
{
    /**
     * @param Request $request
     * @param string $method
     * @param array $parameter
     * @return mixed
     */
    public function call(Request $request, string $method, array $parameter = []);
}

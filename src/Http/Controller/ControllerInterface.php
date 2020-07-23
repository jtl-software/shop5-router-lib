<?php declare(strict_types=1);

namespace JTL\Shop5Router\Http\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ControllerInterface
 * @package JTL\Shop5Router\Http\Controller
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

<?php

namespace Thruster\Component\WebApplication;

use Psr\Http\Message\ServerRequestInterface;
use Thruster\Component\HttpRouter\AsyncRouteHandlerInterface;
use Thruster\Component\HttpRouter\RouteHandlerInterface;
use Thruster\Component\HttpRouter\RouteProviderInterface;
use Thruster\Component\Promise\ExtendedPromiseInterface;

/**
 * Interface AsyncWebApplicationInterface
 *
 * @package Thruster\Component\WebApplication
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
interface AsyncWebApplicationInterface extends RouteProviderInterface, AsyncRouteHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ExtendedPromiseInterface
     */
    public function processRequest(ServerRequestInterface $request) : ExtendedPromiseInterface;
}

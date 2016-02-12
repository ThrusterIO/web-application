<?php

namespace Thruster\Component\WebApplication;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Thruster\Component\HttpRouter\RouteHandlerInterface;
use Thruster\Component\HttpRouter\RouteProviderInterface;

/**
 * Interface ApplicationInterface
 *
 * @package Thruster\Component\WebApplication
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
interface ApplicationInterface extends RouteProviderInterface, RouteHandlerInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function processRequest(ServerRequestInterface $request) : ResponseInterface;
}

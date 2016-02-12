<?php

namespace Thruster\Component\WebApplication;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Thruster\Component\HttpMessage\Response;
use Thruster\Component\HttpMiddleware\RequestMiddlewaresAwareTrait;
use Thruster\Component\HttpRouter\Router;

/**
 * Class BaseApplication
 *
 * @package Thruster\Component\WebApplication
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
abstract class BaseApplication implements ApplicationInterface
{
    use RequestMiddlewaresAwareTrait;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @return Router
     */
    public function getRouter() : Router
    {
        if ($this->router) {
            return $this->router;
        }

        $this->router = new Router($this, $this);

        return $this->router;
    }

    /**
     * @param Router $router
     *
     * @return $this
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function processRequest(ServerRequestInterface $request) : ResponseInterface
    {
        $response = new Response();

        return $this->executeMiddlewares($request, $response, $this->getRouter());
    }

    /**
     * @inheritDoc
     */
    public function handleRoute(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $callback
    ) : ResponseInterface {
        return $callback($request, $response);
    }

    /**
     * @inheritDoc
     */
    public function handleRouteMethodNotAllowed(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $allowedMethods
    ) : ResponseInterface {
        return $response->withStatus(405)->withHeader('Allow', implode(', ', $allowedMethods));
    }

    /**
     * @inheritDoc
     */
    public function handleRouteNotFound(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : ResponseInterface {
        return $response->withStatus(404);
    }
}

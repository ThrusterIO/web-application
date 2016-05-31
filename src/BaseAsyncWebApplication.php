<?php

namespace Thruster\Component\WebApplication;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Thruster\Component\HttpMessage\Response;
use Thruster\Component\HttpMiddleware\RequestMiddlewaresAwareTrait;
use Thruster\Component\HttpRouter\AsyncRouter;
use Thruster\Component\Promise\Deferred;
use Thruster\Component\Promise\ExtendedPromiseInterface;
use Thruster\Component\Promise\FulfilledPromise;

/**
 * Class BaseAsyncWebApplication
 *
 * @package Thruster\Component\WebApplication
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
abstract class BaseAsyncWebApplication implements AsyncWebApplicationInterface
{
    use RequestMiddlewaresAwareTrait;

    /**
     * @var AsyncRouter
     */
    protected $router;

    /**
     * @return AsyncRouter
     */
    public function getRouter() : AsyncRouter
    {
        if ($this->router) {
            return $this->router;
        }

        $this->router = new AsyncRouter($this, $this);

        return $this->router;
    }

    /**
     * @param AsyncRouter $router
     *
     * @return $this
     */
    public function setRouter(AsyncRouter $router)
    {
        $this->router = $router;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function processRequest(ServerRequestInterface $request) : ExtendedPromiseInterface
    {
        $response = new Response();

        $deferred = new Deferred();

        $this->getRequestMiddlewares()->getPreMiddlewares()->__invoke(
            $request,
            $response,
            function (ServerRequestInterface $request, ResponseInterface $response) use ($deferred) {
                $promise = $this->getRouter()->__invoke(
                    $request,
                    $response,
                    $this->getRequestMiddlewares()->getPostMiddlewares()
                );

                $promise->then([$deferred, 'resolve'], [$deferred, 'reject'], [$deferred, 'notify']);

                return $response;
            }
        );

        return $deferred->promise();
    }

    /**
     * @inheritDoc
     */
    public function handleRoute(
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $callback
    ) : ExtendedPromiseInterface {

        $defered = new Deferred();

        $callback($defered, $request, $response);

        return $defered->promise();
    }

    /**
     * @inheritDoc
     */
    public function handleRouteMethodNotAllowed(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $allowedMethods
    ) : ExtendedPromiseInterface {
        return new FulfilledPromise($response->withStatus(405)->withHeader('Allow', implode(', ', $allowedMethods)));
    }

    /**
     * @inheritDoc
     */
    public function handleRouteNotFound(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : ExtendedPromiseInterface {
        return new FulfilledPromise($response->withStatus(404));
    }
}

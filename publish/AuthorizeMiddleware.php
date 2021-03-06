<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\ErrorCode;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Arr;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use unify\connector\User;
use unify\connector\UserState;

/**
 * Class PermissionMiddleware
 * 认证权限中间件
 * @package App\Middleware
 */
class AuthorizeMiddleware implements MiddlewareInterface
{
    use UserState;

    /**
     * @var HttpResponse
     */
    protected $response;

    public function __construct(HttpResponse $response)
    {
        $this->response = $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = Arr::first($request->getHeader('Authorization'));

        $cache = ApplicationContext::getContainer()->get(CacheInterface::class);
        $user = $cache->get('token:user:' . $token);

        if ($user) {
            $user = new User($user);
            $this->setUser($user);
            return $handler->handle($request);
        }

        return $this
            ->response
            ->withStatus(200)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream(message(ErrorCode::AUTH_FAILURE)));
    }
}

<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\ErrorCode;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\ApplicationContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use unify\connector\UserState;
use unify\contract\PermissionUniqueSlug;

/**
 * Class PermissionMiddleware
 * 认证权限中间件
 * @package App\Middleware
 */
class PermissionMiddleware implements MiddlewareInterface
{
    use PermissionUniqueSlug;
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
        // 超级管理员或者应用管理员不验证权限
        if ($this->getUser()->isSuperAdmin || $this->getUser()->isOwner) {
            return $handler->handle($request);
        }
        $dispatcher = $request->getAttribute(Dispatched::class);
        $slug = $this->slug($request->getMethod(), strtolower($dispatcher->handler->route));

        $cache = ApplicationContext::getContainer()->get(CacheInterface::class);
        $permission = $cache->get('user:permission:' . $this->getUser()->id);
        if ($permission && in_array($slug, $permission)) {
            return $handler->handle($request);
        }

        return $this
            ->response
            ->withStatus(200)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream(message(ErrorCode::PERMISSION_REFUSE)));
    }
}

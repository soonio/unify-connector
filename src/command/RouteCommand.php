<?php

declare(strict_types=1);

namespace unify\connector\command;

use App\Middleware\PermissionMiddleware;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\MiddlewareManager;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\HttpServer\Router\Handler;
use Hyperf\Utils\Str;
use Psr\Container\ContainerInterface;
use unify\contract\AppServiceInterface;

class RouteCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        parent::__construct('route:report');
        $this->container = $container;
        $this->config = $config;
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('获取Http请求的路由信息并通过RPC服务发到中控系统');
    }

    public function handle()
    {
        $factory = $this->container->get(DispatcherFactory::class);
        $router = $factory->getRouter('http');

        [$staticRouters, $variableRouters] = $router->getData();

        $data = [];
        foreach ($staticRouters as $method => $items) {
            foreach ($items as $handler) {
                $this->analyzeHandler($data, 'http', $method, null, $handler);
            }
        }
        foreach ($variableRouters as $method => $items) {
            foreach ($items as $item) {
                if (is_array($item['routeMap'] ?? false)) {
                    foreach ($item['routeMap'] as $routeMap) {
                        $this->analyzeHandler($data, 'http', $method, null, $routeMap[0]);
                    }
                }
            }
        }

        $permissions = [];
        // 遍历路由插入路由表，记录新的插入ID
        foreach ($data as $router) {
            foreach ($router['method'] as $method) {
                $permissions[] = [ 'method' => $method, 'route' => $router['uri'], 'name' => '-' ];
            }
        }

        $this
            ->container
            ->get(AppServiceInterface::class)
            ->permission($permissions);
        $this->info('权限更新成功');
    }

    /**
     * 仅仅获取需要权限认证的路由
     * @param array $data
     * @param string $serverName
     * @param string $method
     * @param string|null $path
     * @param Handler $handler
     */
    protected function analyzeHandler(array &$data, string $serverName, string $method, ?string $path, Handler $handler)
    {
        $uri = $handler->route;
        if (! is_null($path) && ! Str::contains($uri, $path)) {
            return;
        }
        if (is_array($handler->callback)) {
            $action = $handler->callback[0] . '::' . $handler->callback[1];
        } elseif (is_string($handler->callback)) {
            $action = $handler->callback;
        } elseif (is_callable($handler->callback)) {
            $action = 'Closure';
        } else {
            $action = (string) $handler->callback;
        }
        $unique = "{$serverName}|{$action}";
        if (isset($data[$unique])) {
            $data[$unique]['method'][] = $method;
        } else {
            $registedMiddlewares = MiddlewareManager::get($serverName, $uri, $method);
            $middlewares = $this->config->get('middlewares.' . $serverName, []);
            $middlewares = array_merge($middlewares, $registedMiddlewares);
            foreach ($middlewares as $middleware) {
                if ($middleware == PermissionMiddleware::class) { // 仅仅上报受权限控制的路由
                    $data[$unique] = [
                        'method'  => [$method],
                        'uri'     => $uri,
                        'action'  => $action
                    ];
                    break;
                }
            }
        }
    }
}

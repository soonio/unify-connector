<?php

declare(strict_types=1);

namespace unify\connector\command;

use App\Middleware\PermissionMiddleware;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\MiddlewareManager;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\HttpServer\Router\Handler;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Str;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use unify\contract\AppServiceInterface;
use unify\contract\PermissionUniqueSlug;

class RouteCommand extends HyperfCommand
{
    use PermissionUniqueSlug;
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

    /**
     * 接口路由的名称
     * @var string[]
     */
    protected $routeName = [
        'POST /unify/report/menu' => '上报系统菜单'
    ];

    /**
     * @var string 输出为文件时的格式
     */
    protected $filename = 'route.json';

    public function configure()
    {
        parent::configure();
        $this->setDescription('获取Http请求的路由信息并通过RPC服务发到中控系统');

        $this->addOption('out', 'o', InputArgument::OPTIONAL, '仅仅输出到控制台', false);
        $this->addOption('file', 'f', InputArgument::OPTIONAL, '输出路由配置到文件');
        $this->addOption('filedrive', 'd', InputArgument::OPTIONAL, '文件驱动', 'local');
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
                $permissions[] = [
                    'method'    => $method,
                    'route'     => $router['uri'],
                    'name'      => $this->routeName[$method . ' ' . $router['uri']] ?? '-'
                ];
            }
        }

        if ($this->filesystem(true)) {
            if ($this->filesystem()->has($this->filename)) {
                $old = json_decode($this->filesystem()->read($this->filename), true);
                $old = collect($old)->keyBy(function ($item) {
                    return $this->slug($item['method'], $item['route']);
                });
                foreach ($permissions as &$row) {
                    $key = $this->slug($row['method'], $row['route']);
                    if ($old->has($key)) {
                        $row['name'] = $old->get($key)['name'];
                    }
                }
            }
        }

        if ($this->input->getOption('out')) {
            $str = json_encode($permissions, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
            $this->info($str);
            return;
        }
        if ($this->input->getOption('file')) {
            $str = json_encode($permissions, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            if (!$this->filesystem()) return;

            if ($this->filesystem()->has($this->filename)) {
                $choice = [
                    0 => '覆盖原文件',
                    1 => '创建新文件',
                ];
                $select = $this->choice('文件已存在。请选择处理方式.', $choice, 0);
                $key = array_search($select, $choice);
                switch ($key) {
                    case 0:
                        $this->filesystem()->delete($this->filename);
                        break;
                    case 1:
                        $this->filename = str_replace('.json', '-' . time() . '.json', $this->filename);break;
                    default:
                        $this->error('操作异常.');
                }
            }
            $res = $this->filesystem()->write($this->filename, $str);
            $this->info('文件地址为: ' . $this->filesystem()->getConfig()->get('root') . '/' . $this->filename);
            return;
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

    /**
     * @param false $slient
     * @return \League\Flysystem\Filesystem|null
     */
    protected function filesystem(bool $slient=false)
    {
        if (class_exists('Hyperf\Filesystem\FilesystemFactory')) {
            $fd = $this->input->getOption('filedrive');
            return ApplicationContext::getContainer()
                ->get('Hyperf\Filesystem\FilesystemFactory')
                ->get($fd);
        } else {
            $slient || $this->warn('缺少文件驱动, 请安装 hyperf/filesystem!');
            return null;
        }
    }
}

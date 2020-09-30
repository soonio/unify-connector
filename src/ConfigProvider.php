<?php

declare(strict_types=1);

namespace unify\connector;

use unify\connector\command\RouteCommand;
use unify\connector\command\RpcTestCommand;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
                RouteCommand::class,
                RpcTestCommand::class
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'AbstractController',
                    'description' => '通用控制器.',
                    'source' => __DIR__ . '/../publish/AbstractController.php',
                    'destination' => BASE_PATH . '/app/Controller/AbstractController.php',
                ],
                [
                    'id' => 'AuthorizeMiddleware',
                    'description' => '认证中间件.',
                    'source' => __DIR__ . '/../publish/AuthorizeMiddleware.php',
                    'destination' => BASE_PATH . '/app/Middleware/AuthorizeMiddleware.php',
                ],
                [
                    'id' => 'ErrorCode',
                    'description' => '错误码.',
                    'source' => __DIR__ . '/../publish/ErrorCode.php',
                    'destination' => BASE_PATH . '/app/Constants/ErrorCode.php',
                ],
                [
                    'id' => 'Model',
                    'description' => '基础model.',
                    'source' => __DIR__ . '/../publish/Model.php',
                    'destination' => BASE_PATH . '/app/Model/Model.php',
                ],
                [
                    'id' => 'PermissionMiddleware',
                    'description' => '认证中间件.',
                    'source' => __DIR__ . '/../publish/PermissionMiddleware.php',
                    'destination' => BASE_PATH . '/app/Middleware/PermissionMiddleware.php',
                ],
                [
                    'id' => 'RecvExceptionHandler',
                    'description' => '消息接收异常处理情',
                    'source' => __DIR__ . '/../publish/RecvExceptionHandler.php',
                    'destination' => BASE_PATH . '/app/Exception/Handler/RecvExceptionHandler.php',
                ],
                [
                    'id' => 'RequestExceptionHandler',
                    'description' => '请求异常处理器.',
                    'source' => __DIR__ . '/../publish/RequestExceptionHandler.php',
                    'destination' => BASE_PATH . '/app/Exception/Handler/RequestExceptionHandler.php',
                ],

                [
                    'id' => 'aspect',
                    'description' => 'AOP编程，自动在rpc请求中加入验证信息',
                    'source' => __DIR__ . '/../publish/RpcClientRequestAspect.php',
                    'destination' => BASE_PATH . '/app/Aspect/RpcClientRequestAspect.php',
                ],
                [
                    'id' => 'config',
                    'description' => 'rpc服务配置',
                    'source' => __DIR__ . '/../publish/services.php',
                    'destination' => BASE_PATH . '/config/autoload/services.php',
                ],
                [
                    'id' => 'config',
                    'description' => '基本配置',
                    'source' => __DIR__ . '/../publish/unify.php',
                    'destination' => BASE_PATH . '/config/autoload/unify.php',
                ],
                [
                    'id' => 'Unify',
                    'description' => '基础控制器.',
                    'source' => __DIR__ . '/../publish/UnifyController.php',
                    'destination' => BASE_PATH . '/app/Controller/UnifyController.php',
                ],
            ],
        ];
    }
}

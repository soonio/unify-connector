<?php

declare(strict_types=1);

namespace unify\connector;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'AuthMiddleware',
                    'description' => '认证中间件.',
                    'source' => __DIR__ . '/../publish/AuthMiddleware.php',
                    'destination' => BASE_PATH . '/app/Middleware/AuthMiddleware.php',
                ],
                [
                    'id' => 'CommonController',
                    'description' => '通用控制器.',
                    'source' => __DIR__ . '/../publish/CommonController.php',
                    'destination' => BASE_PATH . '/app/Controller/CommonController.php',
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

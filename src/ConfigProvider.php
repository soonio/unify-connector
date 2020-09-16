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
                    'id' => 'config',
                    'description' => 'The config for unify service.',
                    'source' => __DIR__ . '/../publish/unify.php',
                    'destination' => BASE_PATH . '/config/autoload/unify.php',
                ],
                [
                    'id' => 'aspect',
                    'description' => 'The aspect for unify service.',
                    'source' => __DIR__ . '/../publish/RpcClientRequestAspect.php',
                    'destination' => BASE_PATH . '/app/Aspect/RpcClientRequestAspect.php',
                ],
                [
                    'id' => 'RecvExceptionHandler',
                    'description' => 'The exception handler for unify service.',
                    'source' => __DIR__ . '/../publish/RecvExceptionHandler.php',
                    'destination' => BASE_PATH . '/app/Exception/Handler/RecvExceptionHandler.php',
                ]
            ],
        ];
    }
}

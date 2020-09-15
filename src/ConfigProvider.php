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
                    'source' => __DIR__ . '/../publish/AuthAspect.php',
                    'destination' => BASE_PATH . '/app/Aspect',
                ],
            ],
        ];
    }
}

# unify contract

## 发布

```bash
php bin/hyperf.php vendor:publish unify/connector
```

## 配置

- 配置文件

- 配置AOP

    > 无需特殊配置

- 配置异常处理

    需要手动在`config/autoload/exceptions.php`注册
    ```php
    return [
        'handler' => [
            'http' => [
                // ...其他异常handler
                App\Exception\Handler\RecvExceptionHandler::class,
                App\Exception\Handler\RequestExceptionHandler::class,
                // ...其他异常handler
                App\Exception\Handler\AppExceptionHandler::class,
            ],
        ],
    ];

    ```

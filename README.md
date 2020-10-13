# unify contract

## 发布

```bash
php bin/hyperf.php vendor:publish unify/connector

该扩展依赖文件系统，需要同时发布文件系统配置
php bin/hyperf.php vendor:publish hyperf/filesystem
```

> 对于发布失败的文件，可以通过手动修改  
> 或者命令强制覆盖:   

  ```bash
  /bin/cp -f vendor/unify/connector/publish/AbstractController.php app/Controller/AbstractController.php
  /bin/cp -f vendor/unify/connector/publish/ErrorCode.php app/Constants/ErrorCode.php
  /bin/cp -f vendor/unify/connector/publish/Model.php app/Model/Model.php 
  ```

## 配置

- 配置文件
  - 配置`unify.php`中的应用ID，密钥
  - 配置`services.php`中的服务地址

  .env
  ```dotenv
  UNIFY_RPC_APP_ID=888
  UNIFY_RPC_APP_KEY=73ce3e2f15ff247e0f362e4417a202012
  UNIFY_RPC_NODES=127.0.0.1:9518
  ```

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

## 路由管理

- 输出路由到控制台(不上报)
```bash
php bin/hyperf.php route:report -o true
```

- 输出路由到文件(不上报)
```bash
php bin/hyperf.php route:report -f true

# 需要文件驱动
composer require hyperf/filesystem
php bin/hyperf.php vendor:publish hyperf/filesystem
```

- 路由上报
```bash
php bin/hyperf.php route:report
```

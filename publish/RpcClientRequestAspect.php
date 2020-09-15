<?php

declare(strict_types=1);

namespace App\Aspect;

use Hyperf\Config\Config;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Hyperf\Rpc\Context;
use Hyperf\Utils\ApplicationContext;
use unify\contract\Access;

/**
 * Class FooAspect
 * @Aspect
 * @package App\Aspect
 */
class RpcClientRequestAspect extends AbstractAspect
{
    public $classes = [
        "Hyperf\RpcClient\ServiceClient::__request",
    ];

    /**
     * 切入rpc请求客户端，在发送请求前，创建认证凭证通过协程发送给目标服务
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     * @throws Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // TODO 缺点：无法识别请求的是那个rpc服务，认证信息全局发送
        $container = ApplicationContext::getContainer();
        $config = $container->get(Config::class)->get('unify');
        $container->get(Context::class)->set(Access::NAME, Access::credentials($config['appid'], $config['appkey']));
        return $proceedingJoinPoint->process();
    }
}

<?php

declare(strict_types=1);

namespace App\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;

/**
 * Class FooAspect
 * @Aspect
 * @package App\Aspect
 */
class AuthAspect extends AbstractAspect
{
    public $classes = [
        "Hyperf\RpcClient\ServiceClient::__request",
    ];

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     * @throws Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // TODO 生成判断服务名称，生成验证信息

        return $proceedingJoinPoint->process();
    }
}

<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Rpc\Exception\RecvException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class RecvExceptionHandler
 * @package App\Exception\Handler
 */
class RecvExceptionHandler extends ExceptionHandler
{
    /**
     * @param Throwable|RecvException $throwable
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        // TODO $throwable 当前系统的异常信息
        // TODO $throwable->getThrowable()， 中心系统抛出的错误信息

        return $response
            ->withStatus(500)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream('Rpc服务异常，请稍后重试~'));
    }

    /**
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof RecvException;
    }
}

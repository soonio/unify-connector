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
use Hyperf\RpcClient\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class RequestExceptionHandler
 * @package App\Exception\Handler
 */
class RequestExceptionHandler extends ExceptionHandler
{
    /**
     * @param Throwable|RequestException $throwable
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $this->stopPropagation();

        return $response
            ->withStatus(500)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream( 'Rpc' .': ' . $throwable->getMessage()));
    }

    /**
     * @param Throwable $throwable
     * @return bool
     */
    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof RequestException;
    }
}

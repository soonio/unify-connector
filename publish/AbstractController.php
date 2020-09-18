<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Controller;

use App\Constants\ErrorCode;
use App\Model\Model;
use Hyperf\Contract\LengthAwarePaginatorInterface;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;


    public function __construct(ContainerInterface $container, RequestInterface $request, ResponseInterface $response)
    {
        $this->container = $container;
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * 构建统一的分页数据响格式
     * @param LengthAwarePaginatorInterface $paginate
     * @param callable|null $callback 对数据列表进行额外的处理
     * @return array
     */
    public function paginate(LengthAwarePaginatorInterface $paginate, callable $callback=null)
    {
        return [
            'items'     => is_callable($callback) ? $callback($paginate->items()): $paginate->items(),
            'paginate'  => [
                'page'  => $paginate->currentPage(),
                'size'  => $paginate->perPage(),
                'total' => $paginate->total(),
            ],
        ];
    }

    /**
     * 分页限制，默认限制100条
     * @param int $max
     * @return int
     */
    public function limit($max=100)
    {
        $size = (int) $this->request->query('limit', 10);
        return $size <= $max ? $size : $max;
    }

    /**
     * 快速构建成功消息
     * @param array|null $data
     * @param null $message
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function success(array $data=null, $message=null)
    {
        return $this->response->json(message(ErrorCode::SUCCESS, $data, $message, false));
    }

    /**
     * 快速构建失败消息
     * @param int $code
     * @param null $message
     * @param array|null $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function failure(int $code, $message=null, array $data=null)
    {
        return $this->response->json(message($code, $data, $message, false));
    }

    /**
     * 检查结果并响应
     * @param $res
     * @param int $code
     * @param string|null $msg
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function check($res, int $code=ErrorCode::OPERATE_FAILURE, string $msg=null)
    {
        return (bool)$res ? $this->success() : $this->failure($code, $msg);
    }

    /**
     * 保存数据到模型对应的数据表中
     * @param Model $model
     * @param array $data
     * @param callable|null $callable
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function save(Model $model, array $data, callable $callable=null)
    {
        if ($model->fill($data)->save()) {
            if (is_callable($callable)) {
                return $callable($model);
            } else {
                return $this->success($model->toArray());
            }
        } else {
            return $this->failure(ErrorCode::OPERATE_FAILURE);
        }
    }
}

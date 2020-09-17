<?php

declare(strict_types=1);

use App\Constants\ErrorCode;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Codec\Json;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;

if (!function_exists('message')) {
    /**
     * 构建标准的http请求数据格式
     * @param int $code
     * @param array|null $data
     * @param string|null $msg
     * @param bool $json
     * @return array|string
     */
    function message(int $code, array $data=null, string $msg=null, bool $json=true)
    {
        $info = ['code' => $code, 'msg' => $msg ?? ErrorCode::getMessage($code)];
        is_null($data) || $info['data'] = $data;
        return $json ? Json::encode($info) : $info;
    }
}

if (!function_exists('load')) {
    /**
     * 属性加载器,快速给对象的public属性批量赋值
     * @param object $object
     * @param array $attributes
     * @param bool $strict
     * @return object|null
     */
    function load(object $object, array $attributes = [], $strict = false)
    {
        try {
            $rc = new \ReflectionClass($object);
        } catch (ReflectionException $e) {
            return null;
        }
        $publicProperties = $rc->getProperties(\ReflectionProperty::IS_PUBLIC);
        $propertyNames = [];
        foreach ($publicProperties as $property) {
            $propertyNames[] = $property->name;
        }
        foreach ($attributes as $key => $value) {
            if (in_array($key, $propertyNames)) {
                $object->$key = $value;
            } else {
                if ($strict) {
                    return null;
                }
            }
        }
        return $object;
    }
}

if (!function_exists('unique')) {
    /**
     * 生成唯一的密钥
     * @param string $custom
     * @return string
     */
    function unique(string $custom='')
    {
        return md5(uniqid(microtime(), true) . $custom);
    }
}

if (!function_exists('html')) {
    /**
     * 自定义响应html的便捷方法
     * @param string $contents
     * @return ResponseInterface
     */
    function html(string $contents)
    {
        return Context::get(ResponseInterface::class)
            ->withStatus(200)
            ->withHeader('content-type', 'text/html')
            ->withBody(new SwooleStream($contents));
    }
}

if (!function_exists('icon')) {
    /**
     * 自定义响应html的便捷方法
     * @param string $contents
     * @return ResponseInterface
     */
    function icon(string $contents)
    {
        return Context::get(ResponseInterface::class)
            ->withStatus(200)
            ->withHeader('content-type', 'image/x-icon')
            ->withBody(new SwooleStream($contents));
    }
}

if (!function_exists('to_tree')) {
    /**
     * 获取对应的对象并执行回调
     * @param array $rows
     * @param string $id
     * @param string $pid
     * @param string $sonKey
     * @return array|mixed
     */
    function to_tree(array $rows, string $id='id', string $pid='pid', string $sonKey='children')
    {
        $tree = [];
        foreach ($rows as $row) {
            $tree[$row[$id]] = $row;
        }
        foreach ($tree as $item) {
            $tree[$item[$pid]][$sonKey][$item[$id]] = &$tree[$item[$id]];
        }
        return isset($tree[0][$sonKey]) ? $tree[0][$sonKey] : [];
    }
}
if (!function_exists('format_tree')) {

    /**
     * 格式化树形结构，从对象格式化成列表
     * @param array $tree
     * @param string $sonKey
     * @return array
     */
    function format_tree(array $tree, string $sonKey='children')
    {
        $data = array_values($tree);
        foreach ($data as &$sTree) {
            if (isset($sTree[$sonKey])) {
                $sTree[$sonKey] = format_tree($sTree[$sonKey]);
            }
        }
        return $data;
    }
}


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


use unify\contract\AppServiceInterface;
use unify\contract\UnifyServiceInterface;
use unify\contract\UserServiceInterface;


$services = [
    ['AppService', AppServiceInterface::class],
    ['UnifyService', UnifyServiceInterface::class],
    ['UserService', UserServiceInterface::class],
];


return [
    'consumers' => array_map(function ($item){
        return [
            'name'    => $item[0],
            'service' => $item[1],
            'id'      => $item[1],
            'protocol'=> 'jsonrpc',
            // 需要根据实际情况配置采用consul方式或者直接配置nodes配置服务
            'nodes' => [
                 ['host' => '127.0.0.1', 'port' => 9502],
            ],
            'options' => [
                'connect_timeout'   => 5.0,
                'recv_timeout'      => 5.0,
                'settings'          => [
                    'open_eof_split'        => true,
                    'package_eof'           => "\r\n",
                ],
                // 当使用 JsonRpcPoolTransporter 时会用到以下配置
                'pool' => [
                    'min_connections'   => 3,
                    'max_connections'   => 32,
                    'connect_timeout'   => 10.0,
                    'wait_timeout'      => 3.0,
                    'heartbeat'         => -1,
                    'max_idle_time'     => 60.0,
                ],
            ],
        ];
    }, $services),
];

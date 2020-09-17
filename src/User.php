<?php

declare(strict_types=1);

namespace unify\connector;

/**
 * Class User
 * @property-read int $id 用户ID
 * @property-read int $username 用户名
 * @property-read int $nickname 用户昵称
 * @property-read int $email 用户邮箱
 * @property-read int $status 用户状态
 * @property-read int $remark 用户签名/备注
 * @package unify\connector
 */
class User extends Getter {}


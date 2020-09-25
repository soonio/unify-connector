<?php

declare(strict_types=1);

namespace unify\connector;

/**
 * Class User
 * @property-read int $id 用户ID
 * @property-read string $username 用户名
 * @property-read string $nickname 用户昵称
 * @property-read string $email 用户邮箱
 * @property-read int $status 用户状态
 * @property-read string $remark 用户签名/备注
 * @property-read string $token 当前用户的通行签名
 * @property-read bool isOwner 是否为应用Owner
 * @property-read bool isSuperAdmin 是否为超级管理器
 * @property-read string avatar 头像地址
 * @package unify\connector
 */
class User extends Getter {}


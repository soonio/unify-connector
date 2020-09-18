<?php

declare(strict_types=1);

namespace unify\connector;


use Hyperf\Utils\Context;

/**
 * Trait UserState
 * @package App\Service
 */
trait UserState
{
    /**
     * 获取用户(一般在控制器中获取)
     */
    public function getUser(): ?User
    {
        return Context::get('currentUser');
    }

    /**
     * 设置用户(一般在中间件认证中设置)
     * @param User $user
     */
    public function setUser(User $user)
    {
        Context::set('currentUser', $user);
    }
}

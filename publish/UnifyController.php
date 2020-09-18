<?php
declare(strict_types=1);

namespace App\Controller;

use Hyperf\Utils\Arr;
use unify\connector\UserState;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use unify\contract\UserServiceInterface;

/**
 * @Controller()
 */
class UnifyController extends CommonController
{
    use UserState;

    /**
     * @Inject()
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * 缓存用户的基本数据
     * @RequestMapping(path="/unify/handle", methods="get")
     * @return ResponseInterface
     * @throws InvalidArgumentException
     */
    public function login()
    {
        $cache = $this->container->get(CacheInterface::class);

        $token = $this->request->query('token');
        $user = $this->userService->user($token);

        $token = md5($token);
        $uid = $user['id'];

        $ttl = 86400 * 7;

        $cache->set('token:user:' . $token, $user, $ttl - 30);

        $role = $this->userService->role($uid);
        $cache->set('user:role:' . $uid, $role, $ttl);

        $menu = $this->userService->menu($uid);
        $cache->set('user:menu:' . $uid, $menu, $ttl);

        $permission = $this->userService->permission($uid);
        $cache->set('user:permission:' . $uid, $permission, $ttl);

        return $this->success(compact('token', 'ttl'));
    }

    /**
     * 退出登录
     * @return ResponseInterface
     * @throws InvalidArgumentException
     */
    public function logout()
    {
        $cache = $this->container->get(CacheInterface::class);
        $token = Arr::first($this->request->getHeader('Authorization'));
        $cache->delete('token:user:' . $token);
        $cache->delete('user:role:' . $this->getUser()->id);
        $cache->delete('user:menu:' . $this->getUser()->id);
        $cache->delete('user:permission:' . $this->getUser()->id);
        return $this->success();
    }

    /**
     * @RequestMapping(path="/unify/role", methods="get")
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function role()
    {
        $data = $this
            ->container
            ->get(CacheInterface::class)
            ->get('user:role:' . $this->getUser()->id);
        return $this->success($data);
    }

    /**
     * @RequestMapping(path="/unify/menu", methods="get")
     * @return ResponseInterface
     * @throws InvalidArgumentException
     */
    public function menu()
    {
        $data = $this
            ->container
            ->get(CacheInterface::class)
            ->get('user:menu:' . $this->getUser()->id);
        return $this->success($data);
    }

    /**
     * @RequestMapping(path="/unify/permission", methods="get")
     * @return ResponseInterface
     * @throws InvalidArgumentException
     */
    public function permission()
    {
        $data = $this
            ->container
            ->get(CacheInterface::class)
            ->get('user:permission:' . $this->getUser()->id);
        return $this->success($data);
    }
    // 权限上报

    // 路由上报

}

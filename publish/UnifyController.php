<?php
declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use unify\connector\UserState;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use unify\contract\AppServiceInterface;
use unify\contract\UserServiceInterface;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * @Controller()
 */
class UnifyController extends AbstractController
{
    use UserState;

    /**
     * @Inject()
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @Inject()
     * @var AppServiceInterface
     */
    protected $appService;

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

        $user['token'] = $token; // 把新token同时进行存储
        $cache->set('token:user:' . $token, $user, $ttl);

        $role = $this->userService->role($uid);
        $cache->set('user:role:' . $uid, $role, $ttl);

        $menu = $this->userService->menu($uid);
        $cache->set('user:menu:' . $uid, $menu, $ttl);

        $permission = $this->userService->permission($uid);
        $cache->set('user:permission:' . $uid, $permission, $ttl);

        return $this->success(compact('token', 'ttl', 'role', 'menu', 'permission'));
    }

    /**
     * 退出登录
     * @Middlewares({
     *     @Middleware(AuthMiddleware::class)
     * })
     * @RequestMapping(path="/unify/logout", methods="get")
     * @return ResponseInterface
     * @throws InvalidArgumentException
     */
    public function logout()
    {
        $cache = $this->container->get(CacheInterface::class);
        $cache->delete('token:user:' . $this->getUser()->token);
        $cache->delete('user:role:' . $this->getUser()->id);
        $cache->delete('user:menu:' . $this->getUser()->id);
        $cache->delete('user:permission:' . $this->getUser()->id);
        return $this->success();
    }

    /**
     * 获取用户信息
     * @Middlewares({
     *     @Middleware(AuthMiddleware::class)
     * })
     * @RequestMapping(path="/unify/user", methods="get")
     * @return ResponseInterface
     */
    public function info()
    {
        $user = $this->getUser()->toArray();
        unset($user['token']);
        return $this->success($user);
    }

    /**
     * @Middlewares({
     *     @Middleware(AuthMiddleware::class)
     * })
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
     * @Middlewares({
     *     @Middleware(AuthMiddleware::class)
     * })
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
     * @Middlewares({
     *     @Middleware(AuthMiddleware::class)
     * })
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

    /**
     * @Middlewares({
     *     @Middleware(AuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     * @RequestMapping(path="/unify/report/memu", methods="post")
     * @return ResponseInterface
     */
    public function report()
    {
        $data = $this->request->post();

        $this->appService->menu($data);
        return $this->success();
    }
}

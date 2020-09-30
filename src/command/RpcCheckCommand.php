<?php

declare(strict_types=1);

namespace unify\connector\command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;
use unify\contract\UnifyServiceInterface;

class RpcTestCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('rpc:test');
    }

    public function configure()
    {
        parent::configure();

        $this->setDescription('RPC配置查看及连通性测试');
    }

    public function handle()
    {
        $rpc = config('unify');
        $rpc['nodes'] = explode(',', env('UNIFY_RPC_NODES', ''));
        $config = json_encode($rpc, JSON_PRETTY_PRINT);
        $res = ApplicationContext::getContainer()->get(UnifyServiceInterface::class)->front();
        $this->line("Rpc config:\n{$config}", 'info');
        $this->line("RPC front Address:\n{$res}", 'info');
    }
}

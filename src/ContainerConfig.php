<?php

namespace Dms\Cli\Expressive;

use Dms\Core\Ioc\IIocContainer;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Support\Facades\Schema;

class ContainerConfig
{
    public function define(IIocContainer $container)
    {
        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, MigrationRepositoryInterface::class, function () use ($container) {
            return new DatabaseMigrationRepository($container->get(ConnectionResolverInterface::class), 'migrations');
        });

        $container->bindCallback(IIocContainer::SCOPE_SINGLETON, ConnectionResolverInterface::class, function () use ($container) {
            $settings = [
                'driver' => getenv('driver'),
                'host' => getenv('host'),
                'database' => getenv('database'),
                'username' => getenv('username'),
                'password' => getenv('password'),
                'collation' => getenv('collation'),
                'prefix' => ''
            ];

            $connFactory = new ConnectionFactory($container->getIlluminateContainer());
            $conn = $connFactory->make($settings);
            $resolver = new ConnectionResolver();
            $resolver->addConnection('default', $conn);
            $resolver->setDefaultConnection('default');
            Schema::setFacadeApplication($container->getIlluminateContainer());

            return $resolver;
        });

        // used with migrations
        $container->alias(ConnectionResolverInterface::class, 'db');
    }
}

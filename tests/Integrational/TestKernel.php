<?php

declare(strict_types=1);

namespace Tests\Integrational;

use ChamberOrchestra\MetaBundle\ChamberOrchestraMetaBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Doctrine\ORM\EntityManagerInterface;

final class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new ChamberOrchestraMetaBundle(),
        ];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'secret' => 'test_secret',
            'test' => true,
        ]);

        $dbalConfig = isset($_ENV['DATABASE_URL'])
            ? ['url' => $_ENV['DATABASE_URL'], 'server_version' => '17']
            : [
                'driver' => 'pdo_pgsql',
                'host' => '/var/run/postgresql',
                'user' => get_current_user(),
                'dbname' => 'meta_bundle_test',
                'server_version' => '17',
            ];

        $container->extension('doctrine', [
            'dbal' => $dbalConfig,
            'orm' => [
                'entity_managers' => [
                    'default' => [
                        'mappings' => [
                            'Tests' => [
                                'type' => 'attribute',
                                'dir' => '%kernel.project_dir%/tests/Integrational/Entity',
                                'prefix' => 'Tests\\Integrational\\Entity',
                                'alias' => 'Tests',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $container->services()
            ->alias(EntityManagerInterface::class, 'doctrine.orm.entity_manager')
            ->public();
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2);
    }
}

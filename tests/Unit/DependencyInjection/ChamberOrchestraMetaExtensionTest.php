<?php

declare(strict_types=1);

namespace Tests\Unit\DependencyInjection;

use ChamberOrchestra\MetaBundle\DependencyInjection\ChamberOrchestraMetaExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ChamberOrchestraMetaExtensionTest extends TestCase
{
    public function testLoadDoesNotRegisterAnyServices(): void
    {
        $container = new ContainerBuilder();
        $extension = new ChamberOrchestraMetaExtension();

        $extension->load([], $container);

        $serviceIds = array_filter(
            $container->getServiceIds(),
            static fn(string $id): bool => str_starts_with($id, 'ChamberOrchestra\\MetaBundle\\'),
        );

        self::assertSame([], $serviceIds);
    }

    public function testExtensionAlias(): void
    {
        $extension = new ChamberOrchestraMetaExtension();

        self::assertSame('chamber_orchestra_meta', $extension->getAlias());
    }
}

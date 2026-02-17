<?php

declare(strict_types=1);

namespace Tests\Unit;

use ChamberOrchestra\MetaBundle\ChamberOrchestraMetaBundle;
use ChamberOrchestra\MetaBundle\DependencyInjection\ChamberOrchestraMetaExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class ChamberOrchestraMetaBundleTest extends TestCase
{
    public function testExtendsBundle(): void
    {
        $bundle = new ChamberOrchestraMetaBundle();

        self::assertInstanceOf(Bundle::class, $bundle);
    }

    public function testContainerExtensionIsCorrectClass(): void
    {
        $bundle = new ChamberOrchestraMetaBundle();

        self::assertInstanceOf(ChamberOrchestraMetaExtension::class, $bundle->getContainerExtension());
    }

    public function testBundleName(): void
    {
        $bundle = new ChamberOrchestraMetaBundle();

        self::assertSame('ChamberOrchestraMetaBundle', $bundle->getName());
    }
}

<?php

declare(strict_types=1);

namespace Tests\Integrational;

use ChamberOrchestra\MetaBundle\ChamberOrchestraMetaBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class BundleBootTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        \restore_exception_handler();
    }

    public function testKernelBootsSuccessfully(): void
    {
        self::bootKernel();

        self::assertSame('test', self::$kernel->getEnvironment());
    }

    public function testMetaBundleIsRegistered(): void
    {
        self::bootKernel();

        $bundles = self::$kernel->getBundles();

        self::assertArrayHasKey('ChamberOrchestraMetaBundle', $bundles);
        self::assertInstanceOf(ChamberOrchestraMetaBundle::class, $bundles['ChamberOrchestraMetaBundle']);
    }
}

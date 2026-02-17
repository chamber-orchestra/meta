<?php

declare(strict_types=1);

namespace Tests\Integrational;

use ChamberOrchestra\MetaBundle\Entity\Helper\RobotsBehaviour;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integrational\Entity\TestArticle;

final class DoctrineMetadataTest extends KernelTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        restore_exception_handler();
    }

    public function testTestArticleMetadataIsLoaded(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $metadata = $em->getClassMetadata(TestArticle::class);

        self::assertSame('test_article', $metadata->getTableName());
    }

    public function testMetaTraitFieldsAreInMetadata(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $metadata = $em->getClassMetadata(TestArticle::class);

        $expectedFields = ['title', 'metaTitle', 'metaImagePath', 'metaDescription', 'metaKeywords', 'robotsBehaviour'];

        foreach ($expectedFields as $field) {
            self::assertTrue(
                $metadata->hasField($field),
                sprintf('Field "%s" is missing from metadata', $field),
            );
        }
    }

    public function testRobotsBehaviourEnumTypeMapping(): void
    {
        self::bootKernel();
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $metadata = $em->getClassMetadata(TestArticle::class);

        $mapping = $metadata->fieldMappings['robotsBehaviour'];

        self::assertSame(Types::SMALLINT, $mapping->type);
        self::assertSame(RobotsBehaviour::class, $mapping->enumType);
    }
}

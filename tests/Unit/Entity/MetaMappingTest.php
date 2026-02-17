<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use ChamberOrchestra\MetaBundle\Entity\Helper\RobotsBehaviour;
use ChamberOrchestra\MetaBundle\Entity\MetaInterface;
use ChamberOrchestra\MetaBundle\Entity\MetaTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use PHPUnit\Framework\TestCase;

#[ORM\Entity]
class MetaMappingTestEntity implements MetaInterface
{
    use MetaTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}

final class MetaMappingTest extends TestCase
{
    private ClassMetadata $metadata;

    protected function setUp(): void
    {
        $driver = new AttributeDriver([__DIR__]);
        $metadata = new ClassMetadata(MetaMappingTestEntity::class);
        $metadata->initializeReflection(new \Doctrine\Persistence\Mapping\RuntimeReflectionService());
        $driver->loadMetadataForClass(MetaMappingTestEntity::class, $metadata);
        $this->metadata = $metadata;
    }

    public function testTitleColumnMapping(): void
    {
        $mapping = $this->metadata->fieldMappings['title'];

        self::assertSame(Types::STRING, $mapping->type);
        self::assertSame(255, $mapping->length);
        self::assertTrue($mapping->nullable);
    }

    public function testMetaTitleColumnMapping(): void
    {
        $mapping = $this->metadata->fieldMappings['metaTitle'];

        self::assertSame(Types::STRING, $mapping->type);
        self::assertSame(255, $mapping->length);
        self::assertTrue($mapping->nullable);
    }

    public function testMetaImagePathColumnMapping(): void
    {
        $mapping = $this->metadata->fieldMappings['metaImagePath'];

        self::assertSame(Types::STRING, $mapping->type);
        self::assertSame(255, $mapping->length);
        self::assertTrue($mapping->nullable);
    }

    public function testMetaDescriptionColumnMapping(): void
    {
        $mapping = $this->metadata->fieldMappings['metaDescription'];

        self::assertSame(Types::TEXT, $mapping->type);
        self::assertTrue($mapping->nullable);
    }

    public function testMetaKeywordsColumnMapping(): void
    {
        $mapping = $this->metadata->fieldMappings['metaKeywords'];

        self::assertSame(Types::STRING, $mapping->type);
        self::assertSame(255, $mapping->length);
        self::assertTrue($mapping->nullable);
    }

    public function testRobotsBehaviourColumnMapping(): void
    {
        $mapping = $this->metadata->fieldMappings['robotsBehaviour'];

        self::assertSame(Types::SMALLINT, $mapping->type);
        self::assertFalse($mapping->nullable);
        self::assertSame(RobotsBehaviour::class, $mapping->enumType);
    }

    public function testAllExpectedFieldsAreMapped(): void
    {
        $expectedFields = ['id', 'title', 'metaTitle', 'metaImagePath', 'metaDescription', 'metaKeywords', 'robotsBehaviour'];

        self::assertSame($expectedFields, array_keys($this->metadata->fieldMappings));
    }
}

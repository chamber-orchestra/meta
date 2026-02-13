<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use ChamberOrchestra\MetaBundle\Entity\Helper\RobotsBehaviour;
use ChamberOrchestra\MetaBundle\Entity\MetaInterface;
use ChamberOrchestra\MetaBundle\Entity\MetaTrait;
use PHPUnit\Framework\TestCase;

final class MetaTraitTest extends TestCase
{
    private function createEntity(): MetaInterface
    {
        return new class implements MetaInterface {
            use MetaTrait;

            public function setTitle(?string $title): void
            {
                $this->title = $title;
            }

            public function setMetaTitle(?string $metaTitle): void
            {
                $this->metaTitle = $metaTitle;
            }

            public function setMetaDescription(?string $metaDescription): void
            {
                $this->metaDescription = $metaDescription;
            }

            public function setMetaKeywords(?string $metaKeywords): void
            {
                $this->metaKeywords = $metaKeywords;
            }

            public function setRobotsBehaviour(int $robotsBehaviour): void
            {
                $this->robotsBehaviour = $robotsBehaviour;
            }

            public function setMetaImagePath(?string $path): void
            {
                $this->metaImagePath = $path;
            }
        };
    }

    public function testDefaultValues(): void
    {
        $entity = $this->createEntity();

        self::assertNull($entity->getTitle());
        self::assertNull($entity->getMetaTitle());
        self::assertNull($entity->getMetaDescription());
        self::assertNull($entity->getMetaKeywords());
        self::assertNull($entity->getMetaImage());
        self::assertNull($entity->getMetaImagePath());
        self::assertSame(RobotsBehaviour::IndexNoFollow->value, $entity->getRobotsBehaviour());
    }

    public function testGettersReturnSetValues(): void
    {
        $entity = $this->createEntity();
        $entity->setTitle('Page Title');
        $entity->setMetaTitle('SEO Title');
        $entity->setMetaDescription('SEO Description');
        $entity->setMetaKeywords('key1, key2');

        self::assertSame('Page Title', $entity->getTitle());
        self::assertSame('SEO Title', $entity->getMetaTitle());
        self::assertSame('SEO Description', $entity->getMetaDescription());
        self::assertSame('key1, key2', $entity->getMetaKeywords());
    }

    public function testGetFormattedRobotsBehaviourDefault(): void
    {
        $entity = $this->createEntity();

        self::assertSame('index, nofollow', $entity->getFormattedRobotsBehaviour());
    }

    public function testGetFormattedRobotsBehaviourCustom(): void
    {
        $entity = $this->createEntity();
        $entity->setRobotsBehaviour(RobotsBehaviour::NoIndexNoFollow->value);

        self::assertSame('noindex, nofollow', $entity->getFormattedRobotsBehaviour());
    }

    public function testGetMetaWithAllValues(): void
    {
        $entity = $this->createEntity();
        $entity->setTitle('Page Title');
        $entity->setMetaTitle('SEO Title');
        $entity->setMetaDescription('Description text');
        $entity->setMetaKeywords('key1, key2');
        $entity->setMetaImagePath('/images/meta.jpg');

        $meta = $entity->getMeta();

        self::assertSame('Page Title', $meta['pageTitle']);
        self::assertSame('SEO Title', $meta['title']);
        self::assertSame('/images/meta.jpg', $meta['image']);
        self::assertSame('Description text', $meta['description']);
        self::assertSame('key1, key2', $meta['keywords']);
    }

    public function testGetMetaWithNullValues(): void
    {
        $entity = $this->createEntity();

        $meta = $entity->getMeta();

        self::assertNull($meta['pageTitle']);
        self::assertNull($meta['title']);
        self::assertNull($meta['image']);
        self::assertNull($meta['description']);
        self::assertNull($meta['keywords']);
    }

    public function testGetMetaStripsHtmlFromDescription(): void
    {
        $entity = $this->createEntity();
        $entity->setMetaDescription('<p>Hello <strong>world</strong></p>');

        $meta = $entity->getMeta();

        self::assertSame('Hello world', $meta['description']);
    }

    public function testGetMetaPreservesNullDescription(): void
    {
        $entity = $this->createEntity();
        $entity->setMetaDescription(null);

        $meta = $entity->getMeta();

        self::assertNull($meta['description']);
    }

    public function testGetMetaImagePath(): void
    {
        $entity = $this->createEntity();
        $entity->setMetaImagePath('/uploads/seo/image.png');

        self::assertSame('/uploads/seo/image.png', $entity->getMetaImagePath());
    }
}

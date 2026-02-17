<?php

declare(strict_types=1);

namespace Tests\Unit\Entity;

use ChamberOrchestra\MetaBundle\Entity\Helper\RobotsBehaviour;
use ChamberOrchestra\MetaBundle\Entity\MetaInterface;
use ChamberOrchestra\MetaBundle\Entity\MetaTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

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

            public function setRobotsBehaviour(RobotsBehaviour $robotsBehaviour): void
            {
                $this->robotsBehaviour = $robotsBehaviour;
            }

            public function setMetaImagePath(?string $path): void
            {
                $this->metaImagePath = $path;
            }

            public function setMetaImage(?File $file): void
            {
                $this->metaImage = $file;
            }
        };
    }

    public function testEntityImplementsMetaInterface(): void
    {
        self::assertInstanceOf(MetaInterface::class, $this->createEntity());
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
        self::assertSame(RobotsBehaviour::IndexNoFollow, $entity->getRobotsBehaviour());
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

    #[DataProvider('robotsBehaviourFormatProvider')]
    public function testGetFormattedRobotsBehaviour(RobotsBehaviour $case, string $expected): void
    {
        $entity = $this->createEntity();
        $entity->setRobotsBehaviour($case);

        self::assertSame($expected, $entity->getFormattedRobotsBehaviour());
    }

    public static function robotsBehaviourFormatProvider(): iterable
    {
        yield 'IndexFollow' => [RobotsBehaviour::IndexFollow, 'index, follow'];
        yield 'IndexNoFollow' => [RobotsBehaviour::IndexNoFollow, 'index, nofollow'];
        yield 'NoIndexFollow' => [RobotsBehaviour::NoIndexFollow, 'noindex, follow'];
        yield 'NoIndexNoFollow' => [RobotsBehaviour::NoIndexNoFollow, 'noindex, nofollow'];
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

    public function testGetMetaDoesNotContainRobotsKey(): void
    {
        $entity = $this->createEntity();

        self::assertArrayNotHasKey('robots', $entity->getMeta());
    }

    public function testGetMetaStripsHtmlFromDescription(): void
    {
        $entity = $this->createEntity();
        $entity->setMetaDescription('<p>Hello <strong>world</strong></p>');

        self::assertSame('Hello world', $entity->getMeta()['description']);
    }

    public function testGetMetaPreservesHtmlEntitiesInDescription(): void
    {
        $entity = $this->createEntity();
        $entity->setMetaDescription('Hello &amp; <strong>world</strong>');

        self::assertSame('Hello &amp; world', $entity->getMeta()['description']);
    }

    public function testGetMetaStripsScriptTags(): void
    {
        $entity = $this->createEntity();
        $entity->setMetaDescription('<script>alert(1)</script>Description');

        $description = $entity->getMeta()['description'];

        self::assertStringNotContainsString('<script>', $description);
        self::assertSame('alert(1)Description', $description);
    }

    public function testGetMetaPreservesNullDescription(): void
    {
        $entity = $this->createEntity();
        $entity->setMetaDescription(null);

        self::assertNull($entity->getMeta()['description']);
    }

    public function testGetMetaImagePath(): void
    {
        $entity = $this->createEntity();
        $entity->setMetaImagePath('/uploads/seo/image.png');

        self::assertSame('/uploads/seo/image.png', $entity->getMetaImagePath());
    }

    public function testGetMetaImage(): void
    {
        $entity = $this->createEntity();

        self::assertNull($entity->getMetaImage());

        $file = new File(__FILE__);
        $entity->setMetaImage($file);

        self::assertSame($file, $entity->getMetaImage());
    }
}

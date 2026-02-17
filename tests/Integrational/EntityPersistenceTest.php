<?php

declare(strict_types=1);

namespace Tests\Integrational;

use ChamberOrchestra\MetaBundle\Entity\Helper\RobotsBehaviour;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integrational\Entity\TestArticle;

final class EntityPersistenceTest extends KernelTestCase
{
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);

        $schemaTool = new SchemaTool($this->em);
        $schemaTool->createSchema([$this->em->getClassMetadata(TestArticle::class)]);
    }

    protected function tearDown(): void
    {
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropSchema([$this->em->getClassMetadata(TestArticle::class)]);

        parent::tearDown();
        \restore_exception_handler();
    }

    public function testPersistAndRetrieveWithAllFields(): void
    {
        $article = (new TestArticle())
            ->setTitle('Test Page')
            ->setMetaTitle('SEO Title')
            ->setMetaDescription('<p>Description with <b>HTML</b></p>')
            ->setMetaKeywords('php, symfony')
            ->setMetaImagePath('/images/test.jpg')
            ->setRobotsBehaviour(RobotsBehaviour::NoIndexNoFollow);

        $this->em->persist($article);
        $this->em->flush();

        $id = $article->getId();
        self::assertNotNull($id);

        $this->em->clear();

        $loaded = $this->em->find(TestArticle::class, $id);

        self::assertNotNull($loaded);
        self::assertSame('Test Page', $loaded->getTitle());
        self::assertSame('SEO Title', $loaded->getMetaTitle());
        self::assertSame('<p>Description with <b>HTML</b></p>', $loaded->getMetaDescription());
        self::assertSame('php, symfony', $loaded->getMetaKeywords());
        self::assertSame('/images/test.jpg', $loaded->getMetaImagePath());
        self::assertSame(RobotsBehaviour::NoIndexNoFollow, $loaded->getRobotsBehaviour());
    }

    public function testPersistWithDefaultValues(): void
    {
        $article = new TestArticle();

        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();

        $loaded = $this->em->find(TestArticle::class, $article->getId());

        self::assertNotNull($loaded);
        self::assertNull($loaded->getTitle());
        self::assertNull($loaded->getMetaTitle());
        self::assertNull($loaded->getMetaDescription());
        self::assertNull($loaded->getMetaKeywords());
        self::assertNull($loaded->getMetaImagePath());
        self::assertSame(RobotsBehaviour::IndexNoFollow, $loaded->getRobotsBehaviour());
    }

    public function testRobotsBehaviourEnumRoundTrip(): void
    {
        foreach (RobotsBehaviour::cases() as $case) {
            $article = (new TestArticle())
                ->setRobotsBehaviour($case);

            $this->em->persist($article);
            $this->em->flush();
            $this->em->clear();

            $loaded = $this->em->find(TestArticle::class, $article->getId());

            self::assertSame($case, $loaded->getRobotsBehaviour(), \sprintf('Round-trip failed for %s', $case->name));
        }
    }

    public function testGetMetaAfterPersistAndReload(): void
    {
        $article = (new TestArticle())
            ->setTitle('Page Title')
            ->setMetaTitle('Meta Title')
            ->setMetaDescription('<strong>Bold</strong> text')
            ->setMetaKeywords('keyword1, keyword2')
            ->setMetaImagePath('/img/social.png');

        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();

        $loaded = $this->em->find(TestArticle::class, $article->getId());
        $meta = $loaded->getMeta();

        self::assertSame('Page Title', $meta['pageTitle']);
        self::assertSame('Meta Title', $meta['title']);
        self::assertSame('/img/social.png', $meta['image']);
        self::assertSame('Bold text', $meta['description']);
        self::assertSame('keyword1, keyword2', $meta['keywords']);
    }

    public function testUpdateEntity(): void
    {
        $article = (new TestArticle())
            ->setTitle('Original')
            ->setRobotsBehaviour(RobotsBehaviour::IndexFollow);

        $this->em->persist($article);
        $this->em->flush();

        $article->setTitle('Updated');
        $article->setRobotsBehaviour(RobotsBehaviour::NoIndexFollow);

        $this->em->flush();
        $this->em->clear();

        $loaded = $this->em->find(TestArticle::class, $article->getId());

        self::assertSame('Updated', $loaded->getTitle());
        self::assertSame(RobotsBehaviour::NoIndexFollow, $loaded->getRobotsBehaviour());
    }

    public function testFormattedRobotsBehaviourAfterReload(): void
    {
        $article = (new TestArticle())
            ->setRobotsBehaviour(RobotsBehaviour::NoIndexNoFollow);

        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();

        $loaded = $this->em->find(TestArticle::class, $article->getId());

        self::assertSame('noindex, nofollow', $loaded->getFormattedRobotsBehaviour());
    }

    public function testNullFieldsPersistCorrectly(): void
    {
        $article = (new TestArticle())
            ->setTitle('Title')
            ->setMetaTitle(null)
            ->setMetaDescription(null)
            ->setMetaKeywords(null)
            ->setMetaImagePath(null);

        $this->em->persist($article);
        $this->em->flush();
        $this->em->clear();

        $loaded = $this->em->find(TestArticle::class, $article->getId());

        self::assertSame('Title', $loaded->getTitle());
        self::assertNull($loaded->getMetaTitle());
        self::assertNull($loaded->getMetaDescription());
        self::assertNull($loaded->getMetaKeywords());
        self::assertNull($loaded->getMetaImagePath());
    }
}

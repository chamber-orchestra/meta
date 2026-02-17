<?php

declare(strict_types=1);

namespace Tests\Integrational\Entity;

use ChamberOrchestra\MetaBundle\Entity\Helper\RobotsBehaviour;
use ChamberOrchestra\MetaBundle\Entity\MetaInterface;
use ChamberOrchestra\MetaBundle\Entity\MetaTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'test_article')]
class TestArticle implements MetaInterface
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

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function setMetaKeywords(?string $metaKeywords): self
    {
        $this->metaKeywords = $metaKeywords;

        return $this;
    }

    public function setMetaImagePath(?string $metaImagePath): self
    {
        $this->metaImagePath = $metaImagePath;

        return $this;
    }

    public function setRobotsBehaviour(RobotsBehaviour $robotsBehaviour): self
    {
        $this->robotsBehaviour = $robotsBehaviour;

        return $this;
    }
}

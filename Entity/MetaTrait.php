<?php

declare(strict_types=1);

namespace ChamberOrchestra\MetaBundle\Entity;

use ChamberOrchestra\MetaBundle\Entity\Helper\RobotsBehaviour;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

trait MetaTrait
{
    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $title = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $metaTitle = null;

    protected ?File $metaImage = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $metaImagePath = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $metaDescription = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    protected ?string $metaKeywords = null;

    #[ORM\Column(type: 'smallint', nullable: false)]
    protected int $robotsBehaviour = RobotsBehaviour::IndexNoFollow->value;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    public function getRobotsBehaviour(): int
    {
        return $this->robotsBehaviour;
    }

    public function getMetaImage(): ?File
    {
        return $this->metaImage;
    }

    public function getMetaImagePath(): ?string
    {
        return $this->metaImagePath;
    }

    public function getFormattedRobotsBehaviour(): string
    {
        return RobotsBehaviour::getFormattedBehaviour($this->robotsBehaviour);
    }

    public function getMeta(): array
    {
        return [
            'pageTitle' => $this->title,
            'title' => $this->metaTitle,
            'image' => $this->metaImagePath,
            'description' => $this->metaDescription !== null ? strip_tags($this->metaDescription) : null,
            'keywords' => $this->metaKeywords,
        ];
    }
}

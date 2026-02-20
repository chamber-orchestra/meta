<?php

declare(strict_types=1);

namespace ChamberOrchestra\Meta\Entity;

use ChamberOrchestra\FileBundle\Mapping\Annotation as Upload;
use ChamberOrchestra\Meta\Entity\Helper\RobotsBehaviour;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

trait MetaTrait
{
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $title = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $metaTitle = null;

    #[Upload\UploadableProperty(mappedBy: 'metaImagePath')]
    protected ?File $metaImage = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $metaImagePath = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $metaDescription = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    protected ?string $metaKeywords = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: false, enumType: RobotsBehaviour::class)]
    protected RobotsBehaviour $robotsBehaviour = RobotsBehaviour::IndexNoFollow;

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

    public function getRobotsBehaviour(): RobotsBehaviour
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
        return $this->robotsBehaviour->format();
    }

    public function getMeta(): array
    {
        return [
            'pageTitle' => $this->title,
            'title' => $this->metaTitle,
            'image' => $this->metaImagePath,
            'description' => null !== $this->metaDescription ? \strip_tags($this->metaDescription) : null,
            'keywords' => $this->metaKeywords,
        ];
    }
}

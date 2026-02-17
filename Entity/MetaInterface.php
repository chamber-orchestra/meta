<?php

declare(strict_types=1);

namespace ChamberOrchestra\MetaBundle\Entity;

use ChamberOrchestra\MetaBundle\Entity\Helper\RobotsBehaviour;
use Symfony\Component\HttpFoundation\File\File;

interface MetaInterface
{
    public function getTitle(): ?string;

    public function getMetaTitle(): ?string;

    public function getMetaDescription(): ?string;

    public function getMetaKeywords(): ?string;

    public function getMetaImage(): ?File;

    public function getMetaImagePath(): ?string;

    public function getRobotsBehaviour(): RobotsBehaviour;

    /**
     * @return array{pageTitle: ?string, title: ?string, image: ?string, description: ?string, keywords: ?string}
     */
    public function getMeta(): array;
}

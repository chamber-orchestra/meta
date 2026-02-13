<?php

declare(strict_types=1);

namespace ChamberOrchestra\MetaBundle\Entity;

interface MetaInterface
{
    public function getTitle(): ?string;

    public function getMetaTitle(): ?string;

    public function getMetaDescription(): ?string;

    public function getMetaKeywords(): ?string;

    public function getRobotsBehaviour(): int;
}

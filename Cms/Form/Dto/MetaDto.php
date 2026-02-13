<?php

declare(strict_types=1);

namespace ChamberOrchestra\MetaBundle\Cms\Form\Dto;

use ChamberOrchestra\CmsBundle\Form\Dto\AbstractDto;
use Symfony\Component\HttpFoundation\File\File;

class MetaDto extends AbstractDto
{
    public ?string $title = null;
    public ?string $slug = null;
    public ?int $robotsBehaviour = null;
    public ?string $metaTitle = null;
    public ?string $metaDescription = null;
    public ?string $metaKeywords = null;
    public ?string $seoDescription = null;
    public ?File $metaImage = null;
}

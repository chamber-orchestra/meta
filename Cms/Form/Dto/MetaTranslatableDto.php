<?php

declare(strict_types=1);

namespace ChamberOrchestra\MetaBundle\Cms\Form\Dto;

use ChamberOrchestra\CmsBundle\Form\Dto\AbstractDto;
use ChamberOrchestra\CmsBundle\Form\Dto\DtoCollection;
use ChamberOrchestra\TranslationBundle\Cms\Form\Dto\TranslatableDtoTrait;

class MetaTranslatableDto extends AbstractDto
{
    use TranslatableDtoTrait;

    public function __construct(string $typeClass)
    {
        $this->translations = new DtoCollection(MetaDto::class);
        parent::__construct($typeClass);
    }
}

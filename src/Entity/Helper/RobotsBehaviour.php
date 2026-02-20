<?php

declare(strict_types=1);

namespace ChamberOrchestra\Meta\Entity\Helper;

enum RobotsBehaviour: int
{
    case IndexFollow = 0;
    case IndexNoFollow = 1;
    case NoIndexFollow = 2;
    case NoIndexNoFollow = 3;

    public function format(): string
    {
        return match ($this) {
            self::IndexFollow => 'index, follow',
            self::IndexNoFollow => 'index, nofollow',
            self::NoIndexFollow => 'noindex, follow',
            self::NoIndexNoFollow => 'noindex, nofollow',
        };
    }
}

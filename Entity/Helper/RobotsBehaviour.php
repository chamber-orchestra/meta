<?php

declare(strict_types=1);

namespace ChamberOrchestra\MetaBundle\Entity\Helper;

use ChamberOrchestra\MetaBundle\Exception\OutOfBoundsException;

enum RobotsBehaviour: int
{
    case IndexFollow = 0;
    case IndexNoFollow = 1;
    case NoIndexFollow = 2;
    case NoIndexNoFollow = 3;

    public static function choices(): array
    {
        return [
            'robots_behaviour.indexfollow' => self::IndexFollow->value,
            'robots_behaviour.indexnofollow' => self::IndexNoFollow->value,
            'robots_behaviour.noindexfollow' => self::NoIndexFollow->value,
            'robots_behaviour.noindexnofollow' => self::NoIndexNoFollow->value,
        ];
    }

    public static function getFormattedBehaviour(int $behaviour): string
    {
        $case = self::tryFrom($behaviour);

        if ($case === null) {
            throw new OutOfBoundsException(sprintf("No behaviour with type '%d'", $behaviour));
        }

        return match ($case) {
            self::IndexFollow => 'index, follow',
            self::IndexNoFollow => 'index, nofollow',
            self::NoIndexFollow => 'noindex, follow',
            self::NoIndexNoFollow => 'noindex, nofollow',
        };
    }
}

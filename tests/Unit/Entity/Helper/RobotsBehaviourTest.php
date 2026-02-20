<?php

declare(strict_types=1);

namespace Tests\Unit\Entity\Helper;

use ChamberOrchestra\Meta\Entity\Helper\RobotsBehaviour;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RobotsBehaviourTest extends TestCase
{
    #[DataProvider('formatProvider')]
    public function testFormat(RobotsBehaviour $case, string $expected): void
    {
        self::assertSame($expected, $case->format());
    }

    public static function formatProvider(): iterable
    {
        yield 'IndexFollow' => [RobotsBehaviour::IndexFollow, 'index, follow'];
        yield 'IndexNoFollow' => [RobotsBehaviour::IndexNoFollow, 'index, nofollow'];
        yield 'NoIndexFollow' => [RobotsBehaviour::NoIndexFollow, 'noindex, follow'];
        yield 'NoIndexNoFollow' => [RobotsBehaviour::NoIndexNoFollow, 'noindex, nofollow'];
    }

    public function testEnumCasesHaveCorrectValues(): void
    {
        self::assertSame(0, RobotsBehaviour::IndexFollow->value);
        self::assertSame(1, RobotsBehaviour::IndexNoFollow->value);
        self::assertSame(2, RobotsBehaviour::NoIndexFollow->value);
        self::assertSame(3, RobotsBehaviour::NoIndexNoFollow->value);
    }

    public function testTryFromReturnsNullForInvalid(): void
    {
        self::assertNull(RobotsBehaviour::tryFrom(42));
    }

    public function testFromReturnsCase(): void
    {
        self::assertSame(RobotsBehaviour::IndexFollow, RobotsBehaviour::from(0));
        self::assertSame(RobotsBehaviour::NoIndexNoFollow, RobotsBehaviour::from(3));
    }
}

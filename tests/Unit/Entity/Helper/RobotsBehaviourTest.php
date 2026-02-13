<?php

declare(strict_types=1);

namespace Tests\Unit\Entity\Helper;

use ChamberOrchestra\MetaBundle\Entity\Helper\RobotsBehaviour;
use ChamberOrchestra\MetaBundle\Exception\OutOfBoundsException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class RobotsBehaviourTest extends TestCase
{
    public function testChoicesReturnsAllCases(): void
    {
        $choices = RobotsBehaviour::choices();

        self::assertCount(4, $choices);
        self::assertSame(0, $choices['robots_behaviour.indexfollow']);
        self::assertSame(1, $choices['robots_behaviour.indexnofollow']);
        self::assertSame(2, $choices['robots_behaviour.noindexfollow']);
        self::assertSame(3, $choices['robots_behaviour.noindexnofollow']);
    }

    public function testChoicesKeysAreTranslationKeys(): void
    {
        $choices = RobotsBehaviour::choices();

        foreach (array_keys($choices) as $key) {
            self::assertStringStartsWith('robots_behaviour.', $key);
        }
    }

    #[DataProvider('formattedBehaviourProvider')]
    public function testGetFormattedBehaviour(int $value, string $expected): void
    {
        self::assertSame($expected, RobotsBehaviour::getFormattedBehaviour($value));
    }

    public static function formattedBehaviourProvider(): iterable
    {
        yield 'IndexFollow' => [0, 'index, follow'];
        yield 'IndexNoFollow' => [1, 'index, nofollow'];
        yield 'NoIndexFollow' => [2, 'noindex, follow'];
        yield 'NoIndexNoFollow' => [3, 'noindex, nofollow'];
    }

    public function testGetFormattedBehaviourThrowsOnInvalidValue(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage("No behaviour with type '99'");

        RobotsBehaviour::getFormattedBehaviour(99);
    }

    public function testGetFormattedBehaviourThrowsOnNegativeValue(): void
    {
        $this->expectException(OutOfBoundsException::class);

        RobotsBehaviour::getFormattedBehaviour(-1);
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

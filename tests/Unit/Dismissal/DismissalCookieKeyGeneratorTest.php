<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Unit\Dismissal;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusOutOfOfficePlugin\Dismissal\DismissalCookieKeyGenerator;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;

final class DismissalCookieKeyGeneratorTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_builds_the_key_from_the_prefix_id_and_updated_at(): void
    {
        $generator = new DismissalCookieKeyGenerator('prefix');

        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->getId()->willReturn(7);
        $period->getUpdatedAt()->willReturn(new \DateTimeImmutable('@1718600000'));

        self::assertSame('prefix_7_1718600000', $generator->generate($period->reveal()));
    }

    /**
     * @test
     */
    public function it_uses_the_default_prefix(): void
    {
        $generator = new DismissalCookieKeyGenerator();

        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->getId()->willReturn(1);
        $period->getUpdatedAt()->willReturn(new \DateTimeImmutable('@0'));

        self::assertSame('setono_out_of_office_dismissed_1_0', $generator->generate($period->reveal()));
    }

    /**
     * @test
     */
    public function it_handles_a_period_without_id_or_updated_at(): void
    {
        $generator = new DismissalCookieKeyGenerator('prefix');

        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->getId()->willReturn(null);
        $period->getUpdatedAt()->willReturn(null);

        self::assertSame('prefix_new_0', $generator->generate($period->reveal()));
    }
}

<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OutOfOfficeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('setono_sylius_out_of_office_active_period', [OutOfOfficeRuntime::class, 'getActivePeriod']),
            new TwigFunction('setono_sylius_out_of_office_is_active', [OutOfOfficeRuntime::class, 'isActive']),
            new TwigFunction('setono_sylius_out_of_office_period_is_active', [OutOfOfficeRuntime::class, 'isPeriodActive']),
        ];
    }
}

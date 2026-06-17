<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Dismissal;

use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;

interface DismissalCookieKeyGeneratorInterface
{
    /**
     * Returns the cookie key used to remember that the top bar for the given period was dismissed.
     */
    public function generate(OutOfOfficePeriodInterface $period): string;
}

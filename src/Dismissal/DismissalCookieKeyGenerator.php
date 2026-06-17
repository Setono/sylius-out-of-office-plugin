<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Dismissal;

use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;

/**
 * Default policy: the cookie key is derived from the period id and its last update timestamp,
 * so editing a period re-shows a previously dismissed bar. Decorate or replace this service
 * (e.g. with an id-only key) to change the policy without touching templates.
 */
final class DismissalCookieKeyGenerator implements DismissalCookieKeyGeneratorInterface
{
    public function __construct(
        private readonly string $cookiePrefix = 'setono_out_of_office_dismissed',
    ) {
    }

    public function generate(OutOfOfficePeriodInterface $period): string
    {
        $id = $period->getId() ?? 'new';

        $updatedAt = $period->getUpdatedAt();
        $version = null !== $updatedAt ? $updatedAt->getTimestamp() : 0;

        return sprintf('%s_%s_%d', $this->cookiePrefix, $id, $version);
    }
}

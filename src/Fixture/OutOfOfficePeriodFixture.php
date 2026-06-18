<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class OutOfOfficePeriodFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'setono_out_of_office_period';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('starts_at')->end()
                ->scalarNode('ends_at')->end()
                ->booleanNode('show_on_top_bar')->end()
                ->booleanNode('show_on_product_page')->end()
                ->booleanNode('show_at_checkout')->end()
                ->scalarNode('checkout_behavior')->end()
                ->scalarNode('top_bar_message')->end()
                ->scalarNode('product_message')->end()
                ->scalarNode('checkout_message')->end()
                ->variableNode('channels')
                    ->beforeNormalization()
                        ->ifNull()->thenUnset()
                    ->end()
                ->end()
        ;
    }
}

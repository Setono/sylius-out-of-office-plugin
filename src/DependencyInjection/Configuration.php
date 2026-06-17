<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\DependencyInjection;

use Setono\SyliusOutOfOfficePlugin\Doctrine\ORM\OutOfOfficePeriodRepository;
use Setono\SyliusOutOfOfficePlugin\Form\Type\OutOfOfficePeriodTranslationType;
use Setono\SyliusOutOfOfficePlugin\Form\Type\OutOfOfficePeriodType;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriod;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodTranslation;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodTranslationInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Resource\Factory\Factory;
use Sylius\Resource\Factory\TranslatableFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_sylius_out_of_office');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->arrayNode('dismissal')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('cookie_prefix')
                            ->cannotBeEmpty()
                            ->defaultValue('setono_out_of_office_dismissed')
                        ->end()
                        ->integerNode('cookie_max_age')
                            ->info('Lifetime of the dismissal cookie in seconds.')
                            ->defaultValue(2592000) // 30 days
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('out_of_office_period')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(OutOfOfficePeriod::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(OutOfOfficePeriodInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(OutOfOfficePeriodRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(OutOfOfficePeriodType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(OutOfOfficePeriodTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(OutOfOfficePeriodTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                                ->scalarNode('form')->defaultValue(OutOfOfficePeriodTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}

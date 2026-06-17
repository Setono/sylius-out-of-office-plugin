<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Fixture\Factory;

use Faker\Factory;
use Faker\Generator;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficeCheckoutBehavior;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OutOfOfficePeriodExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    private readonly Generator $faker;

    private readonly OptionsResolver $optionsResolver;

    /**
     * @param FactoryInterface<OutOfOfficePeriodInterface> $periodFactory
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private readonly FactoryInterface $periodFactory,
        private readonly RepositoryInterface $localeRepository,
        private readonly ChannelRepositoryInterface $channelRepository,
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * @param array<array-key, mixed> $options
     */
    public function create(array $options = []): OutOfOfficePeriodInterface
    {
        /**
         * @var array{
         *     name: string,
         *     enabled: bool,
         *     starts_at: \DateTimeImmutable|null,
         *     ends_at: \DateTimeImmutable|null,
         *     show_on_top_bar: bool,
         *     show_on_product_page: bool,
         *     show_at_checkout: bool,
         *     dismissible: bool,
         *     checkout_behavior: string,
         *     top_bar_message: string|null,
         *     product_message: string|null,
         *     checkout_message: string|null,
         *     channels: array<array-key, ChannelInterface>
         * } $options
         */
        $options = $this->optionsResolver->resolve($options);

        /** @var OutOfOfficePeriodInterface $period */
        $period = $this->periodFactory->createNew();
        $period->setName($options['name']);
        $period->setEnabled($options['enabled']);
        $period->setStartsAt($options['starts_at']);
        $period->setEndsAt($options['ends_at']);
        $period->setShowOnTopBar($options['show_on_top_bar']);
        $period->setShowOnProductPage($options['show_on_product_page']);
        $period->setShowAtCheckout($options['show_at_checkout']);
        $period->setDismissible($options['dismissible']);
        $period->setCheckoutBehavior($options['checkout_behavior']);

        foreach ($this->getLocales() as $localeCode) {
            $period->setCurrentLocale($localeCode);
            $period->setFallbackLocale($localeCode);

            $period->setTopBarMessage($options['top_bar_message']);
            $period->setProductMessage($options['product_message']);
            $period->setCheckoutMessage($options['checkout_message']);
        }

        foreach ($options['channels'] as $channel) {
            $period->addChannel($channel);
        }

        return $period;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', fn (Options $options): string => $this->faker->sentence(3))
            ->setAllowedTypes('name', 'string')
            ->setDefault('enabled', true)
            ->setAllowedTypes('enabled', 'bool')
            ->setDefault('starts_at', null)
            ->setAllowedTypes('starts_at', ['null', 'string', \DateTimeInterface::class])
            ->setNormalizer('starts_at', fn (Options $options, mixed $value): ?\DateTimeImmutable => $this->normalizeDate($value))
            ->setDefault('ends_at', null)
            ->setAllowedTypes('ends_at', ['null', 'string', \DateTimeInterface::class])
            ->setNormalizer('ends_at', fn (Options $options, mixed $value): ?\DateTimeImmutable => $this->normalizeDate($value))
            ->setDefault('show_on_top_bar', true)
            ->setAllowedTypes('show_on_top_bar', 'bool')
            ->setDefault('show_on_product_page', true)
            ->setAllowedTypes('show_on_product_page', 'bool')
            ->setDefault('show_at_checkout', true)
            ->setAllowedTypes('show_at_checkout', 'bool')
            ->setDefault('dismissible', true)
            ->setAllowedTypes('dismissible', 'bool')
            ->setDefault('checkout_behavior', OutOfOfficeCheckoutBehavior::Allow->value)
            ->setAllowedTypes('checkout_behavior', 'string')
            ->setDefault('top_bar_message', fn (Options $options): string => $this->faker->sentence())
            ->setAllowedTypes('top_bar_message', ['null', 'string'])
            ->setDefault('product_message', null)
            ->setAllowedTypes('product_message', ['null', 'string'])
            ->setDefault('checkout_message', null)
            ->setAllowedTypes('checkout_message', ['null', 'string'])
            ->setDefault('channels', LazyOption::all($this->channelRepository))
            ->setAllowedTypes('channels', 'array')
            ->setNormalizer('channels', LazyOption::findBy($this->channelRepository, 'code'))
        ;
    }

    private function normalizeDate(mixed $value): ?\DateTimeImmutable
    {
        if ($value instanceof \DateTimeImmutable) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return \DateTimeImmutable::createFromInterface($value);
        }

        if (is_string($value)) {
            return new \DateTimeImmutable($value);
        }

        return null;
    }

    /**
     * @return iterable<string>
     */
    private function getLocales(): iterable
    {
        /** @var array<array-key, LocaleInterface> $locales */
        $locales = $this->localeRepository->findAll();

        foreach ($locales as $locale) {
            $code = $locale->getCode();
            if (null !== $code) {
                yield $code;
            }
        }
    }
}

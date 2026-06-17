<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DatabaseTestCase extends KernelTestCase
{
    protected EntityManagerInterface $entityManager;

    private ?CurrencyInterface $currency = null;

    private ?LocaleInterface $locale = null;

    protected function setUp(): void
    {
        self::bootKernel();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager = $entityManager;

        try {
            $this->entityManager->getConnection()->executeQuery('SELECT 1');
        } catch (\Throwable $e) {
            self::markTestSkipped('A database connection is required for this test: ' . $e->getMessage());
        }

        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    protected function createChannel(string $code): ChannelInterface
    {
        $currency = $this->getCurrency();
        $locale = $this->getLocale();

        /** @var FactoryInterface<ChannelInterface> $channelFactory */
        $channelFactory = self::getContainer()->get('sylius.factory.channel');
        $channel = $channelFactory->createNew();
        $channel->setCode($code);
        $channel->setName($code);
        $channel->setTaxCalculationStrategy('order_items_based');
        $channel->setBaseCurrency($currency);
        $channel->setDefaultLocale($locale);
        $channel->addLocale($locale);
        $channel->addCurrency($currency);
        $channel->setEnabled(true);
        $this->entityManager->persist($channel);

        return $channel;
    }

    private function getCurrency(): CurrencyInterface
    {
        if (null === $this->currency) {
            /** @var FactoryInterface<CurrencyInterface> $currencyFactory */
            $currencyFactory = self::getContainer()->get('sylius.factory.currency');
            $this->currency = $currencyFactory->createNew();
            $this->currency->setCode('USD');
            $this->entityManager->persist($this->currency);
        }

        return $this->currency;
    }

    private function getLocale(): LocaleInterface
    {
        if (null === $this->locale) {
            /** @var FactoryInterface<LocaleInterface> $localeFactory */
            $localeFactory = self::getContainer()->get('sylius.factory.locale');
            $this->locale = $localeFactory->createNew();
            $this->locale->setCode('en_US');
            $this->entityManager->persist($this->locale);
        }

        return $this->locale;
    }
}

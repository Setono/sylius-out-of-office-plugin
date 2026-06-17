<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Creates the out of office period tables. Targets MySQL.
 */
final class Version20260617000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create setono out of office period tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE setono_sylius_out_of_office_period (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, starts_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ends_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', show_on_top_bar TINYINT(1) NOT NULL, show_on_product_page TINYINT(1) NOT NULL, show_at_checkout TINYINT(1) NOT NULL, dismissible TINYINT(1) NOT NULL, checkout_behavior VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setono_sylius_out_of_office_period_channels (period_id INT NOT NULL, channel_id INT NOT NULL, INDEX IDX_CF24F07EEC8B7ADE (period_id), INDEX IDX_CF24F07E72F5A1AA (channel_id), PRIMARY KEY(period_id, channel_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setono_sylius_out_of_office_period_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT NOT NULL, top_bar_message LONGTEXT DEFAULT NULL, product_message LONGTEXT DEFAULT NULL, checkout_message LONGTEXT DEFAULT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_75F8C2D42C2AC5D3 (translatable_id), UNIQUE INDEX setono_sylius_out_of_office_period_translation_uniq_trans (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE setono_sylius_out_of_office_period_channels ADD CONSTRAINT FK_CF24F07EEC8B7ADE FOREIGN KEY (period_id) REFERENCES setono_sylius_out_of_office_period (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE setono_sylius_out_of_office_period_channels ADD CONSTRAINT FK_CF24F07E72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE setono_sylius_out_of_office_period_translation ADD CONSTRAINT FK_75F8C2D42C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES setono_sylius_out_of_office_period (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE setono_sylius_out_of_office_period_channels DROP FOREIGN KEY FK_CF24F07EEC8B7ADE');
        $this->addSql('ALTER TABLE setono_sylius_out_of_office_period_translation DROP FOREIGN KEY FK_75F8C2D42C2AC5D3');
        $this->addSql('DROP TABLE setono_sylius_out_of_office_period_translation');
        $this->addSql('DROP TABLE setono_sylius_out_of_office_period_channels');
        $this->addSql('DROP TABLE setono_sylius_out_of_office_period');
    }
}

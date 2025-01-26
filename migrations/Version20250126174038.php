<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250126174038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE cursus ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE lesson ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE purchase ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE theme ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP created_at, DROP updated_at, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE theme DROP created_at, DROP updated_at, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE purchase DROP created_at, DROP updated_at, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE user DROP created_at, DROP updated_at, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE lesson DROP created_at, DROP updated_at, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE cursus DROP created_at, DROP updated_at, DROP created_by, DROP updated_by');
    }
}

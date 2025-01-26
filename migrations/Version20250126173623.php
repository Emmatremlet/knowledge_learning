<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250126173623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7559027487');
        $this->addSql('DROP INDEX IDX_6C3C6D7559027487 ON certification');
        $this->addSql('ALTER TABLE certification CHANGE theme_id cursus_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7540AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('CREATE INDEX IDX_6C3C6D7540AEF4B9 ON certification (cursus_id)');
        $this->addSql('ALTER TABLE lesson ADD is_validated TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE purchase ADD status VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certification DROP FOREIGN KEY FK_6C3C6D7540AEF4B9');
        $this->addSql('DROP INDEX IDX_6C3C6D7540AEF4B9 ON certification');
        $this->addSql('ALTER TABLE certification CHANGE cursus_id theme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE certification ADD CONSTRAINT FK_6C3C6D7559027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_6C3C6D7559027487 ON certification (theme_id)');
        $this->addSql('ALTER TABLE lesson DROP is_validated');
        $this->addSql('ALTER TABLE purchase DROP status');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124102755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase ADD lesson_id INT DEFAULT NULL, ADD cursus_id INT DEFAULT NULL, DROP item_id, DROP item_type');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BCDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B40AEF4B9 FOREIGN KEY (cursus_id) REFERENCES cursus (id)');
        $this->addSql('CREATE INDEX IDX_6117D13BCDF80196 ON purchase (lesson_id)');
        $this->addSql('CREATE INDEX IDX_6117D13B40AEF4B9 ON purchase (cursus_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BCDF80196');
        $this->addSql('ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B40AEF4B9');
        $this->addSql('DROP INDEX IDX_6117D13BCDF80196 ON purchase');
        $this->addSql('DROP INDEX IDX_6117D13B40AEF4B9 ON purchase');
        $this->addSql('ALTER TABLE purchase ADD item_id INT NOT NULL, ADD item_type VARCHAR(20) NOT NULL, DROP lesson_id, DROP cursus_id');
    }
}

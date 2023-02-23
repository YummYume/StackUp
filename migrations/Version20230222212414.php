<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222212414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add depends_on field to tech.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tech ADD depends_on_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tech ADD CONSTRAINT FK_86BC30121E088F8 FOREIGN KEY (depends_on_id) REFERENCES tech (id)');
        $this->addSql('CREATE INDEX IDX_86BC30121E088F8 ON tech (depends_on_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tech DROP FOREIGN KEY FK_86BC30121E088F8');
        $this->addSql('DROP INDEX IDX_86BC30121E088F8 ON tech');
        $this->addSql('ALTER TABLE tech DROP depends_on_id');
    }
}

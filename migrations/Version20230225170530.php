<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230225170530 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add SET NULL on delete for depends_on.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tech DROP FOREIGN KEY FK_86BC30121E088F8');
        $this->addSql('ALTER TABLE tech ADD CONSTRAINT FK_86BC30121E088F8 FOREIGN KEY (depends_on_id) REFERENCES tech (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tech DROP FOREIGN KEY FK_86BC30121E088F8');
        $this->addSql('ALTER TABLE tech ADD CONSTRAINT FK_86BC30121E088F8 FOREIGN KEY (depends_on_id) REFERENCES tech (id)');
    }
}

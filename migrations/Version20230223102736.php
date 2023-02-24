<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230223102736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix picture_id for tech.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tech ADD picture_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE tech ADD CONSTRAINT FK_86BC3012EE45BDBF FOREIGN KEY (picture_id) REFERENCES tech_picture (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_86BC3012EE45BDBF ON tech (picture_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tech DROP FOREIGN KEY FK_86BC3012EE45BDBF');
        $this->addSql('DROP INDEX UNIQ_86BC3012EE45BDBF ON tech');
        $this->addSql('ALTER TABLE tech DROP picture_id');
    }
}

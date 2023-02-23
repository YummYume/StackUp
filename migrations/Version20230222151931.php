<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230222151931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add category, request, stack, tech, tech_picture and vote tables.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(100) NOT NULL, slug VARCHAR(150) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_64C19C1DE12AB56 (created_by), INDEX IDX_64C19C116FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(100) NOT NULL, last_changed_at DATETIME NOT NULL, created TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_3B978F9FDE12AB56 (created_by), INDEX IDX_3B978F9F16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stack (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', profile_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, slug VARCHAR(150) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_41A87B6ACCFA12B8 (profile_id), INDEX IDX_41A87B6ADE12AB56 (created_by), INDEX IDX_41A87B6A16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stack_tech (stack_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', tech_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_8A2C7AE437C70060 (stack_id), INDEX IDX_8A2C7AE464727BFC (tech_id), PRIMARY KEY(stack_id, tech_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tech (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', request_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(100) NOT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(100) NOT NULL, slug VARCHAR(150) NOT NULL, links LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_86BC3012427EB8A5 (request_id), INDEX IDX_86BC3012DE12AB56 (created_by), INDEX IDX_86BC301216FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tech_category (tech_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', category_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_4BB73A64727BFC (tech_id), INDEX IDX_4BB73A12469DE2 (category_id), PRIMARY KEY(tech_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tech_picture (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', file_name VARCHAR(255) DEFAULT NULL, size INT DEFAULT NULL, mime_type VARCHAR(255) DEFAULT NULL, original_name VARCHAR(255) DEFAULT NULL, dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A7C0ABCCDE12AB56 (created_by), INDEX IDX_A7C0ABCC16FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vote (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', profile_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', request_id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', created_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', updated_by BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', upvote TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_5A108564CCFA12B8 (profile_id), INDEX IDX_5A108564427EB8A5 (request_id), INDEX IDX_5A108564DE12AB56 (created_by), INDEX IDX_5A10856416FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C116FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9FDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stack ADD CONSTRAINT FK_41A87B6ACCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE stack ADD CONSTRAINT FK_41A87B6ADE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stack ADD CONSTRAINT FK_41A87B6A16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE stack_tech ADD CONSTRAINT FK_8A2C7AE437C70060 FOREIGN KEY (stack_id) REFERENCES stack (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stack_tech ADD CONSTRAINT FK_8A2C7AE464727BFC FOREIGN KEY (tech_id) REFERENCES tech (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tech ADD CONSTRAINT FK_86BC3012427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id)');
        $this->addSql('ALTER TABLE tech ADD CONSTRAINT FK_86BC3012DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tech ADD CONSTRAINT FK_86BC301216FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tech_category ADD CONSTRAINT FK_4BB73A64727BFC FOREIGN KEY (tech_id) REFERENCES tech (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tech_category ADD CONSTRAINT FK_4BB73A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tech_picture ADD CONSTRAINT FK_A7C0ABCCDE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tech_picture ADD CONSTRAINT FK_A7C0ABCC16FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A108564DE12AB56 FOREIGN KEY (created_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A10856416FE72E1 FOREIGN KEY (updated_by) REFERENCES user (id)');
        $this->addSql('ALTER TABLE profile ADD github_link VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1DE12AB56');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C116FE72E1');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9FDE12AB56');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F16FE72E1');
        $this->addSql('ALTER TABLE stack DROP FOREIGN KEY FK_41A87B6ACCFA12B8');
        $this->addSql('ALTER TABLE stack DROP FOREIGN KEY FK_41A87B6ADE12AB56');
        $this->addSql('ALTER TABLE stack DROP FOREIGN KEY FK_41A87B6A16FE72E1');
        $this->addSql('ALTER TABLE stack_tech DROP FOREIGN KEY FK_8A2C7AE437C70060');
        $this->addSql('ALTER TABLE stack_tech DROP FOREIGN KEY FK_8A2C7AE464727BFC');
        $this->addSql('ALTER TABLE tech DROP FOREIGN KEY FK_86BC3012427EB8A5');
        $this->addSql('ALTER TABLE tech DROP FOREIGN KEY FK_86BC3012DE12AB56');
        $this->addSql('ALTER TABLE tech DROP FOREIGN KEY FK_86BC301216FE72E1');
        $this->addSql('ALTER TABLE tech_category DROP FOREIGN KEY FK_4BB73A64727BFC');
        $this->addSql('ALTER TABLE tech_category DROP FOREIGN KEY FK_4BB73A12469DE2');
        $this->addSql('ALTER TABLE tech_picture DROP FOREIGN KEY FK_A7C0ABCCDE12AB56');
        $this->addSql('ALTER TABLE tech_picture DROP FOREIGN KEY FK_A7C0ABCC16FE72E1');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564CCFA12B8');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564427EB8A5');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A108564DE12AB56');
        $this->addSql('ALTER TABLE vote DROP FOREIGN KEY FK_5A10856416FE72E1');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE stack');
        $this->addSql('DROP TABLE stack_tech');
        $this->addSql('DROP TABLE tech');
        $this->addSql('DROP TABLE tech_category');
        $this->addSql('DROP TABLE tech_picture');
        $this->addSql('DROP TABLE vote');
        $this->addSql('ALTER TABLE profile DROP github_link');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424144032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, haiku_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL, INDEX IDX_5F9E962AF624B39D (sender_id), INDEX IDX_5F9E962A4F157774 (haiku_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE likes (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, haiku_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_49CA4E7DF624B39D (sender_id), INDEX IDX_49CA4E7D4F157774 (haiku_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AF624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A4F157774 FOREIGN KEY (haiku_id) REFERENCES haikus (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7DF624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likes ADD CONSTRAINT FK_49CA4E7D4F157774 FOREIGN KEY (haiku_id) REFERENCES haikus (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AF624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A4F157774
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7DF624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE likes DROP FOREIGN KEY FK_49CA4E7D4F157774
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE comments
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE likes
        SQL);
    }
}

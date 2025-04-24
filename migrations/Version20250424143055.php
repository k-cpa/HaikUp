<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424143055 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE collections (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_D325D3EE61220EA6 (creator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE haikus (id INT AUTO_INCREMENT NOT NULL, creator_id INT NOT NULL, collection_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_68DF431461220EA6 (creator_id), INDEX IDX_68DF4314514956FD (collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collections ADD CONSTRAINT FK_D325D3EE61220EA6 FOREIGN KEY (creator_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haikus ADD CONSTRAINT FK_68DF431461220EA6 FOREIGN KEY (creator_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haikus ADD CONSTRAINT FK_68DF4314514956FD FOREIGN KEY (collection_id) REFERENCES collections (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words ADD haiku_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words ADD CONSTRAINT FK_93BFE5D44F157774 FOREIGN KEY (haiku_id) REFERENCES haikus (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_93BFE5D44F157774 ON user_words (haiku_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words DROP FOREIGN KEY FK_93BFE5D44F157774
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collections DROP FOREIGN KEY FK_D325D3EE61220EA6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haikus DROP FOREIGN KEY FK_68DF431461220EA6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haikus DROP FOREIGN KEY FK_68DF4314514956FD
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE collections
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE haikus
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_93BFE5D44F157774 ON user_words
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words DROP haiku_id, DROP created_at
        SQL);
    }
}

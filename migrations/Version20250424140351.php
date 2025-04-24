<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250424140351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_words (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, words_id INT NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_93BFE5D4F624B39D (sender_id), INDEX IDX_93BFE5D4CD53EDB6 (receiver_id), INDEX IDX_93BFE5D4749B15FB (words_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE words (id INT AUTO_INCREMENT NOT NULL, word LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words ADD CONSTRAINT FK_93BFE5D4F624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words ADD CONSTRAINT FK_93BFE5D4CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words ADD CONSTRAINT FK_93BFE5D4749B15FB FOREIGN KEY (words_id) REFERENCES words (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words DROP FOREIGN KEY FK_93BFE5D4F624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words DROP FOREIGN KEY FK_93BFE5D4CD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words DROP FOREIGN KEY FK_93BFE5D4749B15FB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_words
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE words
        SQL);
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428091242 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE haikus DROP FOREIGN KEY FK_68DF431461220EA6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haikus CHANGE creator_id creator_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haikus ADD CONSTRAINT FK_68DF431461220EA6 FOREIGN KEY (creator_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE haikus DROP FOREIGN KEY FK_68DF431461220EA6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haikus CHANGE creator_id creator_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE haikus ADD CONSTRAINT FK_68DF431461220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)
        SQL);
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250604134542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words DROP FOREIGN KEY FK_93BFE5D44F157774
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words ADD CONSTRAINT FK_93BFE5D44F157774 FOREIGN KEY (haiku_id) REFERENCES haikus (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words DROP FOREIGN KEY FK_93BFE5D44F157774
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words ADD CONSTRAINT FK_93BFE5D44F157774 FOREIGN KEY (haiku_id) REFERENCES haikus (id)
        SQL);
    }
}

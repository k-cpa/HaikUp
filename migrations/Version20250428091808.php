<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428091808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE collections DROP FOREIGN KEY FK_D325D3EE61220EA6
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D325D3EE61220EA6 ON collections
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collections ADD collection_id INT DEFAULT NULL, DROP creator_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collections ADD CONSTRAINT FK_D325D3EE514956FD FOREIGN KEY (collection_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D325D3EE514956FD ON collections (collection_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE collections DROP FOREIGN KEY FK_D325D3EE514956FD
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D325D3EE514956FD ON collections
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collections ADD creator_id INT NOT NULL, DROP collection_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collections ADD CONSTRAINT FK_D325D3EE61220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D325D3EE61220EA6 ON collections (creator_id)
        SQL);
    }
}

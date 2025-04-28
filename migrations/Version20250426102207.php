<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250426102207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications ADD entity_type_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D35681BEB0 FOREIGN KEY (entity_type_id) REFERENCES entity_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6000B0D35681BEB0 ON notifications (entity_type_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D35681BEB0
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6000B0D35681BEB0 ON notifications
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications DROP entity_type_id
        SQL);
    }
}

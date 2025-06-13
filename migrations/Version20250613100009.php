<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613100009 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE follows DROP FOREIGN KEY FK_4B638A73CD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE follows DROP FOREIGN KEY FK_4B638A73F624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE follows ADD CONSTRAINT FK_4B638A73CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE follows ADD CONSTRAINT FK_4B638A73F624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE follows DROP FOREIGN KEY FK_4B638A73F624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE follows DROP FOREIGN KEY FK_4B638A73CD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE follows ADD CONSTRAINT FK_4B638A73F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE follows ADD CONSTRAINT FK_4B638A73CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)
        SQL);
    }
}

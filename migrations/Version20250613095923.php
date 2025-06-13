<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613095923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3CD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3F624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications CHANGE sender_id sender_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3F624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words DROP FOREIGN KEY FK_93BFE5D4F624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words DROP FOREIGN KEY FK_93BFE5D4CD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words ADD CONSTRAINT FK_93BFE5D4F624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words ADD CONSTRAINT FK_93BFE5D4CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id) ON DELETE SET NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3CD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3F624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications CHANGE sender_id sender_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D649F85E0677 ON `user`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words DROP FOREIGN KEY FK_93BFE5D4F624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words DROP FOREIGN KEY FK_93BFE5D4CD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words ADD CONSTRAINT FK_93BFE5D4F624B39D FOREIGN KEY (sender_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_words ADD CONSTRAINT FK_93BFE5D4CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)
        SQL);
    }
}

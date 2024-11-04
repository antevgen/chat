<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241104074848 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE groups (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F06D39705E237E06 ON groups (name)');
        $this->addSql('CREATE TABLE messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, subject VARCHAR(255) NOT NULL, content CLOB NOT NULL, createdAt DATETIME NOT NULL, user_id INTEGER NOT NULL, group_id INTEGER NOT NULL, CONSTRAINT FK_DB021E96A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_DB021E96FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_DB021E96A76ED395 ON messages (user_id)');
        $this->addSql('CREATE INDEX IDX_DB021E96FE54D947 ON messages (group_id)');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9F85E0677 ON users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE TABLE user_group (user_id INTEGER NOT NULL, group_id INTEGER NOT NULL, PRIMARY KEY(user_id, group_id), CONSTRAINT FK_8F02BF9DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8F02BF9DFE54D947 FOREIGN KEY (group_id) REFERENCES groups (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8F02BF9DA76ED395 ON user_group (user_id)');
        $this->addSql('CREATE INDEX IDX_8F02BF9DFE54D947 ON user_group (group_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE user_group');
    }
}

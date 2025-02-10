<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210105824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA game');
        $this->addSql('CREATE TABLE game.game (id SERIAL NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE game.game_player (game_id INT NOT NULL, player_id INT NOT NULL, PRIMARY KEY(game_id, player_id))');
        $this->addSql('CREATE INDEX IDX_84965834E48FD905 ON game.game_player (game_id)');
        $this->addSql('CREATE INDEX IDX_8496583499E6F5DF ON game.game_player (player_id)');
        $this->addSql('ALTER TABLE game.game_player ADD CONSTRAINT FK_84965834E48FD905 FOREIGN KEY (game_id) REFERENCES game.game (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game.game_player ADD CONSTRAINT FK_8496583499E6F5DF FOREIGN KEY (player_id) REFERENCES player.player (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game.game_player DROP CONSTRAINT FK_84965834E48FD905');
        $this->addSql('ALTER TABLE game.game_player DROP CONSTRAINT FK_8496583499E6F5DF');
        $this->addSql('DROP TABLE game.game');
        $this->addSql('DROP TABLE game.game_player');
    }
}

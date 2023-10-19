<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231019075723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE foto ADD COLUMN destacada BOOLEAN NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__foto AS SELECT id, comercio_id, archivo FROM foto');
        $this->addSql('DROP TABLE foto');
        $this->addSql('CREATE TABLE foto (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, comercio_id INTEGER NOT NULL, archivo VARCHAR(255) NOT NULL, CONSTRAINT FK_EADC3BE52C8A84B9 FOREIGN KEY (comercio_id) REFERENCES comercio (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO foto (id, comercio_id, archivo) SELECT id, comercio_id, archivo FROM __temp__foto');
        $this->addSql('DROP TABLE __temp__foto');
        $this->addSql('CREATE INDEX IDX_EADC3BE52C8A84B9 ON foto (comercio_id)');
    }
}

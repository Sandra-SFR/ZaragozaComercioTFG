<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231016095735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE foto (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, comercio_id INTEGER NOT NULL, archivo VARCHAR(255) NOT NULL, CONSTRAINT FK_EADC3BE52C8A84B9 FOREIGN KEY (comercio_id) REFERENCES comercio (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_EADC3BE52C8A84B9 ON foto (comercio_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE foto');
    }
}

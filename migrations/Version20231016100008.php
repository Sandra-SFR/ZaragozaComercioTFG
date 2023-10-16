<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231016100008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comercio_categoria (comercio_id INTEGER NOT NULL, categoria_id INTEGER NOT NULL, PRIMARY KEY(comercio_id, categoria_id), CONSTRAINT FK_B06AA44B2C8A84B9 FOREIGN KEY (comercio_id) REFERENCES comercio (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B06AA44B3397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_B06AA44B2C8A84B9 ON comercio_categoria (comercio_id)');
        $this->addSql('CREATE INDEX IDX_B06AA44B3397707A ON comercio_categoria (categoria_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comercio_categoria');
    }
}

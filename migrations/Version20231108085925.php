<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231108085925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categoria (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, icono VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comercio (id INT AUTO_INCREMENT NOT NULL, usuario_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion VARCHAR(255) NOT NULL, direccion VARCHAR(255) NOT NULL, telefono INT NOT NULL, email VARCHAR(255) NOT NULL, estado SMALLINT DEFAULT NULL, INDEX IDX_419511CDDB38439E (usuario_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comercio_categoria (comercio_id INT NOT NULL, categoria_id INT NOT NULL, INDEX IDX_B06AA44B2C8A84B9 (comercio_id), INDEX IDX_B06AA44B3397707A (categoria_id), PRIMARY KEY(comercio_id, categoria_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE foto (id INT AUTO_INCREMENT NOT NULL, comercio_id INT NOT NULL, archivo VARCHAR(255) NOT NULL, destacada TINYINT(1) NOT NULL, INDEX IDX_EADC3BE52C8A84B9 (comercio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE horario (id INT AUTO_INCREMENT NOT NULL, comercio_id INT NOT NULL, hora_apertura TIME NOT NULL, hora_cierre TIME NOT NULL, dia SMALLINT NOT NULL, INDEX IDX_E25853A32C8A84B9 (comercio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comercio ADD CONSTRAINT FK_419511CDDB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id)');
        $this->addSql('ALTER TABLE comercio_categoria ADD CONSTRAINT FK_B06AA44B2C8A84B9 FOREIGN KEY (comercio_id) REFERENCES comercio (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comercio_categoria ADD CONSTRAINT FK_B06AA44B3397707A FOREIGN KEY (categoria_id) REFERENCES categoria (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE foto ADD CONSTRAINT FK_EADC3BE52C8A84B9 FOREIGN KEY (comercio_id) REFERENCES comercio (id)');
        $this->addSql('ALTER TABLE horario ADD CONSTRAINT FK_E25853A32C8A84B9 FOREIGN KEY (comercio_id) REFERENCES comercio (id)');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id BIGINT AUTO_INCREMENT NOT NULL, CHANGE body body LONGTEXT NOT NULL, CHANGE headers headers LONGTEXT NOT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE available_at available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comercio DROP FOREIGN KEY FK_419511CDDB38439E');
        $this->addSql('ALTER TABLE comercio_categoria DROP FOREIGN KEY FK_B06AA44B2C8A84B9');
        $this->addSql('ALTER TABLE comercio_categoria DROP FOREIGN KEY FK_B06AA44B3397707A');
        $this->addSql('ALTER TABLE foto DROP FOREIGN KEY FK_EADC3BE52C8A84B9');
        $this->addSql('ALTER TABLE horario DROP FOREIGN KEY FK_E25853A32C8A84B9');
        $this->addSql('DROP TABLE categoria');
        $this->addSql('DROP TABLE comercio');
        $this->addSql('DROP TABLE comercio_categoria');
        $this->addSql('DROP TABLE foto');
        $this->addSql('DROP TABLE horario');
        $this->addSql('ALTER TABLE messenger_messages CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE body body JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE headers headers JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE created_at created_at DATETIME NOT NULL, CHANGE available_at available_at DATETIME NOT NULL, CHANGE delivered_at delivered_at DATETIME DEFAULT NULL');
    }
}

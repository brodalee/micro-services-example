<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240623210156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE products (id VARCHAR(255) NOT NULL, designation VARCHAR(255) NOT NULL, price INT NOT NULL, creation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ram INT NOT NULL, storage INT NOT NULL, color VARCHAR(255) NOT NULL, description TEXT NOT NULL, resolution VARCHAR(255) NOT NULL, processor VARCHAR(255) NOT NULL, front_camera_resolution INT NOT NULL, back_camera_resolution INT NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE products');
    }
}

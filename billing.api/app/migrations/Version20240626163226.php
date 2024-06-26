<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240626163226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE billings (id VARCHAR(255) NOT NULL, creation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_id VARCHAR(255) NOT NULL, tva DOUBLE PRECISION NOT NULL, payment_reference VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE billings_items (id VARCHAR(255) NOT NULL, billing_id VARCHAR(255) NOT NULL, price INT NOT NULL, product_id VARCHAR(255) NOT NULL, quantity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_26E175693B025C87 ON billings_items (billing_id)');
        $this->addSql('ALTER TABLE billings_items ADD CONSTRAINT FK_26E175693B025C87 FOREIGN KEY (billing_id) REFERENCES billings (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE billings_items DROP CONSTRAINT FK_26E175693B025C87');
        $this->addSql('DROP TABLE billings');
        $this->addSql('DROP TABLE billings_items');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250716190226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "order" ALTER order_status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE "order" ALTER delivery_type TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE product ALTER measurements TYPE JSON');
        $this->addSql('ALTER TABLE report ADD detail JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE report DROP detail');
        $this->addSql('ALTER TABLE "order" ALTER order_status TYPE VARCHAR(40)');
        $this->addSql('ALTER TABLE "order" ALTER delivery_type TYPE VARCHAR(40)');
        $this->addSql('ALTER TABLE product ALTER measurements TYPE JSON');
    }
}

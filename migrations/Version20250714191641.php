<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250714191641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE report_id_seq CASCADE');
        $this->addSql('ALTER TABLE report ALTER COLUMN id TYPE UUID USING id::text::uuid');
        $this->addSql('ALTER TABLE report ALTER COLUMN id SET DEFAULT gen_random_uuid()');
        $this->addSql('ALTER TABLE report ALTER id DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN report.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE report ALTER COLUMN id TYPE INT USING id::text::integer');
        $this->addSql('ALTER TABLE report ALTER COLUMN id DROP DEFAULT');
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE report_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE report_id_seq');
        $this->addSql('SELECT setval(\'report_id_seq\', (SELECT MAX(id) FROM report))');
        $this->addSql('COMMENT ON COLUMN report.id IS NULL');
    }
}

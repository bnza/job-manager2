<?php

declare(strict_types=1);

namespace Bnza\JobManagerBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250315170718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE bnza_job_manager.job_error (id UUID NOT NULL, work_unit_id UUID DEFAULT NULL, class VARCHAR(255) NOT NULL, message TEXT NOT NULL, values JSONB DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_787128E22D76C82F ON bnza_job_manager.job_error (work_unit_id)');
        $this->addSql('COMMENT ON COLUMN bnza_job_manager.job_error.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN bnza_job_manager.job_error.work_unit_id IS \'(DC2Type:uuid)\'');
        $this->addSql(
            'ALTER TABLE bnza_job_manager.job_error ADD CONSTRAINT FK_787128E22D76C82F FOREIGN KEY (work_unit_id) REFERENCES bnza_job_manager.job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER startedat TYPE BIGINT');
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER terminatedat TYPE BIGINT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bnza_job_manager.job_error DROP CONSTRAINT FK_787128E22D76C82F');
        $this->addSql('DROP TABLE bnza_job_manager.job_error');
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER startedAt TYPE BIGINT');
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER terminatedAt TYPE BIGINT');
    }
}

<?php

declare(strict_types=1);

namespace Bnza\JobManagerBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250304163605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA bnza_job_manager');
        $this->addSql('CREATE TABLE bnza_job_manager.job (id UUID NOT NULL, parent_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, class VARCHAR(255) NOT NULL, service VARCHAR(255) NOT NULL, parameters JSONB DEFAULT NULL, stepsCount SMALLINT NOT NULL, startedAt BIGINT DEFAULT NULL, terminatedAt BIGINT DEFAULT NULL, status_value INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F00AA88727ACA70 ON bnza_job_manager.job (parent_id)');
        $this->addSql('COMMENT ON COLUMN bnza_job_manager.job.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN bnza_job_manager.job.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE bnza_job_manager.job ADD CONSTRAINT FK_5F00AA88727ACA70 FOREIGN KEY (parent_id) REFERENCES bnza_job_manager.job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bnza_job_manager.job DROP CONSTRAINT FK_5F00AA88727ACA70');
        $this->addSql('DROP TABLE bnza_job_manager.job');
    }
}

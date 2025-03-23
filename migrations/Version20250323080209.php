<?php

declare(strict_types=1);

namespace Bnza\JobManagerBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250323080209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bnza_job_manager.job ADD current_step_num SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE bnza_job_manager.job DROP name');
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER started_at TYPE BIGINT');
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER terminated_at TYPE BIGINT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bnza_job_manager.job ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE bnza_job_manager.job DROP current_step_num');
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER started_at TYPE BIGINT');
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER terminated_at TYPE BIGINT');
    }
}

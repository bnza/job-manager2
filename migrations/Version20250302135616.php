<?php

declare(strict_types=1);

namespace Bnza\JobManagerBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250302135616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA bnza_job_manager');
        $this->addSql(
            'CREATE TABLE bnza_job_manager.job (id UUID NOT NULL, name VARCHAR(255) NOT NULL, class VARCHAR(255) NOT NULL, service VARCHAR(255) NOT NULL, parameters JSONB DEFAULT NULL, stepsCount SMALLINT NOT NULL, status INT NOT NULL, startedAt BIGINT DEFAULT NULL, terminatedAt BIGINT DEFAULT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON COLUMN bnza_job_manager.job.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SCHEMA IF EXISTS bnza_job_manager');
        $this->addSql('DROP TABLE bnza_job_manager.job');
    }
}

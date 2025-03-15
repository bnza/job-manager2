<?php

declare(strict_types=1);

namespace Bnza\JobManagerBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250314162415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER startedat TYPE BIGINT');
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER terminatedat TYPE BIGINT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER startedAt TYPE BIGINT');
        $this->addSql('ALTER TABLE bnza_job_manager.job ALTER terminatedAt TYPE BIGINT');
    }
}

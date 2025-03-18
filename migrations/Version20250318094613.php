<?php

declare(strict_types=1);

namespace Bnza\JobManagerBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318094613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA bnza_job_manager');
        $this->addSql('CREATE TABLE bnza_job_manager.job (id UUID NOT NULL, parent_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, user_id VARCHAR(255) DEFAULT NULL, class VARCHAR(255) NOT NULL, service VARCHAR(255) NOT NULL, parameters JSONB DEFAULT NULL, steps_count SMALLINT NOT NULL, started_at BIGINT DEFAULT NULL, terminated_at BIGINT DEFAULT NULL, status_value INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5F00AA88727ACA70 ON bnza_job_manager.job (parent_id)');
        $this->addSql('COMMENT ON COLUMN bnza_job_manager.job.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN bnza_job_manager.job.parent_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE bnza_job_manager.job_error (id UUID NOT NULL, work_unit_id UUID DEFAULT NULL, class VARCHAR(255) NOT NULL, message TEXT NOT NULL, values JSONB DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_787128E22D76C82F ON bnza_job_manager.job_error (work_unit_id)');
        $this->addSql('COMMENT ON COLUMN bnza_job_manager.job_error.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN bnza_job_manager.job_error.work_unit_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE bnza_job_manager.job ADD CONSTRAINT FK_5F00AA88727ACA70 FOREIGN KEY (parent_id) REFERENCES bnza_job_manager.job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE bnza_job_manager.job_error ADD CONSTRAINT FK_787128E22D76C82F FOREIGN KEY (work_unit_id) REFERENCES bnza_job_manager.job (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bnza_job_manager.job DROP CONSTRAINT FK_5F00AA88727ACA70');
        $this->addSql('ALTER TABLE bnza_job_manager.job_error DROP CONSTRAINT FK_787128E22D76C82F');
        $this->addSql('DROP TABLE bnza_job_manager.job');
        $this->addSql('DROP TABLE bnza_job_manager.job_error');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

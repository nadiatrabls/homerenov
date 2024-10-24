<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023082204 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonne ADD reset_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE chantier CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chantier ADD CONSTRAINT FK_636F27F619EB6921 FOREIGN KEY (client_id) REFERENCES abonne (id)');
        $this->addSql('CREATE INDEX IDX_636F27F619EB6921 ON chantier (client_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE abonne DROP reset_token');
        $this->addSql('ALTER TABLE chantier DROP FOREIGN KEY FK_636F27F619EB6921');
        $this->addSql('DROP INDEX IDX_636F27F619EB6921 ON chantier');
        $this->addSql('ALTER TABLE chantier CHANGE client_id client_id INT NOT NULL');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250103170940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE disponibilite DROP FOREIGN KEY FK_2CBACE2F19EB6921');
        $this->addSql('ALTER TABLE disponibilite CHANGE client_id client_id INT NOT NULL');
        $this->addSql('ALTER TABLE disponibilite ADD CONSTRAINT FK_2CBACE2F19EB6921 FOREIGN KEY (client_id) REFERENCES abonne (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE disponibilite DROP FOREIGN KEY FK_2CBACE2F19EB6921');
        $this->addSql('ALTER TABLE disponibilite CHANGE client_id client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE disponibilite ADD CONSTRAINT FK_2CBACE2F19EB6921 FOREIGN KEY (client_id) REFERENCES abonne (id)');
    }
}

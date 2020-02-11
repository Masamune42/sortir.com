<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200211075625 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A10625D1F6CE84');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D1F6CE84');
        $this->addSql('CREATE TABLE establishment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE etablishement');
        $this->addSql('DROP INDEX IDX_F2A10625D1F6CE84 ON outing');
        $this->addSql('ALTER TABLE outing CHANGE etablishement_id establishment_id INT NOT NULL');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A106258565851 FOREIGN KEY (establishment_id) REFERENCES establishment (id)');
        $this->addSql('CREATE INDEX IDX_F2A106258565851 ON outing (establishment_id)');
        $this->addSql('DROP INDEX IDX_8D93D649D1F6CE84 ON user');
        $this->addSql('ALTER TABLE user CHANGE etablishement_id establishment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498565851 FOREIGN KEY (establishment_id) REFERENCES establishment (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6498565851 ON user (establishment_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE outing DROP FOREIGN KEY FK_F2A106258565851');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498565851');
        $this->addSql('CREATE TABLE etablishement (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE establishment');
        $this->addSql('DROP INDEX IDX_F2A106258565851 ON outing');
        $this->addSql('ALTER TABLE outing CHANGE establishment_id etablishement_id INT NOT NULL');
        $this->addSql('ALTER TABLE outing ADD CONSTRAINT FK_F2A10625D1F6CE84 FOREIGN KEY (etablishement_id) REFERENCES etablishement (id)');
        $this->addSql('CREATE INDEX IDX_F2A10625D1F6CE84 ON outing (etablishement_id)');
        $this->addSql('DROP INDEX IDX_8D93D6498565851 ON user');
        $this->addSql('ALTER TABLE user CHANGE establishment_id etablishement_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D1F6CE84 FOREIGN KEY (etablishement_id) REFERENCES etablishement (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649D1F6CE84 ON user (etablishement_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220129154000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `add` ADD department_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `add` ADD CONSTRAINT FK_FD1A73E7AE80F5DF FOREIGN KEY (department_id) REFERENCES department (id)');
        $this->addSql('CREATE INDEX IDX_FD1A73E7AE80F5DF ON `add` (department_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `add` DROP FOREIGN KEY FK_FD1A73E7AE80F5DF');
        $this->addSql('DROP INDEX IDX_FD1A73E7AE80F5DF ON `add`');
        $this->addSql('ALTER TABLE `add` DROP department_id');
    }
}

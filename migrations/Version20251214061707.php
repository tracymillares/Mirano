<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251214061707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation_menu DROP FOREIGN KEY FK_6B3CF7FBB83297E7');
        $this->addSql('ALTER TABLE reservation_menu DROP FOREIGN KEY FK_6B3CF7FBCCD7E912');
        $this->addSql('DROP TABLE reservation_menu');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation_menu (reservation_id INT NOT NULL, menu_id INT NOT NULL, INDEX IDX_6B3CF7FBCCD7E912 (menu_id), INDEX IDX_6B3CF7FBB83297E7 (reservation_id), PRIMARY KEY(reservation_id, menu_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE reservation_menu ADD CONSTRAINT FK_6B3CF7FBB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation_menu ADD CONSTRAINT FK_6B3CF7FBCCD7E912 FOREIGN KEY (menu_id) REFERENCES menu (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}

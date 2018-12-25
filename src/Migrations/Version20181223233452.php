<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181223233452 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery CHANGE delivery_date delivery_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE order_date order_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE shipping CHANGE shipping_date shipping_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE token ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5F37A13BA76ED395 ON token (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery CHANGE delivery_date delivery_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE order_date order_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE shipping CHANGE shipping_date shipping_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13BA76ED395');
        $this->addSql('DROP INDEX IDX_5F37A13BA76ED395 ON token');
        $this->addSql('ALTER TABLE token DROP user_id');
    }
}

<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181224170444 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery CHANGE delivery_date delivery_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` DROP cargo_code, CHANGE order_date order_date DATETIME NOT NULL, CHANGE max_shipping_date max_shipping_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE order_status ADD delivery_date DATETIME NOT NULL, ADD shipping_code VARCHAR(100) NOT NULL, DROP order_date');
        $this->addSql('ALTER TABLE shipping CHANGE shipping_date shipping_date DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE delivery CHANGE delivery_date delivery_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD cargo_code VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE order_date order_date DATETIME NOT NULL, CHANGE max_shipping_date max_shipping_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE order_status ADD order_date DATETIME NOT NULL, DROP delivery_date, DROP shipping_code');
        $this->addSql('ALTER TABLE shipping CHANGE shipping_date shipping_date DATETIME NOT NULL');
    }
}

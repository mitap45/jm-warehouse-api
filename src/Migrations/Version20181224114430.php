<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181224114430 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE delivery CHANGE delivery_date delivery_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE order_date order_date DATETIME NOT NULL, CHANGE max_shipping_date max_shipping_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE order_product ADD product_id INT DEFAULT NULL, DROP code');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_2530ADE64584665A ON order_product (product_id)');
        $this->addSql('ALTER TABLE shipping CHANGE shipping_date shipping_date DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE64584665A');
        $this->addSql('DROP TABLE product');
        $this->addSql('ALTER TABLE delivery CHANGE delivery_date delivery_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` CHANGE order_date order_date DATETIME NOT NULL, CHANGE max_shipping_date max_shipping_date DATETIME NOT NULL');
        $this->addSql('DROP INDEX IDX_2530ADE64584665A ON order_product');
        $this->addSql('ALTER TABLE order_product ADD code VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, DROP product_id');
        $this->addSql('ALTER TABLE shipping CHANGE shipping_date shipping_date DATETIME NOT NULL');
    }
}

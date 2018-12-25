<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181224095457 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE order_product (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, code VARCHAR(100) NOT NULL, amount INT NOT NULL, INDEX IDX_2530ADE68D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_status (id INT AUTO_INCREMENT NOT NULL, order_id INT DEFAULT NULL, status VARCHAR(100) NOT NULL, INDEX IDX_B88F75C98D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE68D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_status ADD CONSTRAINT FK_B88F75C98D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE product');
        $this->addSql('ALTER TABLE delivery CHANGE delivery_date delivery_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD ecommerce_company_id INT DEFAULT NULL, ADD cargo_company_id INT DEFAULT NULL, CHANGE order_date order_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939865428AAA FOREIGN KEY (ecommerce_company_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398BAE7EFFE FOREIGN KEY (cargo_company_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_F529939865428AAA ON `order` (ecommerce_company_id)');
        $this->addSql('CREATE INDEX IDX_F5299398BAE7EFFE ON `order` (cargo_company_id)');
        $this->addSql('ALTER TABLE shipping CHANGE shipping_date shipping_date DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(100) NOT NULL COLLATE utf8mb4_unicode_ci, amount INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('DROP TABLE order_status');
        $this->addSql('ALTER TABLE delivery CHANGE delivery_date delivery_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939865428AAA');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398BAE7EFFE');
        $this->addSql('DROP INDEX IDX_F529939865428AAA ON `order`');
        $this->addSql('DROP INDEX IDX_F5299398BAE7EFFE ON `order`');
        $this->addSql('ALTER TABLE `order` DROP ecommerce_company_id, DROP cargo_company_id, CHANGE order_date order_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE shipping CHANGE shipping_date shipping_date DATETIME NOT NULL');
    }
}

<?php

declare(strict_types=1);

namespace App\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220519220224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE authentication (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', password VARCHAR(60) NOT NULL, PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', tenant_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, deleted_datetime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_datetime datetime DEFAULT CURRENT_TIMESTAMP, updated_datetime datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX IDX_57698A6A9033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tenant (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, deleted_datetime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_datetime datetime DEFAULT CURRENT_TIMESTAMP, updated_datetime datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, UNIQUE INDEX UNIQ_4E59C462989D9B62 (slug), INDEX name_idx (name), INDEX slug_idx (slug), INDEX name_slug_idx (name, is_active), INDEX active_slug_idx (slug, is_active), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tenant_auth_provider (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', tenant_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', provider_type_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', options JSON DEFAULT NULL, deleted_datetime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_datetime datetime DEFAULT CURRENT_TIMESTAMP, updated_datetime datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX IDX_8C6F73EE9033212A (tenant_id), INDEX IDX_8C6F73EE35142E34 (provider_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tenant_auth_provider_type (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', handle VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', tenant_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(255) NOT NULL, remember_token VARCHAR(100) DEFAULT NULL, is_active TINYINT(1) NOT NULL, deleted_datetime DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_datetime datetime DEFAULT CURRENT_TIMESTAMP, updated_datetime datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX IDX_8D93D6499033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_roles (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', role_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_54FCD59FA76ED395 (user_id), INDEX IDX_54FCD59FD60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_detail (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, given_name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, position VARCHAR(255), home_tel VARCHAR(255) DEFAULT NULL, mobile_tel VARCHAR(255) DEFAULT NULL, work_tel VARCHAR(255) DEFAULT NULL, other_tel LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE authentication ADD CONSTRAINT FK_FEB4C9FDA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE tenant_auth_provider ADD CONSTRAINT FK_8C6F73EE9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE tenant_auth_provider ADD CONSTRAINT FK_8C6F73EE35142E34 FOREIGN KEY (provider_type_id) REFERENCES tenant_auth_provider_type (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59FD60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE user_detail ADD CONSTRAINT FK_4B5464AEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');

        $this->addSql(
            'INSERT IGNORE INTO tenant_auth_provider_type (id, handle, description) VALUES (?, ?, ?)',
            ['8c8f3f38-5274-4327-8504-a4cb1a048852', 'basic', 'Basic DB']
        );

        $this->addDefaultRoles();
    }

    private function addDefaultRoles()
    {
        $query = '
            INSERT IGNORE INTO role (id, name, description, created_datetime, updated_datetime)
            VALUES (:id, :name, :description, now(), now())
        ';

        $roles = [
            ['id' => 'caf531ee-5bd2-400b-b0ba-42c92e1bf689', 'name' => 'user', 'description' => 'User'],
            ['id' => 'c516fcd2-8b2f-45a9-9f8f-a62307dd80c7', 'name' => 'admin', 'description' => 'Admin'],
            ['id' => '5c46a10a-ca58-40ac-b25c-d03e2fec26c6', 'name' => 'editor', 'description' => 'Editor'],
        ];

        foreach ($roles as $role) {
            $this->addSql($query, $role);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FD60322AC');
        $this->addSql('ALTER TABLE role DROP FOREIGN KEY FK_57698A6A9033212A');
        $this->addSql('ALTER TABLE tenant_auth_provider DROP FOREIGN KEY FK_8C6F73EE9033212A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499033212A');
        $this->addSql('ALTER TABLE tenant_auth_provider DROP FOREIGN KEY FK_8C6F73EE35142E34');
        $this->addSql('ALTER TABLE authentication DROP FOREIGN KEY FK_FEB4C9FDA76ED395');
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59FA76ED395');
        $this->addSql('ALTER TABLE user_detail DROP FOREIGN KEY FK_4B5464AEA76ED395');
        $this->addSql('DROP TABLE authentication');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE tenant');
        $this->addSql('DROP TABLE tenant_auth_provider');
        $this->addSql('DROP TABLE tenant_auth_provider_type');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_roles');
        $this->addSql('DROP TABLE user_detail');
    }
}

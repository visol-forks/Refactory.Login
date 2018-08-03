<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration:
 */
class Version20140205224948 extends AbstractMigration
{

    /**
     * @param Schema $schema
     * @return void
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("CREATE TABLE refactory_login_domain_model_resetpasswordtoken (persistence_object_identifier VARCHAR(40) NOT NULL, account VARCHAR(40) DEFAULT NULL, date DATETIME NOT NULL, token VARCHAR(255) NOT NULL, ip VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, INDEX IDX_6D7424807D3656A4 (account), PRIMARY KEY(persistence_object_identifier))");
        $this->addSql("ALTER TABLE refactory_login_domain_model_resetpasswordtoken ADD CONSTRAINT FK_6D7424807D3656A4 FOREIGN KEY (account) REFERENCES typo3_flow_security_account (persistence_object_identifier)");
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("DROP TABLE refactory_login_domain_model_resetpasswordtoken");
        $this->addSql("ALTER TABLE refactory_login_domain_model_resetpasswordtoken DROP FOREIGN KEY FK_6D7424807D3656A4");
    }
}

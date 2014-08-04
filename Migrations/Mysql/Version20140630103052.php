<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
	Doctrine\DBAL\Schema\Schema;

/**
 * Migration: User Registry
 */
class Version20140630103052 extends AbstractMigration {

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function up(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("CREATE TABLE refactory_login_domain_model_userregistry (persistence_object_identifier VARCHAR(40) NOT NULL, person VARCHAR(40) DEFAULT NULL, date DATETIME NOT NULL, token VARCHAR(255) NOT NULL, ip VARCHAR(255) NOT NULL, credentialssource VARCHAR(255) NOT NULL, accountverified TINYINT(1) NOT NULL, INDEX IDX_1C7DF2A234DCD176 (person), PRIMARY KEY(persistence_object_identifier))");
		$this->addSql("ALTER TABLE refactory_login_domain_model_userregistry ADD CONSTRAINT FK_1C7DF2A234DCD176 FOREIGN KEY (person) REFERENCES typo3_party_domain_model_person (persistence_object_identifier)");
	}

	/**
	 * @param Schema $schema
	 * @return void
	 */
	public function down(Schema $schema) {
		$this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

		$this->addSql("DROP TABLE refactory_login_domain_model_userregistry");
	}
}
?>
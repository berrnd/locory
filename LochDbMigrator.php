<?php

class LochDbMigrator
{
	public static function MigrateDb(PDO $pdo)
	{
		self::ExecuteMigrationWhenNeeded($pdo, 1, "
			CREATE TABLE IF NOT EXISTS locationpoints (
				id INT(11) NOT NULL AUTO_INCREMENT,
				time DATETIME NOT NULL,
				latitude DECIMAL(10, 8) NOT NULL,
				longitude DECIMAL(11, 8) NOT NULL,
				accuracy DECIMAL(8, 3) NOT NULL,
				import_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				distance_to_point_before INT(11) DEFAULT NULL,
				PRIMARY KEY (id),
				KEY time (time)
			)"
		);
	}

	private static function ExecuteMigrationWhenNeeded(PDO $pdo, int $migrationId, string $sql)
	{
		$rowCount = $pdo->query('SELECT COUNT(*) FROM migrations WHERE migration = ' . $migrationId)->fetchColumn();
		if (intval($rowCount) === 0)
		{
			$pdo->exec(utf8_encode($sql));
			$statement = $pdo->prepare('INSERT INTO migrations (migration) VALUES (:id)');
			$statement->bindValue(':id', $migrationId);
			$statement->execute();
		}
	}
}

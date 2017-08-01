<?php

use Location\Coordinate;
use Location\Distance\Vincenty;

class Locory
{
	private static $DbConnection;
	/**
	 * @return PDO
	 */
	public static function GetDbConnection($doMigrations = false)
	{
		if ($doMigrations === true)
		{
			self::$DbConnection = null;
		}

		if (self::$DbConnection == null)
		{
			self::$DbConnection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
			self::$DbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			if ($doMigrations === true)
			{
				self::$DbConnection->exec("CREATE TABLE IF NOT EXISTS migrations (migration SMALLINT NOT NULL, execution_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (migration))");
				LocoryDbMigrator::MigrateDb(self::$DbConnection);

				if (self::IsDemoInstallation())
				{
					LocoryDemoDataGenerator::PopulateDemoData(self::$DbConnection);
				}
			}
		}

		return self::$DbConnection;
	}

	public static function AddLocationPoint($time, $latitude, $longitude, $accuracy)
	{
		$db = self::GetDbConnection();

		$statement = $db->prepare('INSERT INTO locationpoints (time, latitude, longitude, accuracy) VALUES (:time, :latitude, :longitude, :accuracy)');
		$statement->bindValue(':time', $time);
		$statement->bindValue(':latitude', $latitude);
		$statement->bindValue(':longitude', $longitude);
		$statement->bindValue(':accuracy', $accuracy);

		$statement->execute();
	}

	public static function AddCsvData($csvString)
	{
		$lines = explode(PHP_EOL, $csvString);

		foreach ($lines as $line)
		{
			if (!empty($line))
			{
				$parsedLine = str_getcsv($line);
				self::AddLocationPoint($parsedLine[0], $parsedLine[1], $parsedLine[2], $parsedLine[3]);
			}
		}
	}

	public static function GetLocationPoints($from, $to)
	{
		$db = self::GetDbConnection();

		$statement = $db->prepare('SELECT * FROM locationpoints WHERE time >= :from AND time <= :to');
		$statement->bindValue(':from', $from);
		$statement->bindValue(':to', $to);
		$statement->execute();

		$rows = array();
		while ($row = $statement->fetch(PDO::FETCH_ASSOC))
		{
			$rows[] = $row;
		}

		return $rows;
	}

	public static function GetLocationPointStatistics($from, $to)
	{
		$db = self::GetDbConnection();

		$statement = $db->prepare('SELECT MIN(accuracy) AS AccuracyMin, MAX(accuracy) AS AccuracyMax, AVG(accuracy) AS AccuracyAverage, SUM(distance_to_point_before) AS Distance FROM locationpoints WHERE time >= :from AND time <= :to');
		$statement->bindValue(':from', $from);
		$statement->bindValue(':to', $to);
		$statement->execute();

		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	public static function CalculateLocationPointDistances()
	{
		$db = self::GetDbConnection();
		$distanceCalculator = new Vincenty();

		$statementNotCalculatedRows = $db->prepare('SELECT id, time, latitude, longitude FROM locationpoints WHERE distance_to_point_before IS NULL ORDER BY time');
		$statementNotCalculatedRows->execute();

		$idPreviousRow = null;
		while ($row = $statementNotCalculatedRows->fetch(PDO::FETCH_ASSOC))
		{
			if ($idPreviousRow == null)
			{
				//Try to get the row before, should only happen once when starting a new calculation

				$statementRowBefore = $db->prepare('SELECT id, latitude, longitude FROM locationpoints WHERE time < :time ORDER BY time DESC LIMIT 1');
				$statementRowBefore->bindValue(':time', $row['time']);
				$statementRowBefore->execute();
				$rowBefore = $statementRowBefore->fetch(PDO::FETCH_ASSOC);

				$idPreviousRow = $rowBefore['id'];
				$latitudePreviousRow = $rowBefore['latitude'];
				$longitudePreviousRow = $rowBefore['longitude'];
			}

			if ($idPreviousRow != null)
			{
				$coordinatePrevious = new Coordinate($latitudePreviousRow, $longitudePreviousRow);
				$coordinateCurrent = new Coordinate($row['latitude'], $row['longitude']);
				$distance = $distanceCalculator->getDistance($coordinatePrevious, $coordinateCurrent);

				$statementUpdate = $db->prepare('UPDATE locationpoints SET distance_to_point_before = :distance WHERE id = :id');
				$statementUpdate->bindValue(':distance', $distance);
				$statementUpdate->bindValue(':id', $row['id']);
				$statementUpdate->execute();
			}

			$idPreviousRow = $row['id'];
			$latitudePreviousRow = $row['latitude'];
			$longitudePreviousRow = $row['longitude'];
		}
	}

	/**
	 * @return boolean
	 */
	public static function IsDemoInstallation()
	{
		return file_exists(__DIR__ . '/data/demo.txt');
	}

	private static $InstalledVersion;
	/**
	 * @return string
	 */
	public static function GetInstalledVersion()
	{
		if (self::$InstalledVersion == null)
		{
			self::$InstalledVersion = file_get_contents(__DIR__ . '/version.txt');
		}

		return self::$InstalledVersion;
	}

	/**
	 * @return boolean
	 */
	public static function IsValidSession($sessionKey)
	{
		if ($sessionKey === null || empty($sessionKey))
		{
			return false;
		}
		else
		{
			return file_exists(__DIR__ . "/data/sessions/$sessionKey.txt");
		}
	}

	/**
	 * @return string
	 */
	public static function CreateSession()
	{
		if (!file_exists(__DIR__ . '/data/sessions'))
		{
			mkdir(__DIR__ . '/data/sessions');
		}

		$now = time();
		foreach (new FilesystemIterator(__DIR__ . '/data/sessions') as $file)
		{
			if ($now - $file->getCTime() >= 2678400) //31 days
			{
				unlink(__DIR__ . '/data/sessions/' . $file->getFilename());
			}
		}

		$newSessionKey = uniqid() . uniqid() . uniqid();
		file_put_contents(__DIR__ . "/data/sessions/$newSessionKey.txt", '');
		return $newSessionKey;
	}

	public static function RemoveSession($sessionKey)
	{
		unlink(__DIR__ . "/data/sessions/$sessionKey.txt");
	}
}

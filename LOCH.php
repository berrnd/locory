<?php

use Location\Coordinate;
use Location\Distance\Vincenty;

require_once 'config.php';

class LOCH
{
	private $DbConnection;

	function GetDbConnection()
	{
		if ($this->DbConnection == null)
		{
			$this->DbConnection = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
		}

		return $this->DbConnection;
	}

	function AddLocationPoint($time, $latitude, $longitude, $accuracy)
	{
		$db = $this->GetDbConnection();

		$statement = $db->prepare('INSERT INTO locationpoints (time, latitude, longitude, accuracy) VALUES (:time, :latitude, :longitude, :accuracy)');
		$statement->bindValue(':time', $time);
		$statement->bindValue(':latitude', $latitude);
		$statement->bindValue(':longitude', $longitude);
		$statement->bindValue(':accuracy', $accuracy);

		$statement->execute();
	}

	function AddCsvData($csvString)
	{
		$lines = explode(PHP_EOL, $csvString);

		foreach ($lines as $line)
		{
			$parsedLine = str_getcsv($line);
			$this->AddLocationPoint($parsedLine[0], $parsedLine[1], $parsedLine[2], $parsedLine[3]);
		}
	}

	function GetLocationPoints($from, $to)
	{
		$db = $this->GetDbConnection();

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

	function GetLocationPointStatistics($from, $to)
	{
		$db = $this->GetDbConnection();

		$statement = $db->prepare('SELECT MIN(accuracy) AS AccuracyMin, MAX(accuracy) AS AccuracyMax, AVG(accuracy) AS AccuracyAverage, SUM(distance_to_point_before) AS Distance FROM locationpoints WHERE time >= :from AND time <= :to');
		$statement->bindValue(':from', $from);
		$statement->bindValue(':to', $to);
		$statement->execute();

		return $statement->fetch(PDO::FETCH_ASSOC);
	}

	function CalculateLocationPointDistances()
	{
		$db = $this->GetDbConnection();
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
}

<?php

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
		while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
			$rows[] = $row;
		}

		return $rows;
	}
}

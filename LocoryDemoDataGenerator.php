<?php

class LocoryDemoDataGenerator
{
	public static function PopulateDemoData(PDO $pdo)
	{
		$rowCount = $pdo->query('SELECT COUNT(*) FROM migrations WHERE migration = -1')->fetchColumn();
		if (intval($rowCount) === 0)
		{
			$sql = "
				INSERT INTO migrations (migration) VALUES (-1);
			";
			$pdo->exec(utf8_encode($sql));

			$csvTrack1 = file_get_contents(__DIR__ . '/data/demotrack1.csv');
			$csvTrack2 = file_get_contents(__DIR__ . '/data/demotrack2.csv');
			$csvTrack3 = file_get_contents(__DIR__ . '/data/demotrack3.csv');
			$csvTrack4 = file_get_contents(__DIR__ . '/data/demotrack4.csv');

			$startDateForTrack1 = new DateTime();
			$startDateForTrack1->modify('-4 day');
			$startDateForTrack1->setTime(0, 0, 0);

			$startDateForTrack2 = new DateTime();
			$startDateForTrack2->modify('-3 day');
			$startDateForTrack2->setTime(0, 0, 0);

			$startDateForTrack3 = new DateTime();
			$startDateForTrack3->modify('-2 day');
			$startDateForTrack3->setTime(0, 0, 0);

			$startDateForTrack4 = new DateTime();
			$startDateForTrack4->modify('-1 day');
			$startDateForTrack4->setTime(0, 0, 0);

			$linesTrack1 = explode(PHP_EOL, $csvTrack1);
			foreach ($linesTrack1 as $line)
			{
				$startDateForTrack1->modify('+1 second');
				$line = str_replace('TIME', $startDateForTrack1->format('Y-m-d H:i:s'), $line);
				$parsedLine = str_getcsv($line);
				Locory::AddLocationPoint($parsedLine[0], $parsedLine[1], $parsedLine[2], $parsedLine[3]);
			}

			$linesTrack2 = explode(PHP_EOL, $csvTrack2);
			foreach ($linesTrack2 as $line)
			{
				$startDateForTrack2->modify('+1 second');
				$line = str_replace('TIME', $startDateForTrack2->format('Y-m-d H:i:s'), $line);
				$parsedLine = str_getcsv($line);
				Locory::AddLocationPoint($parsedLine[0], $parsedLine[1], $parsedLine[2], $parsedLine[3]);
			}

			$linesTrack3 = explode(PHP_EOL, $csvTrack3);
			foreach ($linesTrack3 as $line)
			{
				$startDateForTrack3->modify('+1 second');
				$line = str_replace('TIME', $startDateForTrack3->format('Y-m-d H:i:s'), $line);
				$parsedLine = str_getcsv($line);
				Locory::AddLocationPoint($parsedLine[0], $parsedLine[1], $parsedLine[2], $parsedLine[3]);
			}

			$linesTrack4 = explode(PHP_EOL, $csvTrack4);
			foreach ($linesTrack4 as $line)
			{
				$startDateForTrack4->modify('+1 second');
				$line = str_replace('TIME', $startDateForTrack4->format('Y-m-d H:i:s'), $line);
				$parsedLine = str_getcsv($line);
				Locory::AddLocationPoint($parsedLine[0], $parsedLine[1], $parsedLine[2], $parsedLine[3]);
			}

			Locory::CalculateLocationPointDistances();
		}
	}

	public static function RecreateDemo()
	{
		$db = Locory::GetDbConnection();
		$db->exec('TRUNCATE TABLE migrations');
		$db->exec('TRUNCATE TABLE locationpoints');

		$db = Locory::GetDbConnection(true);
		self::PopulateDemoData($db);
	}
}

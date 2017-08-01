<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<meta name="robots" content="noindex,nofollow" />

	<meta name="author" content="Bernd Bestel (bernd@berrnd.de)" />
	<link rel="icon" href="/locory.png" />

	<title><?php echo $title; ?> | locory</title>

	<link href="/bower_components/bootstrap/dist/css/bootstrap.min.css?v=<?php echo Locory::GetInstalledVersion(); ?>" rel="stylesheet" />
	<link href="/bower_components/font-awesome/css/font-awesome.min.css?v=<?php echo Locory::GetInstalledVersion(); ?>" rel="stylesheet" />
	<link href="/bower_components/leaflet/dist/leaflet.css?v=<?php echo Locory::GetInstalledVersion(); ?>" rel="stylesheet" />
	<link href="/bower_components/bootstrap-daterangepicker/daterangepicker.css?v=<?php echo Locory::GetInstalledVersion(); ?>" rel="stylesheet" />
	<link href="/style.css?v=<?php echo Locory::GetInstalledVersion(); ?>" rel="stylesheet" />

	<script src="/bower_components/jquery/dist/jquery.min.js?v=<?php echo Locory::GetInstalledVersion(); ?>"></script>
	<script src="/locory.js?v=<?php echo Locory::GetInstalledVersion(); ?>"></script>
</head>

<body>
	<nav class="navbar navbar-default navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-mobile" >
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">locory</a>
			</div>

			<div id="navbar" class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
					<li>
						<a class="discrete-link logout-button" href="/logout"><i class="fa fa-sign-out fa-fw"></i>&nbsp;Logout</a>
					</li>
				</ul>
			</div>

			<div id="navbar-mobile" class="navbar-collapse collapse">

				<ul class="nav navbar-nav navbar-right">
					<li data-nav-for-page="dashboard.php">
						<a class="discrete-link" href="/"><i class="fa fa-tachometer fa-fw"></i>&nbsp;Dashboard</a>
					</li>
				</ul>

				<ul class="nav navbar-nav navbar-right">
					<li>
						<a class="discrete-link logout-button" href="/logout"><i class="fa fa-sign-out fa-fw"></i>&nbsp;Logout</a>
					</li>
				</ul>

			</div>
		</div>
	</nav>

	<div class="container-fluid">
		<div class="row">

			<div class="col-sm-3 col-md-2 sidebar">

				<ul class="nav nav-sidebar">
					<li data-nav-for-page="dashboard.php">
						<a class="discrete-link" href="/"><i class="fa fa-tachometer fa-fw"></i>&nbsp;Dashboard</a>
					</li>
				</ul>

				<div class="nav-copyright nav nav-sidebar">
					locory is a project by
					<a class="discrete-link" href="https://berrnd.de" target="_blank">Bernd Bestel</a>
					<br />
					Created with passion since 2016
					<br />
					Version <?php echo Locory::GetInstalledVersion(); ?>
					<br />
					Life runs on code
					<br />
					<a class="discrete-link" href="https://github.com/berrnd/locory" target="_blank">
						<i class="fa fa-github"></i>
					</a>
				</div>

			</div>

			<script>Locory.ContentPage = '<?php echo $contentPage; ?>';</script>
			<?php include $contentPage; ?>

		</div>
	</div>

	<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js?v=<?php echo Locory::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/moment/min/moment.min.js?v=<?php echo Locory::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/jquery-timeago/jquery.timeago.js?v=<?php echo Locory::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/bootstrap-daterangepicker/daterangepicker.js?v=<?php echo Locory::GetInstalledVersion(); ?>"></script>
	<script src="/bower_components/leaflet/dist/leaflet.js?v=<?php echo Locory::GetInstalledVersion(); ?>"></script>

	<?php if (file_exists(__DIR__ . '/' . str_replace('.php', '.js', $contentPage))) : ?>
		<script src="/views/<?php echo str_replace('.php', '.js', $contentPage) . '?v=' . Locory::GetInstalledVersion(); ?>"></script>
	<?php endif; ?>

	<?php if (file_exists(__DIR__ . '/../data/add_before_end_body.html')) include __DIR__ . '/../data/add_before_end_body.html' ?>
</body>
</html>

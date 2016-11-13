<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<meta name="robots" content="noindex,nofollow" />

	<meta name="author" content="Bernd Bestel (bernd@berrnd.de)" />

	<title>LOCH</title>

	<link href="/vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link href="/vendor/components/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="/vendor/drmonty/leaflet/css/leaflet.css" rel="stylesheet" />
	<link href="/fonts/open-sans.css" rel="stylesheet" />
	<link href="/style.css" rel="stylesheet" />
</head>

<body>
	<div class="site-wrapper">
		<div class="site-wrapper-inner">
			<div class="content-container center">
				<div class="inner content">

					<h1 class="content-heading">LOCH</h1>
					<div class="little-more-space"></div>

					<div class="content-separator"></div>

					<form class="form-inline">
						<div class="form-group">
							<label for="inputFrom" class="control-label">From</label>
							<input type="date" class="form-control" id="inputFrom" placeholder="From">
						</div>
						<div class="form-group">
							<label for="inputTo" class="control-label">To</label>
							<input type="date" class="form-control" id="inputTo" placeholder="To">
						</div>
					</form>

					<div class="little-more-space"></div>

					<div>

						<div class="content-separator"></div>

						<div id="map"></div>

					</div>

					<div class="footer">

						<div class="copyright">
							LOCH is a project by <a class="discrete-link" href="https://berrnd.de" targete="_blank">Bernd Bestel</a>
							<br />
							Created with passion since 2016
							<br />
							Version <?php echo file_get_contents('version.txt'); ?>
							<br />
							Life runs on code
							<br />
							<a class="discrete-link" href="https://github.com/berrnd/LOCH" target="_blank"><i class="fa fa-github"></i></a>
						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
	<script src="/LOCH.js"></script>
	<script src="/vendor/drmonty/leaflet/js/leaflet.min.js"></script>
	<script>
		LOCH.Map = L.map('map');
		LOCH.LocationPointsLayer = new L.FeatureGroup();
		LOCH.Map.addLayer(LOCH.LocationPointsLayer);

		var defaultFrom = new Date().addDays(-1).toISOString().substring(0, 10);
		var defaultTo = new Date().toISOString().substring(0, 10);

		document.getElementById('inputFrom').value = defaultFrom;
		document.getElementById('inputTo').value = defaultTo;

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: 'Map data &copy; <a target="_blank" href="https://www.openstreetmap.org">OpenStreetMap</a> contributors',
			maxZoom: 18
		}).addTo(LOCH.Map);

		document.getElementById('inputFrom').addEventListener('input', function () {
			LoadLocationPoints();
		});

		document.getElementById('inputTo').addEventListener('input', function () {
			LoadLocationPoints();
		});

		LoadLocationPoints();

		function LoadLocationPoints() {
			var from = document.getElementById('inputFrom').value;
			var to = document.getElementById('inputTo').value;

			if (IsDate(from) && IsDate(to)) {
				LOCH.Map.removeLayer(LOCH.LocationPointsLayer);
				LOCH.LocationPointsLayer = new L.FeatureGroup();
				LOCH.Map.addLayer(LOCH.LocationPointsLayer);
				
				LOCH.FetchJson('/api/get/' + from + '/' + to,
					function (points) {
						var mapViewSet = false;
						for (point of points) {
							if (!mapViewSet) {
								LOCH.Map.setView([point.latitude, point.longitude], 13);
								mapViewSet = true;
							}
							L.marker([point.latitude, point.longitude]).addTo(LOCH.LocationPointsLayer);
						}
					},
					function (xhr) {
						console.error(xhr);
					}
				);
			}
		}
	</script>
</body>
</html>

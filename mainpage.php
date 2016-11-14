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
	<link href="/vendor/webfontkit/open-sans/open-sans.min.css" rel="stylesheet" />
	<link href="/vendor/rohnstock/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" />
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

					<div id="daterange">
						<i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
						<span></span>
						<b class="caret"></b>
					</div>

					<div class="little-space"></div>

					<div id="summary" class="well container">
						<strong><span id="summary-location-points"></span></strong> location points - accuracy varies between <strong><span id="summary-accuracy-min"></span> m</strong> and <strong><span id="summary-accuracy-max"></span> m</strong> (average <strong><span id="summary-accuracy-average"></span> m</strong>).
					</div>

					<div class="little-space"></div>

					<div>

						<div class="content-separator"></div>

						<div id="map"></div>

					</div>

					<div class="footer">

						<div class="copyright">
							LOCH is a project by <a class="discrete-link" href="https://berrnd.de" target="_blank">Bernd Bestel</a>
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
	<script src="/vendor/drmonty/leaflet/js/leaflet.min.js"></script>
	<script src="/vendor/components/jquery/jquery.min.js"></script>
	<script src="/vendor/moment/moment/min/moment.min.js"></script>
	<script src="/vendor/rohnstock/bootstrap-daterangepicker/daterangepicker.js"></script>
	<script src="/LOCH.js"></script>
	<script>
		LOCH.SetupMap("map");

		$(function ()
		{
			function SetDateRangeDisplay(start, end)
			{
				$("#daterange span").html(start.format("YYYY-MM-DD") + " - " + end.format("YYYY-MM-DD"));
			}

			$("#daterange").daterangepicker(
			{
				startDate: LOCH.DefaultFrom,
				endDate: LOCH.DefaultTo,
				showWeekNumbers: true,
				alwaysShowCalendars: true,
				showDropdowns: true,
				opens: "center",
				locale:
				{
					format: "YYYY-MM-DD",
					firstDay: 1
				},
				ranges:
				{
					"Today": [moment(), moment()],
					"Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
					"Last 7 Days": [moment().subtract(6, "days"), moment()],
					"Last 30 Days": [moment().subtract(29, "days"), moment()],
					"This Month": [moment().startOf("month"), moment().endOf("month")],
					"Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
				}
			}, SetDateRangeDisplay);

			$("#daterange").on("apply.daterangepicker", function (ev, picker)
			{
				LOCH.Reload(picker.startDate.format("YYYY-MM-DD"), picker.endDate.format("YYYY-MM-DD"));
			});

			SetDateRangeDisplay(LOCH.DefaultFrom, LOCH.DefaultTo);
			LOCH.Reload(LOCH.DefaultFrom.format("YYYY-MM-DD"), LOCH.DefaultTo.format("YYYY-MM-DD"));
		});
	</script>
</body>
</html>

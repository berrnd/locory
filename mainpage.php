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
	<div class="container">
		<div class="row">
			<div class="col-xs-12">

				<h1 class="bold">LOCH</h1>
					
				<div class="discrete-content-separator-2x"></div>
				<div class="content-separator"></div>

				<div id="daterange">
					<i class="fa fa-calendar"></i>&nbsp;
					<span></span>
					<b class="caret"></b><br />
				</div>

				<div class="discrete-content-separator-2x"></div>
				<div class="content-separator"></div>

				<div id="daterange-navigation">
					<a id="daterange-backward" role="button" class="btn btn-default"><i class="fa fa-arrow-left"></i></a>
					<a id="daterange-forward" role="button" class="btn btn-default"><i class="fa fa-arrow-right"></i></a>
				</div>

				<div class="discrete-content-separator-2x"></div>

				<div id="summary" class="well container">
					<p><strong><span id="summary-location-points"></span></strong>&nbsp;location points,&nbsp;<strong><span id="summary-distance"></span>&nbsp;km</strong>&nbsp;total distance - accuracy varies between&nbsp;<strong><span id="summary-accuracy-min"></span>&nbsp;m</strong>&nbsp;and&nbsp;<strong><span id="summary-accuracy-max"></span>&nbsp;m</strong>&nbsp;(average&nbsp;<strong><span id="summary-accuracy-average"></span>&nbsp;m</strong>).</p>
				</div>

				<div class="content-separator"></div>

				<div id="map"></div>

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

			function MoveDateRange(forward)
			{
				picker = $("#daterange").data("daterangepicker");
				var days = moment.duration(picker.endDate.diff(picker.startDate)).asDays().toFixed(0);

				if (forward == false)
				{
					days = days * -1;
				}
				
				var newStartDate = picker.startDate.add(days, "days");
				var newEndDate = picker.endDate.add(days, "days");

				SetupDateRangePicker(newStartDate, newEndDate);
				LOCH.ReloadMap(newStartDate, newEndDate);
			}

			function SetupDateRangePicker(from, to)
			{
				$("#daterange").daterangepicker(
				{
					startDate: from,
					endDate: to,
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

				$("#daterange").on("apply.daterangepicker", function (ev, picker) {
					LOCH.ReloadMap(picker.startDate, picker.endDate);
				});

				SetDateRangeDisplay(from, to);
			}

			$("#daterange-forward").click(function()
			{
				MoveDateRange(true);
			});

			$("#daterange-backward").click(function()
			{
				MoveDateRange(false);
			});

			SetupDateRangePicker(LOCH.DefaultFrom, LOCH.DefaultTo);
			LOCH.ReloadMap(LOCH.DefaultFrom, LOCH.DefaultTo);
		});
	</script>
</body>
</html>

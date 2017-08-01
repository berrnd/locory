Locory.SetupMap("map");

function ReloadMap(from, to)
{
	if (moment.isMoment(from) && moment.isMoment(to))
	{
		Locory.Map.removeLayer(Locory.LocationPointsLayer);
		Locory.LocationPointsLayer = new L.FeatureGroup();
		Locory.Map.addLayer(Locory.LocationPointsLayer);

		Locory.FetchJson("/api/get/locationpoints/" + from.format("YYYY-MM-DD") + "/" + to.format("YYYY-MM-DD"),
			function(points)
			{
				if (points.length > 0)
				{
					for (point of points)
					{
						L.marker([point.latitude, point.longitude]).addTo(Locory.LocationPointsLayer);
					}

					Locory.Map.fitBounds(Locory.LocationPointsLayer.getBounds());
					document.getElementById("summary-location-points").innerText = points.length;

					Locory.FetchJson("/api/get/statistics/" + from.format("YYYY-MM-DD") + "/" + to.format("YYYY-MM-DD"),
						function(statistics)
						{
							document.getElementById("summary-accuracy-min").innerText = parseFloat(statistics.AccuracyMin).toFixed(0);
							document.getElementById("summary-accuracy-max").innerText = parseFloat(statistics.AccuracyMax).toFixed(0);
							document.getElementById("summary-accuracy-average").innerText = parseFloat(statistics.AccuracyAverage).toFixed(0);
							document.getElementById("summary-distance").innerText = (parseFloat(statistics.Distance) / 1000).toFixed(1);
						},
						function(xhr)
						{
							console.error(xhr);
						}
					);

					$("#datainfo").removeClass("hide");
					$("#nodatainfo").addClass("hide");
				}
				else
				{
					$("#datainfo").addClass("hide");
					$("#nodatainfo").removeClass("hide");
				}
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
}

$(function()
{
	function SetDateRangeDisplay(start, end)
	{
		$("#daterange span").html(start.format("YYYY-MM-DD") + " - " + end.format("YYYY-MM-DD"));
	}

	function MoveDateRange(forward)
	{
		picker = $("#daterange").data("daterangepicker");
		var days = moment.duration(picker.endDate.diff(picker.startDate)).asDays().toFixed(0);

		if (forward === false)
		{
			days = days * -1;
		}
				
		var newStartDate = picker.startDate.add(days, "days");
		var newEndDate = picker.endDate.add(days, "days");

		SetupDateRangePicker(newStartDate, newEndDate);
		ReloadMap(newStartDate, newEndDate);
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

		$("#daterange").on("apply.daterangepicker", function(ev, picker)
		{
			ReloadMap(picker.startDate, picker.endDate);
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

	SetupDateRangePicker(Locory.DefaultFrom, Locory.DefaultTo);
	ReloadMap(Locory.DefaultFrom, Locory.DefaultTo);
});

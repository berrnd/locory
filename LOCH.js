function IsDate(date)
{
	var parsedDate = Date.parse(date);

	if (isNaN(date) && !isNaN(parsedDate))
	{
		return true;
	}
	else
	{
		return false;
	}
}

var LOCH = {};
LOCH.DefaultFrom = moment().subtract(1, "days");
LOCH.DefaultTo = moment().subtract(1, "days");

LOCH.FetchJson = function (url, success, error)
{
	var xhr = new XMLHttpRequest();

	xhr.onreadystatechange = function ()
	{
		if (xhr.readyState === XMLHttpRequest.DONE)
		{
			if (xhr.status === 200)
			{
				if (success)
				{
					success(JSON.parse(xhr.responseText));
				}
			}
			else
			{
				if (error)
				{
					error(xhr);
				}
			}
		}
	};

	xhr.open("GET", url, true);
	xhr.send();
}

LOCH.SetupMap = function(mapElementId)
{
	LOCH.Map = L.map(mapElementId);

	L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
		attribution: 'Map data &copy; <a target="_blank" href="https://www.openstreetmap.org">OpenStreetMap</a> contributors',
		maxZoom: 18
	}).addTo(LOCH.Map);

	LOCH.LocationPointsLayer = new L.FeatureGroup();
	LOCH.Map.addLayer(LOCH.LocationPointsLayer);
}

LOCH.Reload = function (from, to)
{
	if (IsDate(from) && IsDate(to))
	{
		LOCH.Map.removeLayer(LOCH.LocationPointsLayer);
		LOCH.LocationPointsLayer = new L.FeatureGroup();
		LOCH.Map.addLayer(LOCH.LocationPointsLayer);

		LOCH.FetchJson("/api/get/locationpoints/" + from + "/" + to,
			function (points)
			{
				var mapViewSet = false;
				for (point of points)
				{
					if (!mapViewSet)
					{
						LOCH.Map.setView([point.latitude, point.longitude], 13);
						mapViewSet = true;
					}
					L.marker([point.latitude, point.longitude]).addTo(LOCH.LocationPointsLayer);
				}

				document.getElementById("summary-location-points").innerText = points.length;

				LOCH.FetchJson("/api/get/statistics/" + from + "/" + to,
					function (statistics)
					{
						document.getElementById("summary-accuracy-min").innerText = parseFloat(statistics.AccuracyMin).toFixed(0);
						document.getElementById("summary-accuracy-max").innerText = parseFloat(statistics.AccuracyMax).toFixed(0);
						document.getElementById("summary-accuracy-average").innerText = parseFloat(statistics.AccuracyAverage).toFixed(0);
					},
					function (xhr)
					{
						console.error(xhr);
					}
				);
			},
			function (xhr)
			{
				console.error(xhr);
			}
		);
	}
}

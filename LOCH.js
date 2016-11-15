var LOCH = {};
LOCH.DefaultFrom = moment().subtract(1, "days");
LOCH.DefaultTo = moment().subtract(1, "days");

LOCH.FetchJson = function(url, success, error)
{
	var xhr = new XMLHttpRequest();

	xhr.onreadystatechange = function()
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

	L.tileLayer("https://osm-tile-cache.berrnd.org/{z}/{x}/{y}.png", {
		attribution: 'Map data &copy; <a target="_blank" href="https://www.openstreetmap.org">OpenStreetMap</a> contributors',
		maxZoom: 18
	}).addTo(LOCH.Map);

	LOCH.LocationPointsLayer = new L.FeatureGroup();
	LOCH.Map.addLayer(LOCH.LocationPointsLayer);
}

LOCH.ReloadMap = function(from, to)
{
	if (moment.isMoment(from) && moment.isMoment(to))
	{
		LOCH.Map.removeLayer(LOCH.LocationPointsLayer);
		LOCH.LocationPointsLayer = new L.FeatureGroup();
		LOCH.Map.addLayer(LOCH.LocationPointsLayer);

		LOCH.FetchJson("/api/get/locationpoints/" + from.format("YYYY-MM-DD") + "/" + to.format("YYYY-MM-DD"),
			function(points)
			{
				for (point of points)
				{
					L.marker([point.latitude, point.longitude]).addTo(LOCH.LocationPointsLayer);
				}

				LOCH.Map.fitBounds(LOCH.LocationPointsLayer.getBounds());
				document.getElementById("summary-location-points").innerText = points.length;

				LOCH.FetchJson("/api/get/statistics/" + from.format("YYYY-MM-DD") + "/" + to.format("YYYY-MM-DD"),
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
			},
			function(xhr)
			{
				console.error(xhr);
			}
		);
	}
}

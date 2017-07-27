var LOCH = {};

$(function()
{
	var menuItem = $('.nav').find("[data-nav-for-page='" + LOCH.ContentPage + "']");
	menuItem.addClass('active');

	LOCH.DefaultFrom = moment().subtract(1, "days");
	LOCH.DefaultTo = moment().subtract(1, "days");

	$.timeago.settings.allowFuture = true;
	$('time.timeago').timeago();
});

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

LOCH.GetUriParam = function(key)
{
	var currentUri = decodeURIComponent(window.location.search.substring(1));
	var vars = currentUri.split('&');

	for (i = 0; i < vars.length; i++)
	{
		var currentParam = vars[i].split('=');

		if (currentParam[0] === key)
		{
			return currentParam[1] === undefined ? true : currentParam[1];
		}
	}
};

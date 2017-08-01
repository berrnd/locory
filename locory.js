var Locory = {};

$(function()
{
	var menuItem = $('.nav').find("[data-nav-for-page='" + Locory.ContentPage + "']");
	menuItem.addClass('active');

	Locory.DefaultFrom = moment().subtract(1, "days");
	Locory.DefaultTo = moment().subtract(1, "days");

	$.timeago.settings.allowFuture = true;
	$('time.timeago').timeago();
});

Locory.FetchJson = function(url, success, error)
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

Locory.SetupMap = function(mapElementId)
{
	Locory.Map = L.map(mapElementId);

	L.tileLayer("https://osm-tile-cache.berrnd.org/{z}/{x}/{y}.png", {
		attribution: 'Map data &copy; <a target="_blank" href="https://www.openstreetmap.org">OpenStreetMap</a> contributors',
		maxZoom: 18
	}).addTo(Locory.Map);

	Locory.LocationPointsLayer = new L.FeatureGroup();
	Locory.Map.addLayer(Locory.LocationPointsLayer);
}

Locory.GetUriParam = function(key)
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

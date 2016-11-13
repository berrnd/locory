Date.prototype.addDays = function (days) {
	var date = new Date(this.valueOf());
	date.setDate(date.getDate() + days);
	return date;
}

function IsDate(date) {
	var parsedDate = Date.parse(date);

	if (isNaN(date) && !isNaN(parsedDate)) {
		return true;
	}
	else {
		return false;
	}
}

var LOCH = { };

LOCH.FetchJson = function (url, success, error) {
	var xhr = new XMLHttpRequest();

	xhr.onreadystatechange = function () {
		if (xhr.readyState === XMLHttpRequest.DONE) {
			if (xhr.status === 200) {
				if (success) {
					success(JSON.parse(xhr.responseText));
				}
			} else {
				if (error) {
					error(xhr);
				}
			}
		}
	};

	xhr.open("GET", url, true);
	xhr.send();
}

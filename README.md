# locory
A private / self-hosted location history solution

## Motivation
I love to have a location history, but because such data is too private to let this do others for you, I searched for a self hosted solution, found nothing that fitted my needs completely, so this is locory.

# Give it a try
Public demo of the latest version &rarr; [https://locory-demo.berrnd.xyz](https://locory-demo.berrnd.xyz)

## How to install
Just unpack the [latest release](https://github.com/berrnd/locory/releases/latest) on your PHP enabled webserver, copy `config-dist.php` to `data/config.php`, edit it to your needs, create the new database on the server you configured, ensure that the `data` directory is writable and you're ready to go. Alternatively clone this repository and install composer dependencies manually.

If you use nginx as your webserver, please include `try_files $uri /index.php;` in your location block.

## What currently is possible
- It provides an API to save location data
- It gives you a simple web interface to show your location history in custom time ranges, calculates the (linear) distance, and so on

### Get data in
Make a POST to `https://locory/api/add/csv`, the body has to be one or multiple lines in the format `<parseable timestamp (ISO is good)>,<latitude>,<longitude>,<accuracy (integer, in meters)>`.
I personally do this with [Automagic](https://play.google.com/store/apps/details?id=ch.gridvision.ppam.androidautomagic) on my smartphone.

### Calculate distance between location points
This can be a long running task, so this is not done at the time of import - just setup a cron job for example every hour.

`*/60 * * * * www-data php /<locory>/index.php /cli/calculate/distance GET`

### Show your location history
Just browse to the your install URL and login with the configured credentials

## Screenshots
![Main page](https://github.com/berrnd/locory/raw/master/publication_assets/mainpage.png "Main page")

## Todo
The analysis features are very basic for now, there are much more great things, heatmaps, "where spent I most of my time", and so on...

## License
The MIT License (MIT)

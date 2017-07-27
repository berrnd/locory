<div class="col-xs-10 col-sm-offset-2 col-xs-offset-0 main">

	<h1 class="page-header"><?php echo $title; ?>&nbsp;<span id="datainfo" class="text-muded small"><strong><span id="summary-location-points"></span></strong>&nbsp;location points,&nbsp;<strong><span id="summary-distance"></span>&nbsp;km</strong>&nbsp;total distance - accuracy varies between&nbsp;<strong><span id="summary-accuracy-min"></span>&nbsp;m</strong>&nbsp;and&nbsp;<strong><span id="summary-accuracy-max"></span>&nbsp;m</strong>&nbsp;(average&nbsp;<strong><span id="summary-accuracy-average"></span>&nbsp;m</strong>)</span><span id="nodatainfo" class="text-muded small hide">No data found in the given date range</span></h1>

	<div id="daterange">
		<i class="fa fa-calendar"></i>&nbsp;
		<span></span>
		<b class="caret"></b>
	</div>
	<div id="daterange-navigation">
		<a id="daterange-backward" role="button" class="btn btn-default btn-xs"><i class="fa fa-arrow-left"></i></a>
		<a id="daterange-forward" role="button" class="btn btn-default btn-xs"><i class="fa fa-arrow-right"></i></a>
	</div>

	<div class="discrete-content-separator-2x"></div>

	<div id="map"></div>

</div>

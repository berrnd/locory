$(function()
{
	$(".logout-button").hide();

	$("#username").focus();

	if (LOCH.GetUriParam("invalid") === "true")
	{
		$("#login-error").text("Invalid credentials, please try again.");
		$("#login-error").show();
	}
});

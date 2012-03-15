<?
	$admin->requireLevel(1);
	
	$admin->deleteSetting("bigtree-internal-google-analytics-profile");
	$setting = array(
		"id" => "bigtree-internal-google-analytics-profile",
		"title" => "Google Analytics Profile ID",
		"type" => "text",
		"encrypted" => "on",
		"system" => "on"
	);
	$admin->createSetting($setting);
	$admin->updateSettingValue("bigtree-internal-google-analytics-profile",$_POST["profile"]);
	
	$admin->growl("Analytics","Profile Set");
	header("Location: ".$mroot);	
	die();
?>
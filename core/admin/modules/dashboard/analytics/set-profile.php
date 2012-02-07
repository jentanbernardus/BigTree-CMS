<?
	$admin->requireLevel(1);
	
	sqlquery("DELETE FROM bigtree_settings WHERE id = 'google-analytics-profile'");
	$setting = array(
		"id" => "google-analytics-profile",
		"title" => "Google Analytics Profile ID",
		"type" => "text",
		"encrypted" => "on",
		"system" => "on"
	);
	$admin->createSetting($setting);
	$admin->updateSettingValue("google-analytics-profile",$_POST["profile"]);
	
	$admin->growl("Analytics","Profile Set");
	header("Location: ".$mroot);	
	die();
?>
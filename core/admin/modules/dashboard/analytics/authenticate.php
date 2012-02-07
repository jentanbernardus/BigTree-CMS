<?
	try {
		$ga = new gapi($_POST["email"],$_POST["password"]);
	} catch (Exception $e) {
		header("Location: ".$mroot."setup/error/");
		die();
	}
	
	$admin->requireLevel(1);
	
	sqlquery("DELETE FROM bigtree_settings WHERE id = 'google-analytics-email'");
	$setting = array(
		"id" => "google-analytics-email",
		"title" => "Google Analytics Email Address",
		"type" => "text",
		"encrypted" => "on",
		"system" => "on"
	);
	$admin->createSetting($setting);
	$admin->updateSettingValue("google-analytics-email",$_POST["email"]);
	
	sqlquery("DELETE FROM bigtree_settings WHERE id = 'google-analytics-password'");
	$setting = array(
		"id" => "google-analytics-password",
		"title" => "Google Analytics Password",
		"type" => "text",
		"encrypted" => "on",
		"system" => "on"
	);
	$admin->createSetting($setting);
	$admin->updateSettingValue("google-analytics-password",$_POST["password"]);
	
	$admin->growl("Analytics","Account Authenticated");
	header("Location: ".$mroot."choose-profile/");	
	die();
?>

<?
	$keys = json_encode(array("access_key_id" => $_POST["access_key_id"], "secret_access_key" => $_POST["secret_access_key"]));
	
	// If we've never used S3 before, setup our settings for it.
	if (!$admin->settingExists("s3-keys")) {
		$admin->createSetting(array(
			"id" => "s3-keys",
			"system" => "on",
			"encrypted" => "on"
		));
	}
	if (!$admin->settingExists("s3-buckets")) {
		$admin->createSetting(array(
			"id" => "s3-buckets",
			"system" => "on"
		));
	}
	
	$admin->updateSettingValue("s3-keys",$keys);
	
	$ups = $cms->getSetting("upload-service");
	
	// Check if we have optipng installed.
	if (file_exists("/usr/bin/optipng")) {
		$ups["optipng"] = "/usr/bin/optipng";
	} elseif (file_exists("/usr/local/bin/optipng")) {
		$ups["optipng"] = "/usr/local/bin/optipng";
	}

	// Check if we have jpegtran installed.
	if (file_exists("/usr/bin/jpegtran")) {
		$ups["jpegtran"] = "/usr/bin/jpegtran";
	} elseif (file_exists("/usr/local/bin/jpegtran")) {
		$ups["jpegtran"] = "/usr/local/bin/jpegtran";
	}
	
	if ($_POST["access_key_id"] && $_POST["secret_access_key"]) {
		$ups["service"] = "s3";
	} else {
		$ups["service"] = "";
	}

	$admin->updateSettingValue("upload-service",json_encode($ups));	
	
	$admin->growl("Developer","Updated Amazon S3 Keys");
	header("Location: $saroot");
	die();
?>
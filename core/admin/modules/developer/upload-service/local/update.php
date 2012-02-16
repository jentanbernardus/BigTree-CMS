<?
	$ups = $cms->getSetting("bigtree-internal-upload-service");
	
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
	
	$ups["service"] = "";
	
	$admin->updateSettingValue("bigtree-internal-upload-service",json_encode($ups));	
	
	$admin->growl("Developer","Updated Upload Service");
	header("Location: $saroot");
	die();
?>
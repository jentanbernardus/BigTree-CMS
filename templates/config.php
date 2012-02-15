<?
	// Time Zone
	date_default_timezone_set("America/New_York");
	
	// Set to false to stop all PHP errors/warnings from showing.
	$debug = true;
	
	// Database info.
	$config["db"]["host"] = "localhost";
	$config["db"]["name"] = "bigtree4";
	$config["db"]["user"] = "root";
	$config["db"]["password"] = "root";
	
	// Separate write database info (for load balanced setups)
	$config["db_write"]["host"] = "";
	$config["db_write"]["name"] = "";
	$config["db_write"]["user"] = "";
	$config["db_write"]["password"] = "";
	
	// Setup the www_root and resource_root
	// Resource root must be on a different domain than www_root.  Usually we just remove the www. from the domain.
	$config["domain"] = "http://localhost:8888/BigTree-CMS";
	$config["www_root"] = "http://localhost:8888/BigTree-CMS/";
	$config["resource_root"] = "http://localhost:8888/BigTree-CMS/";
	//$GLOBALS["secure_root"] = str_replace("http://","https://",$config["www_root"]);
	$GLOBALS["secure_root"] = $config["www_root"];
	
	
	// Email used for default form mailers	
	$config["contact_email"] = "fastspot";
	
	// The amount of work for the password hashing.  Higher is more secure but more costly on your CPU.
	$config["password_depth"] = 8;
	// If you have HTTPS enabled, set to true to force admin logins through HTTPS
	$config["force_secure_login"] = false;
	// Encryption key for encrypted settings
	$config["settings_key"] = "goosepantsforsure";
	
	// Custom Output Filter Function
	$config["output_filter"] = false;
	
	// Enable Simple Caching (incomplete)
	$config["cache"] = false;
	$config["xsendfile"] = false;
	
	// ReCAPTCHA Keys
	$config["recaptcha"]["private"] = "6LcjTrwSAAAAADnHAf1dApaNCX1ODNuEBP1YdMdJ";
	$config["recaptcha"]["public"] = "6LcjTrwSAAAAAKvNG6n0YtCROEWGllOu-dS5M5oj";
	
	define("BIGTREE_CUSTOM_BASE_CLASS",false);
	define("BIGTREE_CUSTOM_ADMIN_CLASS",false);
	define("BIGTREE_CUSTOM_BASE_CLASS_PATH",false);
	define("BIGTREE_CUSTOM_ADMIN_CLASS_PATH",false);
?>
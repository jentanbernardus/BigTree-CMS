<?
	// Time Zone
	date_default_timezone_set("America/New_York");
	
	// Set to false to stop all PHP errors/warnings from showing.
	$debug = true;
	
	// Database info.
	$config["db"]["host"] = "localhost";
	$config["db"]["name"] = "bigtree4_example";
	$config["db"]["user"] = "root";
	$config["db"]["password"] = "fastspot";
	
	// Separate write database info (for load balanced setups)
	$config["db_write"]["host"] = "";
	$config["db_write"]["name"] = "";
	$config["db_write"]["user"] = "";
	$config["db_write"]["password"] = "";
	
	// Setup the www_root and resource_root
	// Resource root must be on a different domain than www_root.  Usually we just remove the www. from the domain.
	$config["domain"] = "http://dev.fastspot.com";
	$config["www_root"] = "http://dev.fastspot.com/labs/bigtree/sites/example/";
	$config["admin_root"] = "http://dev.fastspot.com/labs/bigtree/sites/example/admin/";
	//$GLOBALS["secure_root"] = str_replace("http://","https://",$config["www_root"]);
	$GLOBALS["secure_root"] = $config["www_root"];	
	
	// Email used for default form mailers	
	$config["contact_email"] = "info@fastspot.com";
	
	// The amount of work for the password hashing.  Higher is more secure but more costly on your CPU.
	$config["password_depth"] = 8;
	// If you have HTTPS enabled, set to true to force admin logins through HTTPS
	$config["force_secure_login"] = false;
	// Encryption key for encrypted settings
	$config["settings_key"] = "yellowgoosepants";
	
	// Custom Output Filter Function
	$config["output_filter"] = false;
	
	// Enable Simple Caching (incomplete)
	$config["cache"] = false;
	$config["xsendfile"] = false;
	
	// ReCAPTCHA Keys
	$config["recaptcha"]["private"] = "6LcjTrwSAAAAADnHAf1dApaNCX1ODNuEBP1YdMdJ";
	$config["recaptcha"]["public"] = "6LcjTrwSAAAAAKvNG6n0YtCROEWGllOu-dS5M5oj";
	
	// Base classes for BigTree.  If you want to extend / overwrite core features of the CMS, change these to your new class names
	// Set BIGTREE_CUSTOM_BASE_CLASS_PATH to the directory path (relative to /core/) of the file that will extend BigTreeCMS
	// Set BIGTREE_CUSTOM_ADMIN_CLASS_PATH to the directory path (relative to /core/) of the file that will extend BigTreeAdmin
	define("BIGTREE_CUSTOM_BASE_CLASS",false);
	define("BIGTREE_CUSTOM_ADMIN_CLASS",false);
	define("BIGTREE_CUSTOM_BASE_CLASS_PATH",false);
	define("BIGTREE_CUSTOM_ADMIN_CLASS_PATH",false);

	
// CAHCING -------------------- 
	
	// Javascript caching -- an array of all the files to combine.
	$config["js"]["files"]["site"] = array(
		"jquery-1.7.1.min.js",
		"main.js"
	);
	$config["js"]["vars"] = array();
	$config["js"]["minify"] = false;
	
	
	// CSS caching -- an array of all the files to combine.
	$config["css"]["files"]["site"] = array(
		"grid.css",
		"master.css"
	);
	$config["css"]["vars"] = array();
	$config["css"]["minify"] = false;
	
?>
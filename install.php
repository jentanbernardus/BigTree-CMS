<?
	//!Server Parameters
	$warnings = array();
	if (!extension_loaded('json'))
		$warnings[] = "JSON Extension is missing (this could affect API and Foundry usage).";
	if (!extension_loaded("mysql"))
		$warnings[] = "MySQL Extension is missing (this is a FATAL ERROR).";
	if (get_magic_quotes_gpc())
		$warnings[] = "magic_quotes_gpc is on. BigTree will attempt to override this at runtime but it is advised that you turn it off in php.ini.";
	if (!ini_get('file_uploads'))
		$warnings[] = "PHP does not have file uploads enabled. This will severely limit BigTree's functionality.";
	if (!ini_get('short_open_tag'))
		$warnings[] = "PHP does not currently allow short_open_tags. BigTree will attempt to override this at runtime but you may need to enable it in php.ini manually.";
	if (!extension_loaded('gd') && !extension_loaded('imagick'))
		$warnings[] = "PHP does not have GD or ImageMagick enabled. This will severely limit your ability to do anything with images in BigTree.";
	if (!function_exists("ftp_connect"))
		$warnings[] = "PHP does not have FTP support installed. Dev->Live sync will not work without FTP support.";
	if (intval(ini_get('upload_max_filesize')) < 4)
		$warnings[] = "Max upload filesize is currently less than 4MB. 8MB or higher is recommended.";
	if (intval(ini_get('upload_max_filesize')) < 4)
		$warnings[] = "Max upload filesize (upload_max_filesize in php.ini) is currently less than 4MB. 8MB or higher is recommended.";
	if (intval(ini_get('post_max_size')) < 4)
		$warnings[] = "Max POST size (post_max_size in php.ini) is currently less than 4MB. 8MB or higher is recommended.";
	if (intval(ini_get("memory_limit")) < 32)
		$warnings[] = "PHP's memory limit is currently under 32MB. BigTree recommends at least 32MB of memory be available to PHP.";
	$apache_modules = apache_get_modules();
	if (in_array('mod_rewrite', $apache_modules) === false)
		$warnings[] = "BigTree requires Apache to have mod_rewrite installed (this is a FATAL ERROR).";

?><!doctype html> 
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>	<html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>	<html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>	<html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title>Install BigTree</title>
		<link rel="stylesheet" href="core/admin/css/install.css" type="text/css" media="all" />
		<script type="text/javascript" src="core/admin/js/lib.js"></script>
		<script type="text/javascript" src="core/admin/js/install.js"></script>
	</head>
	<body class="install">
		<div class="install_fade">
			<div class="install_wrapper">
				<h1>Install BigTree</h1>
<?php

	foreach ($_POST as $key => $val) {
		$$key = $val;
	}
	
	$success = false;

	if (count($_POST) && !($db && $host && $user && $password && $cms_user && $cms_pass)) {
		$error = "Errors found! Please fix the highlighted fields before submitting.";
	} elseif (!is_writable(".")) {
		$error = "Please make the current working directory writable.";
	} elseif (count($_POST)) {
		if ($write_host) {
			$con = mysql_connect($write_host,$write_user,$write_password);
		} else {
			$con = mysql_connect($host,$user,$password);
		}
		
		if (!$con) {
			$error = "Could not connect to database.";
		} else {
			$select = mysql_select_db($db);
			if (!$select)
				$error = "Could not select database &ldquo;$db&rdquo;.";
		}
	}
	
	if (!$error && count($_POST)) {
		
		$find = array(
			"[host]",
			"[db]",
			"[user]",
			"[password]",
			"[write_host]",
			"[write_db]",
			"[write_user]",
			"[write_password]",
			"[domain]",
			"[wwwroot]",
			"[resourceroot]",
			"[email]",
			"[settings_key]",
			"[force_secure_login]"
		);
		
		$domain = "http://".$_SERVER["HTTP_HOST"];
		$www_root = $domain.str_replace("install.php","",$_SERVER["REQUEST_URI"]);
		$resource_root = str_replace("http://www.","http://",$www_root);
		
		$replace = array(
			$host,
			$db,
			$user,
			$password,
			$write_host,
			$write_db,
			$write_user,
			$write_password,
			$domain,
			$www_root,
			$resource_root,
			$cms_user,
			$settings_key,
			$force_secure_login
		);
		
		$sql_queries = explode("\n",file_get_contents("bigtree.sql"));
		foreach ($sql_queries as $query) {
			mysql_query($query);
		}
		mysql_query("UPDATE bigtree_pages SET id = '0' WHERE id = '1'");
		include "core/inc/utils/PasswordHash.php";
		
		$phpass = new PasswordHash(8, TRUE);
		$enc_pass = mysql_real_escape_string($phpass->HashPassword($cms_pass));
		mysql_query("INSERT INTO bigtree_users (`email`,`password`,`name`,`level`) VALUES ('$cms_user','$enc_pass','Developer','2')");
		
		function dwrite($dir) {
			global $root;
			mkdir($root.$dir);
			chmod($root.$dir,0777);
		}
		
		function d($dir) {
			global $root;
			mkdir($root.$dir);
		}
		
		function dtouch($file,$contents = "") {
			file_put_contents($file,$contents);
			chmod($file,0777);
		}
		
		$root = "";
		
		dwrite("cache/");
		dwrite("custom/");
		dwrite("custom/admin/");
		dwrite("custom/admin/ajax/");
		dwrite("custom/admin/css/");
		dwrite("custom/admin/images/");
		dwrite("custom/admin/images/modules/");
		dwrite("custom/admin/images/templates/");
		dwrite("custom/admin/modules/");
		dwrite("custom/admin/pages/");
		dwrite("custom/admin/form-field-types/");
		dwrite("custom/admin/form-field-types/draw/");
		dwrite("custom/admin/form-field-types/process/");
		dwrite("custom/inc/");
		dwrite("custom/inc/modules/");
		dwrite("custom/inc/required/");
		dwrite("site");
		dwrite("site/css/");
		dwrite("site/files/");
		dwrite("site/files/pages/");
		dwrite("site/files/resources/");
		dwrite("site/images/");
		dwrite("site/swf/");
		dwrite("site/js/");
		dwrite("templates");
		dwrite("templates/ajax/");
		dwrite("templates/droplets/");
		dwrite("templates/layouts/");
		dtouch("templates/layouts/_header.php");
		dtouch("templates/layouts/default.php",'<? include "_header.php" ?>
<?=$content?>
<? include "_footer.php" ?>');
		dtouch("templates/layouts/_footer.php");
		dwrite("templates/modules/");
		dwrite("templates/pages/");
		dtouch("templates/pages/_404.php");
		dtouch("templates/pages/_home.php");
		dtouch("templates/pages/_sitemap.php");
		dtouch("templates/pages/content.php",'<h1><?=$page_header?></h1>
<?=$page_content?>');
		dwrite("templates/sidelets/");
		dwrite("templates/objects/");
		dwrite("templates/objects/containers/");
		
		dtouch("templates/config.php",str_replace($find,$replace,file_get_contents("core/config.example.php")));
		
		// Create the main site .htaccess and index.php and the .htaccess that lets you do things without moving the root.
		file_put_contents("site/.htaccess",'<IfModule mod_deflate.c>


# force deflate for mangled headers developer.yahoo.com/blogs/ydn/posts/2010/12/pushing-beyond-gzipping/
<IfModule mod_setenvif.c>
  <IfModule mod_headers.c>
    SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$ ^((gzip|deflate)\s,?\s(gzip|deflate)?|X{4,13}|~{4,13}|-{4,13})$ HAVE_Accept-Encoding
    RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding
  </IfModule>
</IfModule>
# html, txt, css, js, json, xml, htc:
<IfModule filter_module>
  FilterDeclare   COMPRESS
  FilterProvider  COMPRESS  DEFLATE resp=Content-Type /text/(html|css|javascript|plain|x(ml|-component))/
  FilterProvider  COMPRESS  DEFLATE resp=Content-Type /application/(javascript|json|xml|x-javascript)/
  FilterChain     COMPRESS
  FilterProtocol  COMPRESS  change=yes;byteranges=no
</IfModule>

<IfModule !mod_filter.c>
  # Legacy versions of Apache
  AddOutputFilterByType DEFLATE text/html text/plain text/css application/json
  AddOutputFilterByType DEFLATE text/javascript application/javascript application/x-javascript 
  AddOutputFilterByType DEFLATE text/xml application/xml text/x-component
</IfModule>

# webfonts and svg:
  <FilesMatch "\.(ttf|otf|eot|svg)$" >
    SetOutputFilter DEFLATE
  </FilesMatch>
</IfModule>

IndexIgnore */*
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php?bigtree_htaccess_url=$1 [QSA,L]

php_flag short_open_tag On
php_flag magic_quotes_gpc Off');

		
		rename("core/index.php","site/index.php");

		file_put_contents(".htaccess",'<IfModule mod_deflate.c>


# force deflate for mangled headers developer.yahoo.com/blogs/ydn/posts/2010/12/pushing-beyond-gzipping/
<IfModule mod_setenvif.c>
  <IfModule mod_headers.c>
    SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$ ^((gzip|deflate)\s,?\s(gzip|deflate)?|X{4,13}|~{4,13}|-{4,13})$ HAVE_Accept-Encoding
    RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding
  </IfModule>
</IfModule>
# html, txt, css, js, json, xml, htc:
<IfModule filter_module>
  FilterDeclare   COMPRESS
  FilterProvider  COMPRESS  DEFLATE resp=Content-Type /text/(html|css|javascript|plain|x(ml|-component))/
  FilterProvider  COMPRESS  DEFLATE resp=Content-Type /application/(javascript|json|xml|x-javascript)/
  FilterChain     COMPRESS
  FilterProtocol  COMPRESS  change=yes;byteranges=no
</IfModule>

<IfModule !mod_filter.c>
  # Legacy versions of Apache
  AddOutputFilterByType DEFLATE text/html text/plain text/css application/json
  AddOutputFilterByType DEFLATE text/javascript application/javascript application/x-javascript 
  AddOutputFilterByType DEFLATE text/xml application/xml text/x-component
</IfModule>

# webfonts and svg:
  <FilesMatch "\.(ttf|otf|eot|svg)$" >
    SetOutputFilter DEFLATE
  </FilesMatch>
</IfModule>

<IfModule mod_rewrite.c>
	RewriteEngine on
	RewriteRule    ^$    site/    [L]
	RewriteRule    (.*) site/$1    [L]
</IfModule>');
?>
					<h1>BigTree CMS Installed</h1>
					<p>Thanks for using BigTree CMS.  Your install was successful.  Ready to hit the admin? <a href="admin/">CLICK HERE</a>.</p>
				</section>
<?
	} else {
		if (!$host)
			$host = "localhost";
?>
				<form method="post" action="" class="module">
					<h2 class="getting_started"><span></span>Getting Started</h2>
					<fieldset class="clear">
						<p>Welcome to the BigTree installer. If you have not done so already, please make the current working directory writable and create a MySQL database for your new BigTree powered site.</p>
					</fieldset>
					<? if (count($warnings)) { ?>
					<br />
					<? foreach ($warnings as $warning) { ?>
					<p class="warning_message clear"><?=$warning?></p>
					<? } ?>
					<? } ?>
					<? if ($error) { ?>
					<p class="error_message clear"><?=$error?></p>
					<? } ?>
					<hr />
					
					<h2 class="database"><span></span>Database Properties</h2>
					<fieldset class="clear">
						<p>Enter your MySQL database information below.</p>
						<br />
					</fieldset>
					<fieldset class="left<? if (count($_POST) && !$host) { ?> form_error<? } ?>">
						<label>Hostname</label>
						<input class="text" type="text" id="db_host" name="host" value="<?=htmlspecialchars($host)?>" tabindex="0" />
					</fieldset>
					<fieldset class="right<? if (count($_POST) && !$db) { ?> form_error<? } ?>">
						<label>Database</label>
						<input class="text" type="text" id="db_name" name="db" value="<?=htmlspecialchars($db)?>" tabindex="1" />
					</fieldset>
					<br class="clear" /><br />
					<fieldset class="left<? if (count($_POST) && !$user) { ?> form_error<? } ?>">
						<label>Username</label>
						<input class="text" type="text" id="db_user" name="user" value="<?=htmlspecialchars($user)?>" tabindex="2" />
					</fieldset>
					<fieldset class="right<? if (count($_POST) && !$password) { ?> form_error<? } ?>">
						<label>Password</label>
						<input class="text" type="text" id="db_pass" name="password" value="<?=htmlspecialchars($password)?>" tabindex="3" />
					</fieldset>
					<fieldset>
						<br />
						<input type="checkbox" class="checkbox" name="loadbalanced" id="loadbalanced"<? if ($loadbalanced) { ?> checked="checked"<? } ?> />
						<label class="for_checkbox">Load Balanced MySQL</label>
					</fieldset>
					
					<div id="loadbalanced_settings"<? if (!$loadbalanced) { ?> style="display: none;"<? } ?>>
						<br class="clear" />
						<hr />
						
						<h2 class="database"><span></span>Write Database Properties</h2>
						<fieldset class="clear">
							<p>If you are hosting a load balanced setup with multiple MySQL servers, enter the master write server information below.</p>
							<br />
						</fieldset>
						<fieldset class="left<? if (count($_POST) && !$write_host) { ?> form_error<? } ?>">
							<label>Hostname</label>
							<input class="text" type="text" id="db_write_host" name="write_host" value="<?=htmlspecialchars($host)?>" tabindex="0" />
						</fieldset>
						<fieldset class="right<? if (count($_POST) && !$write_db) { ?> form_error<? } ?>">
							<label>Database</label>
							<input class="text" type="text" id="db_write_name" name="write_db" value="<?=htmlspecialchars($db)?>" tabindex="1" />
						</fieldset>
						<br class="clear" /><br />
						<fieldset class="left<? if (count($_POST) && !$write_user) { ?> form_error<? } ?>">
							<label>Username</label>
							<input class="text" type="text" id="db_write_user" name="write_user" value="<?=htmlspecialchars($user)?>" tabindex="2" />
						</fieldset>
						<fieldset class="right<? if (count($_POST) && !$write_password) { ?> form_error<? } ?>">
							<label>Password</label>
							<input class="text" type="text" id="db_write_pass" name="write_password" value="<?=htmlspecialchars($password)?>" tabindex="3" />
						</fieldset>
						<br class="clear" />
					</div>
					
					<br class="clear" />
					<hr />
					
					<h2 class="security"><span></span>Site Security</h2>
					<fieldset class="clear">
						<p>Customize your site's security settings below.</p>
						<br />
					</fieldset>
					<fieldset class="left<? if (count($_POST) && !$settings_key) { ?> form_error<? } ?>">
						<label>Settings Encryption Key</label>
						<input class="text" type="text" name="settings_key" id="settings_key" value="<?=htmlspecialchars($settings_key)?>" tabindex="11" />
					</fieldset>
					<fieldset class="clear">
						<br />
						<input type="checkbox" class="checkbox" name="force_secure_login" id="force_secure_login"<? if ($force_secure_login) { ?> checked="checked"<? } ?> />
						<label class="for_checkbox">Force HTTPS Logins</label>
					</fieldset>
					
					<br class="clear" />
					<hr />
					
					<h2 class="account"><span></span>Administrator Account</h2>
					<fieldset class="clear">
						<p>Create the default account your administration area.</p>
						<br />
					</fieldset>
					<fieldset class="left<? if (count($_POST) && !$cms_user) { ?> form_error<? } ?>">
						<label>Email Address</label>
						<input class="text" type="text" id="cms_user" name="cms_user" value="<?=htmlspecialchars($cms_user)?>" tabindex="9" />
					</fieldset>
					<fieldset class="right<? if (count($_POST) && !$cms_pass) { ?> form_error<? } ?>">
						<label>Password</label>
						<input class="text" type="text" id="cms_pass" name="cms_pass" value="<?=htmlspecialchars($cms_pass)?>" tabindex="10" />
					</fieldset>
					
					<br class="clear" />
					<br />
					<hr />
					
					<h2 class="example"><span></span>Example Site</h2>
					<fieldset class="clear">
						<p>If you would also like to install the BigTree example site, check the box below. These optional demo files include example templates and modules to help learn how BigTree works, behind the scenes.</p>
					</fieldset>
					<fieldset class="clear">
						<br />
						<input type="checkbox" class="checkbox" name="install_example_site" id="install_example_site"<? if ($install_example_site) { ?> checked="checked"<? } ?> />
						<label class="for_checkbox">Install Example Site</label>
					</fieldset>
					
					<br class="clear" />
					
					<fieldset class="lower">
						<input type="submit" class="button blue" value="Install Now" />
					</fieldset>
				</form>
<?php
	}
?>
				<a href="http://www.bigtreecms.com" class="install_logo" target="_blank"></a>
				<a href="http://www.fastspot.com" class="install_copyright" target="_blank"></a>
			</div>
		</div>
	</body>
</html>
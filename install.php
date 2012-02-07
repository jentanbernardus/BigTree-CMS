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
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<title>BigTree Install</title>
		<link rel="stylesheet" href="core/admin/css/install.css" type="text/css" media="all" />
		<link rel="stylesheet" href="admin/css/install.css" type="text/css" media="all" />	
		<script type="text/javascript" src="admin/js/lib.js"></script>
		<script type="text/javascript" src="core/admin/js/lib.js"></script>
	</head>
	<body>
		<header>
			<div class="container">
				<span id="logo"></span>
			</div>
		</header>
		<div class="container">
			<article id="page" class="sub form">
				<section class="intro">
					<h4 class="left">Installer</h4>
					<hr class="clear" />
<?php

	foreach ($_POST as $key => $val) {
		$$key = $val;
	}
	
	$success = false;

	if (count($_POST) && !($db && $host && $user && $password && $cms_user && $cms_pass)) {
		$error = "You must complete all required information. Missed fields are highlighted above.";
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

					<h1>Install BigTree CMS 4.0</h1>
					<p>Welcome to the BigTree CMS installer.  If you have not done so already, please make the current working directory writable and create a MySQL database for BigTree to use.</p>
					<p class="required">
						<img src="core/admin/images/install/icon_required.png" alt="Star" /> = Required
					</p>
				</section>
				<section class="form">
					<hr />
					<form method="post" action="" class="formbuilder">
						<? if (count($warnings)) { ?>
						<div class="section_break warnings">
							<h3>WARNINGS</h3>
							<p>You may have issues with your BigTree install.</p>
							<br />
						</div>
						<div class="section_fields">
							<ul class="warnings">
								<? foreach ($warnings as $warning) { ?>
								<li><?=$warning?></li>
								<? } ?>
							</ul>
						</div>
						<hr class="clear" />
						<? } ?>
						<div class="section_break">
							<h3>Database Parameters</h3>
							<p>Enter your MySQL database information here.</p>
						</div>
						<div class="section_fields">
							<div class="fb_column">
								<fieldset>
									<label class="required" for="db_host">Host</label>
									<input class="required<? if (count($_POST) && !$host) { ?> error<? } ?>" type="text" name="host" id="db_host" value="<?=htmlspecialchars($host)?>" tabindex="0" />
								</fieldset>
								<fieldset>
									<label class="required" for="db_user">Username</label>
									<input class="required<? if (count($_POST) && !$user) { ?> error<? } ?>" type="text" name="user" id="db_user" value="<?=htmlspecialchars($user)?>" tabindex="2" />
								</fieldset>
								<fieldset>
									<label>Loadbalanced MySQL</label>
									<input class="checkbox" type="checkbox" name="loadbalanced"<? if ($loadbalanced) { ?> checked="checked"<? } ?> />
								</fieldset>
							</div>
							<div class="fb_column">
								<fieldset>
									<label class="required" for="db_name">Database</label>
									<input class="required<? if (count($_POST) && !$db) { ?> error<? } ?>" type="text" name="db" id="db_name" value="<?=htmlspecialchars($db)?>" tabindex="1" />
								</fieldset>
								<fieldset>
									<label class="required" for="db_pass">Password</label>
									<input class="required<? if (count($_POST) && !$password) { ?> error<? } ?>" type="text" name="password" id="db_pass" value="<?=htmlspecialchars($password)?>" tabindex="3" />
								</fieldset>
							</div>
						</div>
						<hr class="clear" />
						<div id="loadbalanced_settings"<? if (!$loadbalanced) { ?> style="display: none;"<? } ?>>
							<div class="section_break">
								<h3>Write Database Parameters</h3>
								<p>If you are hosting a load balanced setup with multiple MySQL servers, enter the master write server information.</p>
							</div>
							<div class="section_fields">
								<div class="fb_column">
									<fieldset>
										<label>Host</label>
										<input type="text" name="write_host" value="<?=htmlspecialchars($write_host)?>" tabindex="5" />
									</fieldset>
									<fieldset>
										<label>Username</label>
										<input type="text" name="write_user" value="<?=htmlspecialchars($write_user)?>" tabindex="7" />
									</fieldset>
								</div>
								<div class="fb_column">
									<fieldset>
										<label>Database</label>
										<input type="text" name="write_db" value="<?=htmlspecialchars($write_db)?>" tabindex="6" />
									</fieldset>
									<fieldset>
										<label>Password</label>
										<input type="text" name="write_password" value="<?=htmlspecialchars($write_password)?>" tabindex="8" />
									</fieldset>
								</div>
							</div>
							<hr class="clear" />
						</div>
						<div class="section_break">
							<h3>Login Parameters</h3>
							<p>Create a default login here for your CMS admin section.</p>
						</div>
						<div class="section_fields">
							<div class="fb_column">
								<fieldset>
									<label class="required" for="cms_user">Email Address</label>
									<input class="required<? if (count($_POST) && !$cms_user) { ?> error<? } ?>" type="text" name="cms_user" id="cms_user" value="<?=htmlspecialchars($cms_user)?>" tabindex="9" />
								</fieldset>
							</div>
							<div class="fb_column">
								<fieldset>
									<label class="required" for="cms_pass">Password</label>
									<input class="required<? if (count($_POST) && !$cms_pass) { ?> error<? } ?>" type="text" name="cms_pass" id="cms_pass" value="<?=htmlspecialchars($cms_pass)?>" tabindex="10" />
								</fieldset>
							</div>
						</div>
						<hr class="clear" />
						<div class="section_break">
							<h3>Security Parameters</h3>
						</div>
						<div class="section_fields">
							<div class="fb_column">
								<fieldset>
									<label class="required" for="settings_key">Encryption Key for Secure Settings</label>
									<input class="required<? if (count($_POST) && !$settings_key) { ?> error<? } ?>" type="text" name="settings_key" id="settings_key" value="<?=htmlspecialchars($settings_key)?>" tabindex="11" />
								</fieldset>
							</div>
							<div class="fb_column">
								<fieldset>
									<label for="force_secure_login">Force HTTPS Logins</label>
									<input class="checkbox" type="checkbox" name="force_secure_login" id="force_secure_login" <? if ($force_secure_login) { ?>checked="checked" <? } ?> tabindex="12" />
								</fieldset>
							</div>
						</div>
						<hr class="clear" />
						<div class="section_fields">
							<? if ($error) { ?>
							<p class="error"><strong>ERROR:</strong> <?=$error?></p>
							<? } ?>
							<input type="submit" class="submit button fb_submit" value="Submit" />
						</div>
						<br class="clear" />
					</form>
				</section>
<?php
	}
?>
			</article>
		</div>
		<footer>
			<div class="container">
				<div class="block first">
					<h6>Product Of</h6>
					<a href="http://www.fastspot.com" target="_blank" class="logo fastspot">Fastspot</a>
					<p class="address">
						Fastspot LLC <br />
						2026 East Lombard St. <br />
						Baltimore, MD 21231
					</p>
				</div>
				<div class="block">
					<h6>Contact Us</h6>
					<span class="icon phone">Call us at (410) 537 5007</span>
					<a href="mailto:info@fastspot.com" class="icon email">Email us at info@fastspot.com</a>
				</div>
				<div class="block last">
					<h6>Keep In Touch</h6>
					<a href="http://www.facebook.com/Fastspot" target="_blank" class="icon facebook">Like us on Facebook</a>
					<a href="http://feeds.feedburner.com/ThinkDesignInteract/" target="_blank" class="icon rss">Subscribe to our RSS</a>
					<a href="http://twitter.com/fastspot" target="_blank" class="icon twitter">Follow us on Twitter</a>
				</div>
			</div>
		</footer>
		<script type="text/javascript">
			$$("input[name=loadbalanced]").observe("click",function(ev) {
				$("loadbalanced_settings").toggle();
			});
		</script>
	</body>
</html>
<?
	$layout = "login";
	
	// Check if we're forcing HTTPS
	if ($config["force_secure_login"] && $_SERVER["SERVER_PORT"] == 80) {
		header("Location: ".str_replace("http://","https://",$aroot)."login/");
		die();
	}
	
	if (strpos($_SERVER["HTTP_USER_AGENT"],"MSIE") !== false && !$_SESSION["ignore_browser_warning"]) {
		include bigtree_path("admin/pages/browser-warning.php");
		$admin->stop();
	}

	$failure = false;
	if (isset($_POST["user"]) && isset($_POST["password"])) {
		if (!$admin->login($_POST["user"],$_POST["password"],$_POST["stay_logged_in"]))
			$failure = true;
	}
	
	
	if (isset($path[2]) && $path[2] == "logout")
		$admin->logout();
	
	if (isset($path[2]) && $path[2] == "forgot-password") {
?>
<div id="login">
	<form method="post" action="" class="module">
		<h2><span>Forgot Password</span></h2>
		<fieldset>
			<label>Email</label>
			<input class="text" type="text" id="user" name="user" />
		</fieldset>
		<fieldset class="lower">
			<a href="<?=$aroot?>login/" class="forgot_password">&laquo; Back to Login</a>
			<input type="submit" class="button retrieve_password white" value="Submit" />
		</fieldset>
	</form>
</div>
<?
	} else {
?>
<form method="post" action="" class="module">
	<h2><span>Login</span></h2>
	<fieldset>
		<label>Email</label>
		<input type="email" id="user" name="user" class="text" />

		<label>Password</label>
		<input type="password" id="password" name="password" class="text" />

		<p><input type="checkbox" name="stay_logged_in" checked="checked" /> Remember Me</p>
	</fieldset>
	<fieldset class="lower">
		<? if ($failure) { ?><p class="error">You've entered an invalid email address and/or password.</p><? } ?>
		<a href="<?=$aroot?>login/forgot-password/" class="forgot_password">Forgot Password?</a>
		<input type="submit" class="button white" value="Login" />
	</fieldset>
</form>
<?
	}
?>
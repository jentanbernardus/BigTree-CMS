<?
	$failure = false;
	if (isset($_POST["user"]) && isset($_POST["password"])) {
		if (!$admin->login($_POST["user"],$_POST["password"],$_POST["stay_logged_in"])) {
			$failure = true;
		}
	}
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
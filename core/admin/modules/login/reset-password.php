<?
	$hash = mysql_real_escape_string(end($path));
	$user = sqlfetch(sqlquery("SELECT * FROM bigtree_users WHERE change_password_hash = '$hash'"));
	$failure = false;
	
	if ($_POST["password"]) {
		if ($_POST["password"] != $_POST["confirm_password"]) {
			$failure = true;
		} else {
			$admin->changePassword(end($path),$_POST["password"]);
		}
	}
?>
<div id="login">
	<form method="post" action="" class="module">
		<h2><span>Password Reset</span></h2>
		<? if (!$user) { ?>
		<fieldset>
			<p>The password request link you followed has expired.<br /><a href="<?=$aroot?>login/forgot-password/">Click Here</a> to request a new password reset link.</p>
		</fieldset>
		<? } else { ?>
		<fieldset>
			<label>New Password</label>
			<input class="text" type="password" name="password" />
			<label>Confirm New Password</label>
			<input class="text" type="password" name="confirm_password" />
		</fieldset>
		<fieldset class="lower">
			<? if ($failure) { ?><p class="error">Passwords did not match. Please try again.</p><? } ?>
			<input type="submit" class="button white" value="Submit" />
		</fieldset>
		<? } ?>
	</form>
</div>
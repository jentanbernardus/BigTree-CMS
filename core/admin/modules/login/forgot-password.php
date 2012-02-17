<?
	$failure = false;

	if ($_POST["email"]) {
		if (!$admin->forgotPassword($_POST["email"])) {
			$failure = true;
		}
	}
?>
<div id="login">
	<form method="post" action="" class="module">
		<h2><span>Forgot Password</span></h2>
		<fieldset>
			<label>Email</label>
			<input class="text" type="email" name="email" />
		</fieldset>
		<fieldset class="lower">
			<? if ($failure) { ?><p class="error">You've entered an invalid email address.</p><? } ?>
			<a href="<?=$aroot?>login/" class="forgot_password">&laquo; Back to Login</a>
			<input type="submit" class="button retrieve_password white" value="Submit" />
		</fieldset>
	</form>
</div>
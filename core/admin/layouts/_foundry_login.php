<?
	$user = $admin->getUser($admin->ID);
	if ($user["foundry_author"]) {
		// Let's check to see if the credentials in their saved information are still valid.
		$verify = bigtree_curl("http://developer.bigtreecms.com/ajax/foundry/verify-author/",json_decode($user["foundry_author"],true));
		if ($verify == "1") {
			$logged_in = true;
		} else {
			$invalid_password = true;
		}
	}
	
	if ($invalid_password) {
?>
<form method="post" action="" id="foundry_login" class="module">
	
</form>
<?
	} elseif (!$logged_in) {
?>
<form method="post" action="" id="foundry_create" class="module">
	<h4>Create Foundry Account</h4>
	<p>This is your first time using Foundry on this BigTree installation.  If you already have an account, enter the same email and password below, otherwise, we will be creating a Foundry account to manage your submissions.</p>
	<fieldset>
		<label>Name</label>
		<input type="text" name="name" value="<?=htmlspecialchars($user["name"])?>" />
	</fieldset>
	<fieldset>
		<label>Company</label>
		<input type="text" name="company" value="<?=htmlspecialchars($user["company"])?>" />
	</fieldset>
	<fieldset>
		<label>Email</label>
		<input type="text" name="email" value="<?=htmlspecialchars($user["email"])?>" />
	</fieldset>
	<p class="error" id="foundry_create_error" style="display: none;">
		A Foundry account already exists with this email address but the password you entered did not match its account.  If you have forgotten your password, please visit the Foundry's <a href="http://developer.bigtreecms.com/foundry/forgot-password/">forgot password section</a>.
	</p>
	<fieldset>
		<label>Password</label>
		<input type="password" name="password" value="" />
	</fieldset>
	<fieldset>
		<input type="submit" class="button white" value="Create" />
	</fieldset>
</form>
<?
	}
?>
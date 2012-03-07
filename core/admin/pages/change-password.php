<?
	if ($_POST["password"]) {
		$f = sqlfetch(sqlquery("SELECT * FROM bigtree_users WHERE change_password_hash = '".end($path)."'"));
		
		$phpass = new PasswordHash($config["password_depth"], TRUE);
		$password = mysql_real_escape_string($phpass->HashPassword($_POST["password"]));
		sqlquery("UPDATE bigtree_users SET password = '$password' WHERE id = '".$f["id"]."'");
		
		$admin->growl("Change Password","Password changed, ready to login!");
		header("Location: $admin_root");
		die();
	}
	
	$f = sqlfetch(sqlquery("SELECT * FROM bigtree_users WHERE change_password_hash = '".end($path)."'"));
?>
<div id="login">
	<form method="post" action="" id="cpass" class="module">
		<fieldset>
			<h3>Change Password</h3>

			<label>Email</label>
			<input type="text" name="email" disabled="disabled" value="<?=htmlspecialchars($f["email"])?>" />

			<label>New Password</label>
			<input type="password" name="password" />

			<label>Confirm New Password</label>
			<input type="password" name="confirm_password" />

			<input type="submit" class="button white" value="Update" />
		</fieldset>
	</form>
</div>

<script type="text/javascript">
	$("#cpass").submit(function(ev) {
		inputs = $("#cpass input");
		errors = 0;
		if (!inputs.eq(1).val()) {
			errors++;
			BigTree.growl("Error","You must enter a password.");
		}
		if (inputs.eq(1).val() != inputs.eq(2).val()) {
			errors++;
			BigTree.growl("Error","Your passwords do not match.");
		}
		if (errors > 0) {
			return false;
		}
	});
</script>
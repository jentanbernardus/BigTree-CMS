<?
	if ($admin->Level < 1) {
?>
<h1>Access Denied</h1>
<p>Analytics is not presently setup.  Please contact an administrator to setup Analytics before proceeding.</p>
<?
	} else {
		$breadcrumb[] = array("link" => "dashboard/analytics/setup/", "title" => "Setup");
?>
<h1>Setup</h1>
<div class="form_container">
	<header>
		<p>Please enter your Google Analytics email address and password below.</p>
	</header>
	<form method="post" action="<?=$mroot?>authenticate/" class="module">
		<section>
			<fieldset>
				<label>Email Address</label>
				<input type="text" name="email" />
			</fieldset>
			<fieldset>
				<label>Password</label>
				<input type="password" name="password" />
			</fieldset>
			<? if (end($path) == "error") { ?>
			<p class="error">Google Login Failed.</p>
			<? } ?>
		</section>
		<footer>
			<input type="submit" value="Authenticate" class="blue" />
		</footer>
	</form>
</div>
<?
	}
?>
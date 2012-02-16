<?
	if ($admin->Level < 1) {
?>
<h1><span class="analytics"></span>Analytics: Access Denied</h1>
<p>Analytics is not presently setup.  Please contact an administrator to setup Analytics before proceeding.</p>
<?
	} else {
		$breadcrumb[] = array("link" => "dashboard/analytics/setup/", "title" => "Setup");
?>
<h1><span class="analytics"></span>Analytics Setup</h1>
<div class="form_container">
	<header>
		<p>Please enter your Google Analytics email address and password below.</p>
	</header>
	<form method="post" action="<?=$mroot?>authenticate/" class="module">
		<section>
			<? if (end($path) == "error") { ?>
			<p class="error_message">Google Login Failed.</p>
			<? } ?>
			<fieldset>
				<label>Email Address</label>
				<input type="text" name="email" />
			</fieldset>
			<fieldset>
				<label>Password</label>
				<input type="password" name="password" />
			</fieldset>
		</section>
		<footer>
			<input type="submit" value="Authenticate" class="blue" />
		</footer>
	</form>
</div>
<?
	}
?>
<?
	if ($admin->Level < 1) {
?>
<h1><span class="analytics"></span>Analytics: Access Denied</h1>
<p>Analytics is not presently setup.  Please contact an administrator to setup Analytics before proceeding.</p>
<?
	} else {
		$breadcrumb[] = array("link" => "dashboard/vitals-statistics/analytics/choose-profile/", "title" => "Choose Profile");
		$ga = new BigTreeGoogleAnalytics;
		$accounts = $ga->getAvailableProfiles();
?>
<h1><span class="analytics"></span>Analytics Setup</h1>
<div class="form_container">
	<header>
		<p>Please choose the correct site profile below.</p>
	</header>
	<form method="post" action="<?=$mroot?>set-profile/" class="module">
		<section>
			<fieldset>
				<label>Profile</label>
				<select name="profile">
					<?
						foreach ($accounts as $account => $profiles) {
							foreach ($profiles as $profile) {
					?>
					<option value="<?=$profile["id"]?>"><?=$account?> &mdash; <?=$profile["title"]?></option>
					<?
							}
						}
					?>
				</select>
			</fieldset>
		</section>
		<footer>
			<input type="submit" class="blue" value="Update" />
		</footer>
	</form>
</div>
<?
	}
?>
<?
	if ($admin->Level < 1) {
?>
<h1>Access Denied</h1>
<p>Analytics is not presently setup.  Please contact an administrator to setup Analytics before proceeding.</p>
<?
	} else {
		$breadcrumb[] = array("link" => "dashboard/analytics/choose-profile/", "title" => "Choose Profile");
?>
<h1>Choose Profile</h1>
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
						try {
							$ga = new gapi($user,$pass);
						} catch (Exception $e) {
							header("Location: ".$mroot."setup/");
							die();
						}
						$ga->requestAccountData(1,100);
						foreach ($ga->getResults() as $result) {
					?>
					<option value="<?=$result->getProfileId()?>"><?=$result?></option>
					<?
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
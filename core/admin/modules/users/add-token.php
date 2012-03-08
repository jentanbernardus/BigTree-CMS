<?
	$breadcrumb[] = array("link" => "users/view-tokens/", "title" => "API Tokens");
	$breadcrumb[] = array("link" => "users/add-token/", "title" => "Add Token");

	$admin->requireLevel(1);
?>
<h1><span class="users"></span>Add Token</h1>
<? include bigtree_path("admin/modules/users/_nav.php"); ?>
<div class="form_container">
	<form class="module" action="<?=$admin_root?>users/create-token/" method="post">
		<section>
			<div class="left">
				<fieldset>
					<label>Associated User</label>
					<select name="user" tabindex="1">
						<?
							$q = sqlquery("SELECT * FROM bigtree_users WHERE level <= '".$admin->Level."' ORDER BY email");
							while ($f = sqlfetch($q)) {
						?>
						<option value="<?=$f["id"]?>"><?=htmlspecialchars($f["email"])?></option>
						<?
							}
						?>
					</select>
				</fieldset>
			</div>
			<div class="right">
				<fieldset>
					<label>Access Level</label>
					<select name="readonly" tabindex="2">
						<option value="on">Read Only</option>
						<option value="">Full Access</option>
					</select>
				</fieldset>
			</div>
		</section>
		<footer>
			<input type="submit" class="button blue" value="Create" />
		</footer>
	</form>
</div>
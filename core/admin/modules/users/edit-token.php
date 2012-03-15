<?
	$breadcrumb[] = array("link" => "users/view-tokens/", "title" => "API Tokens");
	$breadcrumb[] = array("link" => "#", "title" => "Edit Token");
	
	$admin->requireLevel(1);
	$users = $admin->getTokenUsers();
	$token = $admin->getToken(end($path));
?>
<h1><span class="users"></span>Edit Token</h1>
<? include BigTree::path("admin/modules/users/_nav.php"); ?>
<div class="form_container">
	<form class="module" action="<?=$admin_root?>users/update-token/<?=end($path)?>/" method="post">
		<section>
			<fieldset>
				<label>Token</label>
				<input type="text" name="token" value="<?=htmlspecialchars($token["token"])?>" disabled="disabled" />
			</fieldset>
			<div class="left">
				<fieldset>
					<label>Associated User</label>
					<select name="user">
						<? foreach ($users as $u) { ?>
						<option value="<?=$u["id"]?>"<? if ($token["user"] == $u["id"]) { ?> selected="selected"<? } ?>><?=htmlspecialchars($u["email"])?></option>
						<? } ?>
					</select>
				</fieldset>
			</div>
			<div class="right">
				<fieldset>
					<label>Access Level</label>
					<select name="readonly">
						<option value="on">Read Only</option>
						<option value=""<? if (!$token["readonly"]) { ?> selected="selected"<? } ?>>Full Access</option>
					</select>
				</fieldset>
			</div>
		</section>
		<footer>
			<input type="submit" class="button blue" value="Update" />
		</footer>
	</form>
</div>
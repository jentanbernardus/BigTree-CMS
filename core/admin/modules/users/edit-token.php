<?
	$breadcrumb[] = array("link" => "users/view-tokens/", "title" => "API Tokens");
	$breadcrumb[] = array("link" => "#", "title" => "Edit Token");
	
	$admin->requireLevel(1);
	
	$token = sqlfetch(sqlquery("SELECT * FROM bigtree_api_tokens WHERE id = '".mysql_real_escape_string(end($path))."'"));
?>
<h1><span class="users"></span>Edit Token</h1>
<? include bigtree_path("admin/modules/users/_nav.php"); ?>
<div class="form_container">
	<form class="module" action="<?=$aroot?>users/update-token/<?=end($path)?>/" method="post">
		<section>
			<fieldset>
				<label>Token</label>
				<input type="text" name="token" value="<?=htmlspecialchars($token["token"])?>" disabled="disabled" />
			</fieldset>
			<div class="left">
				<fieldset>
					<label>Associated User</label>
					<select name="user">
						<?
							$q = sqlquery("SELECT * FROM bigtree_users WHERE level <= '".$admin->Level."' ORDER BY email");
							while ($f = sqlfetch($q)) {
						?>
						<option value="<?=$f["id"]?>"<? if ($token["user"] == $f["id"]) { ?> selected="selected"<? } ?>><?=htmlspecialchars($f["email"])?></option>
						<?
							}
						?>
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
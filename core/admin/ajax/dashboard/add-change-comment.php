<?
	$f = sqlfetch(sqlquery("SELECT * FROM bigtree_pending_changes WHERE id = '".$_GET["change"]."'"));
	
	$comments = json_decode($f["comments"],true);
	
	if ($_GET["comment"]) {
	
		$user = $admin->getUser($_SESSION["bigtree"]["id"]);
	
		$comments[] = array(
			"user" => $user["name"],
			"date" => date("F j, Y @ g:ia"),
			"comment" => $_GET["comment"]
		);
	
		sqlquery("UPDATE bigtree_pending_changes SET comments = '".mysql_real_escape_string(json_encode($comments))."' WHERE id = '".$_GET["change"]."'");
	
	}
?>

<? $x = 0; while ($x < count($comments)) { ?>
<li>
	<strong><?=$comments[$x]["user"]?></strong> ( <?=$comments[$x]["date"]?> )<br />
	<p><?=htmlspecialchars($comments[$x]["comment"])?></p>
</li>
<? $x++; } ?>
<li>
	<strong>Post New Comment</strong><br />
	<textarea name="comment"></textarea>
	<a class="button small white add_comment" href="#<?=$f["id"]?>">Submit Comment</a>
</li>
<?
	parse_str($_GET["sort"]);
	$max = count($row);
	
	foreach ($row as $pos => $id) {
		$id = $_POST["rel"][$id];
		sqlquery("UPDATE bigtree_templates SET position = '".($max-$pos)."' WHERE id = '$id'");
	}
?>
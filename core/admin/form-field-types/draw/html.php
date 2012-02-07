<?
	if ($options["simple"]) {
		$simplehtmls[] = "field_$key";
	} else {
		$htmls[] = "field_$key";
	}
	
	include bigtree_path("admin/form-field-types/draw/textarea.php");
?>
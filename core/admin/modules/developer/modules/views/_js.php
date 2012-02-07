<script type="text/javascript">
	$("#view_table").bind("select:changed",function() {
		$("#field_area").load("<?=$aroot?>ajax/developer/load-view-fields/?table=" + $(this).val());
	});
	
	$(".options").click(function() {
		$.ajax("<?=$aroot?>ajax/developer/load-view-options/", { type: "POST", data: { table: $("#view_table").val(), type: $("#view_type").val(), data: $("#view_options").val() }, complete: function(response) {
			new BigTreeDialog("View Options",response.responseText,function(data) {
				$.ajax("<?=$aroot?>ajax/developer/save-view-options/", { type: "POST", data: data });
			});
		}});
		
		return false;
	});
	
	$("#view_type").bind("select:changed",function() {
		if ($(this).val() == "images" || $(this).val() == "images-grouped") {
			$("#fields").hide();
		} else {
			$("#fields").show();
		}
	});
</script>
<script type="text/javascript">
	new BigTreeFormValidator("form.module");

	$("#feed_table").bind("select:changed",function(event,data) {
		$("#field_area").load("<?=$aroot?>ajax/developer/load-feed-fields/?table=" + data.value);
	});
	
	$(".options").click(function() {
		$.ajax("<?=$aroot?>ajax/developer/load-feed-options/", { type: "POST", data: { table: $("#feed_table").val(), type: $("#feed_type").val(), data: $("#feed_options").val() }, complete: function(response) {
			new BigTreeDialog("Feed Options",response.responseText,function(data) {
				$.ajax("<?=$aroot?>ajax/developer/save-feed-options/", { type: "POST", data: data });
			});
		}});
		return false;
	});
	
	$("#feed_type").bind("select:changed",function(event,data) {
		if (data.value == "rss" || data.value == "rss2") {
			$("#field_area").hide();
		} else {
			$("#field_area").show();
		}
	});
</script>
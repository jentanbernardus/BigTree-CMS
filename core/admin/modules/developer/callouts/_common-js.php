<script type="text/javascript">
	new BigTreeFormValidator("form.module");
	
	var current_editing_key;
	var resource_count = 0;
	
	$(".template_image_list a").click(function() {
		$(".template_image_list a.active").removeClass("active");
		$(this).addClass("active");
		$("#existing_image").val($(this).attr("href").substr(1));
		
		return false;
	});
	
	$(".icon_edit").live("click",function() {
		key = $(this).attr("name");
		current_editing_key = key;
		
		$.ajax("<?=$aroot?>ajax/developer/load-field-options/", { type: "POST", data: { template: "true", type: $("#type_" + key).val(), data: $("#options_" + key).val() }, onComplete: function(response) {
			new BigTreeDialog("Field Options",response.responseText,function(data) {
				$.ajax("<?=$aroot?>ajax/developer/save-field-options/?key=" + current_editing_key, { type: "POST", data: data });
			});
		}});
		
		return false;
	});
	
	$(".icon_delete").live("click",function() {
		new BigTreeDialog("Delete Resource",'<p class="confirm">Are you sure you want to delete this resource?',$.proxy(function() {
			$(this).parents("li").remove();
		},this),"delete",false,"OK");
		
		return false;
	});
		
	$(".add_resource").click(function() {
		resource_count++;
		
		li = $('<li id="row_' + resource_count + '">');
		li.html('<section class="developer_resource_id"><span class="icon_sort"></span><input type="text" name="resources[' + resource_count + '][id]" value="" /></section><section class="developer_resource_title"><input type="text" name="resources[' + resource_count + '][name]" value="" /></section><section class="developer_resource_subtitle"><input type="text" name="resources[' + resource_count + '][subtitle]" value="" /></section><section class="developer_resource_type"><select name="resources[' + resource_count + '][type]" id="type_' + resource_count + '"><? foreach ($admin->CalloutFieldTypes as $k => $v) { ?><option value="<?=$k?>"><?=htmlspecialchars($v)?></option><? } ?></select></section><section class="developer_resource_action"><a href="#" tabindex="-1" class="icon_edit" name="' + resource_count + '"></a><input type="hidden" name="resources[' + resource_count + '][options]" value="" id="options_' + resource_count + '" /></section><section class="developer_resource_action"><a href="#" tabindex="-1" class="icon_delete"></a></section>');

		$("#resource_table").append(li);
		li.find("select").get(0).customControl = new BigTreeSelect(li.find("select").get(0));

		$("#resource_table").sortable({ items: "li", handle: ".icon_sort" });

		return false;
	});
	
	$("#resource_table").sortable({ items: "li", handle: ".icon_sort" });
</script>
Event.observe(window,"load",function(ev) {

	$("#foundry_create").submit(function() {
		$.ajax("admin_root/ajax/developer/foundry/create-account/", { type: "POST", data: $(this).serializeJSON(true), complete: function(r) {
			j = r.responseJSON;
			if (j.success) {
				$("#foundry_create").slideUp(300);
				$("#foundry_continue").slideDown(300);
			} else {
				$("#foundry_create_error").show();
			}
		}});
		
		return false;
	});

});
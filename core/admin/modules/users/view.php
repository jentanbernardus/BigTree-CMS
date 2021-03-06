<h1><span class="users"></span>Users</h1>
<? include BigTree::path("admin/modules/users/_nav.php"); ?>

<div class="table">
	<summary>
		<input type="search" name="query" id="query" placeholder="Search" class="form_search" autocomplete="off" />
		<ul id="view_paging" class="view_paging"></ul>
	</summary>
	<header>
		<span class="users_name">Name</span>
		<span class="users_email">Email</span>
		<span class="users_company">Company</span>
		<span class="view_action">Edit</span>
		<span class="view_action">Delete</span>
	</header>
	<ul id="results">
		<? include BigTree::path("admin/ajax/users/get-page.php") ?>	
	</ul>
</div>

<script type="text/javascript">
	var deleteTimer,searchTimer;
	
	$("#query").keyup(function() {
		if (searchTimer) {
			clearTimeout(searchTimer);
		}
		searchTimer = setTimeout("_local_search()",400);
	});

	function _local_search() {
		$("#results").load("<?=ADMIN_ROOT?>ajax/users/get-page/?page=0&query=" + escape($("#query").val()));
	}
	
	$(".icon_delete").live("click",function() {
		new BigTreeDialog("Delete User",'<p class="confirm">Are you sure you want to delete this user?',$.proxy(function() {
			$.ajax("<?=ADMIN_ROOT?>ajax/users/delete/", { type: "POST", data: { id: $(this).attr("href").substr(1) } });
		},this),"delete",false,"OK");
		
		return false;
	});
</script>
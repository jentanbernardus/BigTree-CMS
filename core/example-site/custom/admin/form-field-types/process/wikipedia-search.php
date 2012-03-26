<? 
	
	$value = $data[$key]; 
	
	//HACKIN'!
	$item["wiki_title"] = htmlspecialchars($data["wiki_title"]);
	$item["wiki_url"] = htmlspecialchars($data["wiki_url"]);
	
?> 
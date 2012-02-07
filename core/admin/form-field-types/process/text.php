<?
	$st = $options["sub_type"];
	
	if ($st == "phone") {
		$value = $data[$key]["phone_1"]."-".$data[$key]["phone_2"]."-".$data[$key]["phone_3"];
	} elseif ($st == "address" || $st == "name") {
		$value = json_encode($data[$key]);
	} else {
		$value = $data[$key];
	}
?>
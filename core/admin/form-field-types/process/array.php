<?
	// Need this for existing callout stuff.
	if (is_array($data[$key])) {
		$entries = array();
		foreach ($data[$key] as $k => $v) {
			if (is_numeric($k)) {
				$entries[] = json_decode($v,true);
			}
		}
		$value = json_encode(bigtree_translate_array($entries));
	} else {
		$value = $data[$key];
	}
?>
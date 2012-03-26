<?
/*
	Custom BigTree integration:
		
		Copyright (c) 2012 Ben Plum, MIT license
		http://www.benjaminplum.com
		
		Requires: BTXCachableModule
*/
	
	class BTXWikipediaAPI extends BTXCachableModule {
		
		var $version = "0.1";
		
		/*
			Construct
		*/
		public function __construct($language = "en", $debug = false) {
			global $cms, $www_root;
			
			$this->max_cache_age = 60 * 5; // 5 mins
			$this->cache_prefix = "btx-wikipedia-api";
			
			parent::__construct($debug);
			
			$this->language = $language;
			$this->curl_options = array(CURLOPT_USERAGENT => "BigTree (" . $www_root . ")");
		}
		
		public function search($query = false) {
			if (!$query) {
	    		return false;
	    	}
	    	$curl_url = "http://" . $this->language . ".wikipedia.org/w/api.php?action=opensearch&suggest=false&format=json&search=" . urlencode($query);
			$cache_file = $this->cache_base . "-" . md5($query) . ".btc";
			$results = $this->cacheCurl($curl_url, $cache_file);
			return $results;
		}
		
		public function articleURL($route) {
			return "http://" . $this->language . ".wikipedia.org/wiki/" . $route;
		}
		
		// Handles up to five article redirects
		public function article($url = false, $count = 0) {
			if (!$url) {
	    		return false;
	    	}
	    	$parts = explode("wiki/", $url);
			$parts = explode("?", end($parts));
			$title = trim($parts[0]);
			if ($title == "") {
				return false;
			}
	    	$curl_url = "http://" . $this->language . ".wikipedia.org/w/api.php?action=parse&format=json&prop=text&page=" . $title;
			$cache_file = $this->cache_base . "-" . $title . ".btc";
			$results = $this->cacheCurl($curl_url, $cache_file);
			
			$text = $results["parse"]["text"]["*"];
			if (strpos($text, "REDIRECT") > -1) {
				if ($count >= 5) {
					return false;
				} else {
					$start = strpos($text, 'href="') + 6;
					$length = strpos($text, '" title=') - $start;
					$new_url = substr($text, $start, $length);
					return $this->article($new_url, $count++);
				}
			}
			return strip_tags($text, "<h1><h2><h3><h4><h5><h6><p><ul><ol><li><table><tbody><thead><tr><th><td>");
		}
	}
?>

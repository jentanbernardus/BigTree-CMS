<?
/*
	Custom BigTree integration:
		
		Copyright (c) 2012 Ben Plum, MIT license
		http://www.benjaminplum.com
		
		Requires: BTXCachableModule
*/
	
	class BTXYouTubeAPI extends BTXCachableModule {
		
		var $version = "0.1";
		
		/*
			Construct
		*/
	    public function __construct($debug = false) {
	    	global $cms;
	        
	        $this->max_cache_age = 60 * 5; // 5 mins
	        $this->cache_prefix = "btx-youtube-api";
	        
	        parent::__construct($debug);
	    }
	    
		public function search($query = false, $count = false) {
			if(!$query) {
				die("ERROR!");
			}
	    	$curl_url = "https://gdata.youtube.com/feeds/api/videos?q=" . urlencode($query) . "&format=5&orderby=viewCount&v=2&alt=json";
			if ($count) {
				$curl_url .= "&max-results=" . $count;
			}
			$cache_file = $this->cache_base . "-" . md5($curl_url) . ".btc";
			$user = $this->cacheCurl($curl_url, $cache_file);
			return $user["feed"]["entry"];
		}
	}
?>

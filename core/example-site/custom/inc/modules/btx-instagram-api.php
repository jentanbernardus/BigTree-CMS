<?
/*
	Custom BigTree integration:
		
		Copyright (c) 2012 Ben Plum, MIT license
		http://www.benjaminplum.com
		
		Requires: BTXCachableModule
*/
	
	class BTXInstagramAPI extends BTXCachableModule {
		
		var $version = "0.1";
		
		/*
			Construct
		*/
		public function __construct($debug = false) {
			global $cms;
			
			$this->max_cache_age = 60 * 5; // 5 mins
			$this->cache_prefix = "btx-instagram-api";
			
			parent::__construct($debug);
			
			$this->client_id = $cms->getSetting("btx-instagram-client-id");
		}
		
		public function setClientID($clientID) {
	    	global $admin; 
			
			if (!$clientID) {
				return false;
			}
			
			sqlquery("DELETE FROM bigtree_settings WHERE id = 'btx-instagram-client-id'");
			$setting = array(
				"id" => "btx-instagram-client-id",
				"title" => "BTX Instagram Client ID",
				"type" => "text",
				"encrypted" => "",
				"system" => "on"
			);
			$admin->createSetting($setting);
			$admin->updateSettingValue("btx-instagram-client-id", $clientID);
			
			$this->clearCache();
			
			return true;
		}
		
		public function clearClientID($clientID) {
			sqlquery("DELETE FROM bigtree_settings WHERE id = 'btx-instagram-client-id'");
			$this->clearCache();
		}
		
	    public function searchTag($tag = false, $count = false) {
			if(!$tag || !$this->client_id) {
				return false;
			}
			$tag = str_ireplace(" ", "", $tag);
			$curl_url = "https://api.instagram.com/v1/tags/" . $tag . "/media/recent?client_id=" . $this->client_id;
			if ($count) {
				$curl_url .= "&count=" . $count;
			}
			$cache_file = $this->cache_base . "-" . md5($curl_url) . ".btc";
			return $this->cacheCurl($curl_url, $cache_file);
		}
	}
	
?>
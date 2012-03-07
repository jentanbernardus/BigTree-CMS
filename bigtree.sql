DROP TABLE IF EXISTS `bigtree_404s`;
CREATE TABLE `bigtree_404s` (`id` int(11) NOT NULL AUTO_INCREMENT,`broken_url` varchar(255) NOT NULL DEFAULT '',`redirect_url` varchar(255) NOT NULL DEFAULT '',`requests` int(11) NOT NULL DEFAULT '0',`ignored` char(2) NOT NULL DEFAULT '',PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_api_tokens`;
CREATE TABLE `bigtree_api_tokens` (`id` int(11) NOT NULL AUTO_INCREMENT, `token` varchar(255) NOT NULL, `user` int(11) NOT NULL, `expires` datetime NOT NULL, `temporary` char(2) NOT NULL, `readonly` char(2) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_audit_trail`;
CREATE TABLE `bigtree_audit_trail` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `table` varchar(255) NOT NULL, `user` int(11) NOT NULL, `entry` int(11) NOT NULL, `date` datetime NOT NULL, `type` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_cache`;
CREATE TABLE `bigtree_cache` ( `view` int(11) NOT NULL, `id` varchar(255) NOT NULL, `gbp_field` text NOT NULL, `group_field` text NOT NULL, `group_sort_field` text NOT NULL, `position` int(11) NOT NULL, `approved` char(2) NOT NULL, `archived` char(2) NOT NULL, `featured` char(2) NOT NULL, `status` char(1) NOT NULL DEFAULT '', `pending_owner` int(11) NOT NULL, `column1` text NOT NULL, `column2` text NOT NULL, `column3` text NOT NULL, `column4` text NOT NULL, `column5` text NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_callouts`;
CREATE TABLE `bigtree_callouts` ( `id` varchar(255) NOT NULL, `name` varchar(255) NOT NULL DEFAULT '', `description` text NOT NULL, `resources` text NOT NULL, `position` int(11) NOT NULL, `package` int(11) NOT NULL, `level` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_feeds`;
CREATE TABLE `bigtree_feeds` ( `id` int(11) NOT NULL AUTO_INCREMENT, `route` varchar(255) NOT NULL, `name` varchar(255) NOT NULL, `description` text NOT NULL, `type` varchar(255) NOT NULL, `table` varchar(255) NOT NULL, `fields` text NOT NULL, `options` text NOT NULL, `package` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_field_types`;
CREATE TABLE `bigtree_field_types` ( `id` varchar(255) NOT NULL DEFAULT '', `foundry_id` int(11) NOT NULL, `author` varchar(255) NOT NULL, `name` varchar(255) NOT NULL, `primary_version` int(11) NOT NULL, `secondary_version` int(11) NOT NULL, `tertiary_version` int(11) NOT NULL, `description` text NOT NULL, `release_notes` text NOT NULL, `files` text NOT NULL, `pages` char(2) NOT NULL, `modules` char(2) NOT NULL, `callouts` char(2) NOT NULL, `downloaded` char(2) NOT NULL, `private` char(2) NOT NULL, `last_updated` datetime NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_locks`;
CREATE TABLE `bigtree_locks` ( `id` int(11) NOT NULL AUTO_INCREMENT, `table` varchar(255) NOT NULL, `item_id` varchar(255) NOT NULL, `last_accessed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `user` int(11) NOT NULL, `title` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_messages`;
CREATE TABLE `bigtree_messages` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `sender` int(11) NOT NULL, `recipients` text NOT NULL, `read_by` text NOT NULL, `subject` varchar(255) NOT NULL, `message` text NOT NULL, `response_to` int(11) NOT NULL, `date` datetime NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_module_actions`;
CREATE TABLE `bigtree_module_actions` ( `id` int(11) NOT NULL AUTO_INCREMENT, `module` int(11) NOT NULL DEFAULT '0', `name` varchar(255) NOT NULL DEFAULT '', `route` varchar(255) NOT NULL DEFAULT '', `in_nav` char(2) NOT NULL DEFAULT '', `form` int(11) NOT NULL, `view` int(11) NOT NULL, `class` varchar(255) NOT NULL, `position` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_module_forms`;
CREATE TABLE `bigtree_module_forms` ( `id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(255) NOT NULL, `javascript` varchar(255) NOT NULL, `css` varchar(255) NOT NULL, `callback` varchar(255) NOT NULL, `table` varchar(255) NOT NULL, `fields` text NOT NULL, `positioning` text NOT NULL, `default_position` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_module_groups`;
CREATE TABLE `bigtree_module_groups` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `position` int(11) NOT NULL DEFAULT '0', `package` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_module_packages`;
CREATE TABLE `bigtree_module_packages` ( `id` int(11) NOT NULL AUTO_INCREMENT, `foundry_id` int(11) NOT NULL, `author` varchar(255) NOT NULL, `name` varchar(255) NOT NULL, `primary_version` int(11) NOT NULL, `secondary_version` int(11) NOT NULL, `tertiary_version` int(11) NOT NULL, `description` text NOT NULL, `release_notes` text NOT NULL, `details` text NOT NULL, `group_id` int(11) NOT NULL, `module_id` int(11) NOT NULL, `tables` text NOT NULL, `files` text NOT NULL, `downloaded` char(2) NOT NULL, `private` char(2) NOT NULL, `last_updated` datetime NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_module_views`;
CREATE TABLE `bigtree_module_views` ( `id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(255) NOT NULL DEFAULT '', `description` text NOT NULL, `type` varchar(255) NOT NULL DEFAULT '', `table` varchar(255) NOT NULL DEFAULT '', `fields` text NOT NULL, `options` text NOT NULL, `actions` text NOT NULL, `suffix` varchar(255) NOT NULL, `uncached` char(2) NOT NULL, `preview_url` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_modules`;
CREATE TABLE `bigtree_modules` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL DEFAULT '', `description` text NOT NULL, `image` varchar(255) NOT NULL DEFAULT '', `route` varchar(255) NOT NULL DEFAULT '', `class` varchar(255) NOT NULL DEFAULT '', `group` int(11) NOT NULL DEFAULT '0', `position` int(11) NOT NULL, `package` int(11) NOT NULL, `gbp` text NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_page_versions`;
CREATE TABLE `bigtree_page_versions` ( `id` int(11) NOT NULL AUTO_INCREMENT, `page` int(11) NOT NULL DEFAULT '0', `title` varchar(255) NOT NULL DEFAULT '', `meta_keywords` text NOT NULL, `meta_description` text NOT NULL, `template` varchar(255) NOT NULL DEFAULT '', `external` varchar(255) NOT NULL DEFAULT '', `new_window` varchar(5) NOT NULL DEFAULT '', `resources` longtext NOT NULL, `callouts` longtext NOT NULL, `author` int(11) NOT NULL, `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP, `saved` char(2) NOT NULL, `saved_description` text NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_pages`;
CREATE TABLE `bigtree_pages` ( `id` int(11) NOT NULL AUTO_INCREMENT, `parent` int(11) NOT NULL DEFAULT '0', `in_nav` varchar(5) NOT NULL DEFAULT 'on', `nav_title` varchar(255) NOT NULL DEFAULT '', `route` varchar(30) NOT NULL, `path` text NOT NULL, `title` varchar(255) NOT NULL DEFAULT '', `meta_keywords` text NOT NULL, `meta_description` text NOT NULL, `template` varchar(255) NOT NULL DEFAULT '', `external` varchar(255) NOT NULL DEFAULT '', `new_window` varchar(5) NOT NULL DEFAULT '', `resources` longtext NOT NULL, `callouts` longtext NOT NULL, `archived` char(2) NOT NULL, `archived_inherited` char(2) NOT NULL, `locked` char(2) NOT NULL, `position` int(11) NOT NULL DEFAULT '0', `created_at` datetime NOT NULL, `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP, `publish_at` date DEFAULT NULL, `expire_at` date DEFAULT NULL, `max_age` int(11) DEFAULT 0, `last_edited_by` int(11) NOT NULL, `ga_page_views` int(11) DEFAULT 0, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;
LOCK TABLES `bigtree_pages` WRITE;

INSERT INTO `bigtree_pages` (`id`, `parent`, `in_nav`, `nav_title`, `route`, `path`, `title`, `meta_keywords`, `meta_description`, `template`, `external`, `new_window`, `resources`, `callouts`, `archived`, `archived_inherited`, `locked`, `position`, `created_at`, `updated_at`, `publish_at`, `expire_at`, `max_age`, `last_edited_by`, `ga_page_views`)
VALUES
	(0,-1,'on','BigTree Site','','','BigTree Site','','','','','','','','','','',0,'0000-00-00 00:00:00','2012-02-28 11:38:53',NULL,NULL,0,0,0);
UNLOCK TABLES;

DROP TABLE IF EXISTS `bigtree_pending_changes`;
CREATE TABLE `bigtree_pending_changes` ( `id` int(11) NOT NULL AUTO_INCREMENT, `user` int(11) NOT NULL, `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, `title` varchar(255) NOT NULL, `comments` text NOT NULL, `table` varchar(255) NOT NULL, `changes` longtext NOT NULL, `mtm_changes` longtext NOT NULL, `tags_changes` longtext NOT NULL, `item_id` int(11) NOT NULL, `type` varchar(15) NOT NULL, `module` varchar(10) NOT NULL, `pending_page_parent` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_resource_folders`;
CREATE TABLE `bigtree_resource_folders` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `name` varchar(255) DEFAULT NULL, `parent` int(11) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_resources`;
CREATE TABLE `bigtree_resources` ( `id` int(11) NOT NULL AUTO_INCREMENT, `file` varchar(255) NOT NULL, `date` datetime NOT NULL, `name` varchar(255) NOT NULL DEFAULT '', `type` varchar(255) NOT NULL DEFAULT '', `is_image` char(2) NOT NULL DEFAULT '', `height` int(11) NOT NULL DEFAULT '0', `width` int(11) NOT NULL DEFAULT '0', `crops` text NOT NULL, `thumbs` text NOT NULL, `list_thumb_margin` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_route_history`;
CREATE TABLE `bigtree_route_history` ( `id` int(11) NOT NULL AUTO_INCREMENT, `old_route` varchar(255) NOT NULL, `new_route` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_settings`;
CREATE TABLE `bigtree_settings` ( `id` varchar(255) NOT NULL DEFAULT '', `value` text NOT NULL, `type` varchar(255) NOT NULL, `name` varchar(255) NOT NULL DEFAULT '', `description` text NOT NULL, `locked` char(2) NOT NULL, `module` int(11) NOT NULL, `system` char(2) NOT NULL, `encrypted` char(2) NOT NULL, `package` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_tags`;
CREATE TABLE `bigtree_tags` ( `id` int(11) NOT NULL AUTO_INCREMENT, `tag` varchar(255) NOT NULL, `metaphone` varchar(255) NOT NULL, `route` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_tags_rel`;
CREATE TABLE `bigtree_tags_rel` ( `id` int(11) NOT NULL AUTO_INCREMENT, `module` int(11) NOT NULL, `tag` int(11) NOT NULL, `entry` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_templates`;
CREATE TABLE `bigtree_templates` ( `id` varchar(255) NOT NULL DEFAULT '', `name` varchar(255) NOT NULL DEFAULT '', `image` varchar(255) NOT NULL DEFAULT '', `module` int(11) NOT NULL, `resources` text NOT NULL, `position` int(11) NOT NULL DEFAULT '0', `description` text NOT NULL, `callouts_enabled` char(2) NOT NULL DEFAULT '', `level` int(11) NOT NULL, `package` int(11) NOT NULL, `routed` char(2) NOT NULL DEFAULT '', PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_user_group_membership`;
CREATE TABLE `bigtree_user_group_membership` ( `user` int(11) NOT NULL, `group` int(11) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_user_groups`;
CREATE TABLE `bigtree_user_groups` ( `id` int(11) NOT NULL AUTO_INCREMENT, `name` varchar(255) NOT NULL, `description` text NOT NULL, `permissions` longtext NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `bigtree_users`;
CREATE TABLE `bigtree_users` ( `id` int(11) NOT NULL AUTO_INCREMENT, `ldap` varchar(255) NOT NULL, `email` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '', `password` varchar(255) CHARACTER SET latin1 NOT NULL, `name` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '', `company` varchar(255) CHARACTER SET latin1 NOT NULL DEFAULT '', `level` int(11) NOT NULL DEFAULT '0', `permissions` text CHARACTER SET latin1 NOT NULL, `change_password_hash` varchar(255) NOT NULL, `foundry_author` text NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
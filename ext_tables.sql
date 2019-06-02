#
# Table structure for table cf_sudhaus7newspage_pagecache
CREATE TABLE cf_sudhaus7userint2ssiesi_configcache (
   id int(11) unsigned NOT NULL auto_increment,
   identifier varchar(250) DEFAULT '' NOT NULL,
   expires int(11) unsigned DEFAULT '0' NOT NULL,
   crdate int(11) unsigned DEFAULT '0' NOT NULL,
   content mediumblob,
   lifetime int(11) unsigned DEFAULT '0' NOT NULL,
   PRIMARY KEY (id),
   KEY cache_id (identifier)
) ENGINE=InnoDB;

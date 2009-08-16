#
# Table structure for table 'tx_damlightbox_ds'
#

CREATE TABLE tx_damlightbox_ds (

  uid_local int(11) NOT NULL auto_increment,
  uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
  tablenames varchar(255) DEFAULT '' NOT NULL,
  tx_damlightbox_flex mediumtext NOT NULL,
  deleted tinyint(4) DEFAULT '0' NOT NULL,
  
  PRIMARY KEY (uid_local),
  KEY tablenames (tablenames(10),uid_foreign)
);
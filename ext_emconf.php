<?php

########################################################################
# Extension Manager/Repository config file for ext: "damlightbox"
#
# Auto generated 10-04-2010 23:54
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'DAM Lightbox',
	'description' => 'Show your DAM images with the metadata of your choice in the frontend and within a templateable popup/lightbox',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.1.0',
	'dependencies' => 'dam',
	'conflicts' => '',
	'priority' => '',
	'module' => 'mod1',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Torsten Schrade',
	'author_email' => 'schradt@uni-mainz.de',
	'author_company' => 'Institute for regional history and culture, University of Mainz',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'dam' => '1.1.1-',
			'typo3' => '4.2.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'pmkslimbox' => '3.0.1-',
			'dam_ttcontent' => '1.1.0-',
			'dam_ttnews' => '0.1.9-',
			'dam_pages' => '0.1.7-',
		),
	),
	'_md5_values_when_last_written' => 'a:44:{s:13:"CHANGELOG.txt";s:4:"eee5";s:8:"TODO.txt";s:4:"28dc";s:20:"class.ext_update.php";s:4:"a5a9";s:21:"ext_conf_template.txt";s:4:"e5b2";s:12:"ext_icon.gif";s:4:"ffed";s:17:"ext_localconf.php";s:4:"3eb6";s:14:"ext_tables.php";s:4:"ec16";s:14:"ext_tables.sql";s:4:"4c40";s:15:"flexform_ds.xml";s:4:"1f26";s:16:"locallang_db.xml";s:4:"fffd";s:22:"locallang_flexform.xml";s:4:"0ad5";s:14:"doc/manual.pdf";s:4:"ba00";s:14:"doc/manual.sxw";s:4:"76aa";s:37:"hooks/class.tx_damlightbox_dblist.php";s:4:"3245";s:38:"hooks/class.tx_damlightbox_tceform.php";s:4:"888b";s:38:"hooks/class.tx_damlightbox_tcemain.php";s:4:"1837";s:13:"mod1/conf.php";s:4:"d594";s:14:"mod1/index.php";s:4:"baea";s:18:"mod1/locallang.xml";s:4:"7c84";s:22:"mod1/locallang_mod.xml";s:4:"c7e4";s:22:"mod1/mod_template.html";s:4:"3314";s:19:"mod1/moduleicon.gif";s:4:"ffed";s:32:"pi1/class.tx_damlightbox_div.php";s:4:"6529";s:34:"pi1/class.tx_damlightbox_pages.php";s:4:"f587";s:32:"pi1/class.tx_damlightbox_pi1.php";s:4:"db6f";s:39:"pi1/class.tx_damlightbox_pmkslimbox.php";s:4:"ccfb";s:36:"pi1/class.tx_damlightbox_realurl.php";s:4:"a0b9";s:38:"pi1/class.tx_damlightbox_ttaddress.php";s:4:"4242";s:35:"pi1/class.tx_damlightbox_ttnews.php";s:4:"034d";s:25:"res/basic/damlightbox.css";s:4:"6c25";s:23:"res/basic/template.html";s:4:"10c7";s:30:"res/pmkslimbox/damlightbox.css";s:4:"ff1e";s:29:"res/pmkslimbox/slimboxplus.js";s:4:"1493";s:42:"res/pmkslimbox/slimboxplus_uncompressed.js";s:4:"6051";s:28:"res/pmkslimbox/template.html";s:4:"96af";s:20:"static/constants.txt";s:4:"120d";s:16:"static/setup.txt";s:4:"171a";s:22:"static/pages/setup.txt";s:4:"7923";s:31:"static/pmkslimbox/constants.txt";s:4:"a65d";s:27:"static/pmkslimbox/setup.txt";s:4:"84b6";s:27:"static/tt_address/setup.txt";s:4:"703c";s:27:"static/tt_content/setup.txt";s:4:"5aee";s:24:"static/tt_news/setup.txt";s:4:"981a";s:27:"static/watermarks/setup.txt";s:4:"f144";}',
	'suggests' => array(
	),
);

?>
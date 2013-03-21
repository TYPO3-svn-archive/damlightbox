<?php

########################################################################
# Extension Manager/Repository config file for ext "damlightbox".
#
# Auto generated 02-11-2012 06:29
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'DAM Lightbox',
	'description' => 'Show your DAM images with the metadata of your choice in the frontend and within a templateable popup/lightbox',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.2.1',
	'dependencies' => 'dam',
	'conflicts' => '',
	'priority' => 'bottom',
	'module' => 'mod1',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Torsten Schrade',
	'author_email' => 'schradt@uni-mainz.de',
	'author_company' => 'Academy of Sciences and Literature Mainz | www.digitale-akademie.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'dam' => '1.1.1-0.0.0',
			'typo3' => '4.5.0-4.7.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'd4u_slimbox' => '2.2.0-0.0.0',
			'dam_ttcontent' => '1.1.0-0.0.0',
			'dam_ttnews' => '0.1.9-0.0.0',
			'dam_pages' => '0.1.7-0.0.0',
		),
	),
	'_md5_values_when_last_written' => 'a:43:{s:13:"CHANGELOG.txt";s:4:"0198";s:20:"class.ext_update.php";s:4:"a5a9";s:21:"ext_conf_template.txt";s:4:"e5b2";s:12:"ext_icon.gif";s:4:"ffed";s:17:"ext_localconf.php";s:4:"9a40";s:14:"ext_tables.php";s:4:"4101";s:14:"ext_tables.sql";s:4:"4c40";s:15:"flexform_ds.xml";s:4:"ec69";s:16:"locallang_db.xml";s:4:"fffd";s:22:"locallang_flexform.xml";s:4:"0ad5";s:8:"TODO.txt";s:4:"28dc";s:14:"doc/manual.sxw";s:4:"a20b";s:37:"hooks/class.tx_damlightbox_dblist.php";s:4:"2e5a";s:38:"hooks/class.tx_damlightbox_tceform.php";s:4:"13bf";s:38:"hooks/class.tx_damlightbox_tcemain.php";s:4:"fc60";s:13:"mod1/conf.php";s:4:"c937";s:14:"mod1/index.php";s:4:"d983";s:18:"mod1/locallang.xml";s:4:"5268";s:22:"mod1/locallang_mod.xml";s:4:"416b";s:22:"mod1/mod_template.html";s:4:"3314";s:19:"mod1/moduleicon.gif";s:4:"ffed";s:40:"pi1/class.tx_damlightbox_d4u_slimbox.php";s:4:"d868";s:32:"pi1/class.tx_damlightbox_div.php";s:4:"4bb1";s:34:"pi1/class.tx_damlightbox_pages.php";s:4:"a74d";s:32:"pi1/class.tx_damlightbox_pi1.php";s:4:"5a4c";s:36:"pi1/class.tx_damlightbox_realurl.php";s:4:"73b4";s:38:"pi1/class.tx_damlightbox_ttaddress.php";s:4:"a9aa";s:35:"pi1/class.tx_damlightbox_ttnews.php";s:4:"3176";s:25:"res/basic/damlightbox.css";s:4:"6c25";s:23:"res/basic/template.html";s:4:"10c7";s:31:"res/d4u_slimbox/damlightbox.css";s:4:"2ab3";s:30:"res/d4u_slimbox/slimboxplus.js";s:4:"1493";s:43:"res/d4u_slimbox/slimboxplus_uncompressed.js";s:4:"6051";s:29:"res/d4u_slimbox/template.html";s:4:"5f9f";s:20:"static/constants.txt";s:4:"120d";s:16:"static/setup.txt";s:4:"c745";s:32:"static/d4u_slimbox/constants.txt";s:4:"1c5d";s:28:"static/d4u_slimbox/setup.txt";s:4:"890d";s:22:"static/pages/setup.txt";s:4:"c54e";s:27:"static/tt_address/setup.txt";s:4:"cb74";s:27:"static/tt_content/setup.txt";s:4:"c8ad";s:24:"static/tt_news/setup.txt";s:4:"2280";s:27:"static/watermarks/setup.txt";s:4:"f144";}',
	'suggests' => array(
	),
);

?>
<?php

########################################################################
# Extension Manager/Repository config file for ext: "damlightbox"
#
# Auto generated 13-02-2008 07:36
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
	'version' => '0.0.2',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'alpha',
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
			'typo3' => '4.2.0-',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'pmkslimbox' => '3.0.1-',
			'dam_ttcontent' => '1.1.0-',
			'dam_ttnews' => '0.1.9-',
		),
	),
	'_md5_values_when_last_written' => 'a:20:{s:13:"CHANGELOG.txt";s:4:"733d";s:8:"TODO.txt";s:4:"bf53";s:21:"ext_conf_template.txt";s:4:"6fb1";s:12:"ext_icon.gif";s:4:"ffed";s:14:"ext_tables.php";s:4:"a9ce";s:14:"ext_tables.sql";s:4:"b6af";s:15:"flexform_ds.xml";s:4:"7974";s:16:"locallang_db.xml";s:4:"2cea";s:22:"locallang_flexform.xml";s:4:"30d6";s:14:"doc/manual.sxw";s:4:"75fc";s:32:"pi1/class.tx_damlightbox_pi1.php";s:4:"978f";s:26:"res/scripts/damlightbox.js";s:4:"060b";s:20:"static/constants.txt";s:4:"65e2";s:16:"static/setup.txt";s:4:"91d4";s:31:"static/pmkslimbox/constants.txt";s:4:"0d9a";s:27:"static/pmkslimbox/setup.txt";s:4:"57be";s:16:"tmpl/slimbox.css";s:4:"8d87";s:17:"tmpl/slimbox.html";s:4:"6a74";s:17:"tmpl/standard.css";s:4:"6c25";s:18:"tmpl/standard.html";s:4:"25de";}',
);
?>
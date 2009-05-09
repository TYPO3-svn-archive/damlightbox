<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Extension config
$_EXTCONF = unserialize($_EXTCONF);

// Adding the TS Objects
t3lib_extMgm::addStaticFile($_EXTKEY,'static/','DAM Lightbox: basics');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/pmkslimbox','DAM Lightbox: pmkslimbox');
#t3lib_extMgm::addStaticFile($_EXTKEY,'static/watermarks','DAM Lightbox: watermarks');

// load $TCA of tt_content for changes afterwards
t3lib_div::loadTCA('tt_content');

// add the new flexform field to $TCA
$tempColumns = Array (
	'tx_damlightbox_flex' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:damlightbox/locallang_db.xml:tt_content.tx_damlightbox_flex',
		'config' => Array (
			'type' => 'flex',
         	'ds' => array(
            	'default' => ''.$_EXTCONF['flexformFile'].'',
        	 )
        )
	),
);
t3lib_extMgm::addTCAcolumns('tt_content', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('tt_content','tx_damlightbox_flex','image,textpic','after:tx_damttcontent_files');

// Set new label for tx_damttcontent_files
$TCA['tt_content']['columns']['tx_damttcontent_files']['label'] = 'LLL:EXT:damlightbox/locallang_db.xml:tt_content.tx_damttcontent_files';
?>

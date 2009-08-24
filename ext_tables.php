<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Extension config
$_EXTCONF = unserialize($_EXTCONF);

// Adding the TS Objects
t3lib_extMgm::addStaticFile($_EXTKEY,'static/','DAM Lightbox: basics');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/pmkslimbox','DAM Lightbox: pmkslimbox');
#t3lib_extMgm::addStaticFile($_EXTKEY,'static/watermarks','DAM Lightbox: watermarks');

$tempColumns = Array (
	'tx_damlightbox_flex' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:damlightbox/locallang_db.xml:tx_damlightbox_flex',
		'config' => Array (
			'type' => 'flex',
         	'ds' => Array (
            	'default' => $_EXTCONF['flexformFile'],
        	)
        )
	),
);

$allowedTables = t3lib_div::trimExplode(';', $_EXTCONF['allowedTables'], 1);

if (is_array($allowedTables)) {
	
	foreach ($allowedTables as $configstring) {
		
		$tableconfig = t3lib_div::trimExplode('|', $configstring, 1);
				
		$table = $tableconfig[0];
		
		t3lib_div::loadTCA($table);
		
		$fields = 'tx_damlightbox_image, tx_damlightbox_flex';
		$types = '';
		$after = '';	
		
		foreach ($tableconfig as $config) {

			if (strpos($config, 'types:') !== FALSE) $types = str_replace('types:', '', $config);
			if (strpos($config, 'after:') !== FALSE) $after = str_replace('after:', '', $config);

			if (strpos($config, 'reffield:') !== FALSE) {
				// just add the flexfield
				$fields = 'tx_damlightbox_flex';
				unset($tempColumns['tx_damlightbox_image']);
			} else {
				// ad the universal reverence field
				$tempColumns['tx_damlightbox_image'] = txdam_getMediaTCA('image_field', 'tx_damlightbox_image');
				$tempColumns['tx_damlightbox_image']['label'] = 'LLL:EXT:damlightbox/locallang_db.xml:tx_damlightbox_image';
			}
		}

		t3lib_extMgm::addTCAcolumns($table, $tempColumns, 1);
		t3lib_extMgm::addToAllTCAtypes($table, $fields, $types, 'after:'.$after.'');
	}
}
?>
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
		'label' => 'LLL:EXT:damlightbox/locallang_db.xml:tt_content.tx_damlightbox_flex',
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
	
	foreach ($allowedTables as $table) {
		
		$after = strpos($table, ':');
		$types = strpos($table, '|');
		
		// table:field
		if (FALSE !== $after && FALSE === $types) {
			$field = substr($table, $after+1);
			$TCAtypes = '';
			$table = substr($table, 0, $after);
			
		// table|types:field
		} elseif (FALSE !== $after && FALSE !== $types) {
			$field = substr($table, $after+1);
			$TCAtypes = str_replace(':'.$field, '', substr($table, $types+1));
			$table = substr($table, 0, $types);	
		
		// table|types
		} elseif (FALSE === $after && FALSE !== $types) {
			$field = '';
			$TCAtypes = substr($table, $types+1);
			$table = substr($table, 0, $types);			
		}
		// else it's just the tablename

		t3lib_extMgm::addTCAcolumns($table, $tempColumns, 1);
		t3lib_extMgm::addToAllTCAtypes($table, 'tx_damlightbox_flex', $TCAtypes, 'after:'.$field.'');
	}
}
?>

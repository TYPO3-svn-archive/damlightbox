<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

// Defining constants. This will save some time and repetition
if (!defined('PATH_damlightbox')) {    
	define('PATH_damlightbox', t3lib_extMgm::extPath('damlightbox'));
}

// Extracting configuration from EM
$TYPO3_CONF_VARS['EXTCONF']['damlightbox'] = unserialize($_EXTCONF);

//	Registering hooks
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:damlightbox/hooks/class.tx_damlightbox_tcemain.php:tx_damlightbox_tcemain';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:damlightbox/hooks/class.tx_damlightbox_tcemain.php:tx_damlightbox_tcemain';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getMainFieldsClass'][] = 'EXT:damlightbox/hooks/class.tx_damlightbox_tceform.php:tx_damlightbox_tceform';

// RealURL autoconfiguration 
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['damlightbox'] = 'EXT:damlightbox/pi1/class.tx_damlightbox_realurl.php:tx_damlightbox_realurl->addDamlightboxConfig';
?>
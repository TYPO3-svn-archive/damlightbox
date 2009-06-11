<?php
	if (!defined ('TYPO3_MODE')) die ('Access denied.');
	
	// RealURL autoconfiguration 
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['damlightbox'] = 'EXT:damlightbox/pi1/class.tx_damlightbox_realurl.php:tx_damlightbox_realurl->addDamlightboxConfig';
?>
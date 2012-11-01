<?php
if (!defined ('TYPO3_MODE')) die ('Access denied.');

// Extracting configuration from EM
$TYPO3_CONF_VARS['EXTCONF']['damlightbox'] = unserialize($_EXTCONF);

// Registering hooks
// TCEmain
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:damlightbox/hooks/class.tx_damlightbox_tcemain.php:tx_damlightbox_tcemain';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:damlightbox/hooks/class.tx_damlightbox_tcemain.php:tx_damlightbox_tcemain';
// TCEForms
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getSingleFieldClass'][] = 'EXT:damlightbox/hooks/class.tx_damlightbox_tceform.php:tx_damlightbox_tceform';
// List module
$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/class.db_list_extra.inc']['getTable'][] = 'EXT:damlightbox/hooks/class.tx_damlightbox_dblist.php:tx_damlightbox_dblist';

// RealURL autoconfiguration 
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['damlightbox'] = 'EXT:damlightbox/pi1/class.tx_damlightbox_realurl.php:tx_damlightbox_realurl->addDamlightboxConfig';

// check if dam_ttcontent is loaded, and if so include TS
if (!t3lib_extMgm::isLoaded('dam_ttcontent')) {

	t3lib_extMgm::addTypoScript(
		$_EXTKEY,
		'setup','

		tt_content.image.20.imgList >
		tt_content.image.20.imgList.cObject = USER
		tt_content.image.20.imgList.cObject {
			userFunc = tx_dam_tsfe->fetchFileList
			refField = tx_damlightbox_image
			refTable = tt_content
		}
		tt_content.image.20.imgPath >
		tt_content.image.20.imgPath =
		',
		43
	);
}

// tt_address hook
if (t3lib_extMgm::isLoaded('tt_address')) {
	$TYPO3_CONF_VARS['EXTCONF']['tt_address']['extraItemMarkerHook']['tx_damlightbox_pi1'] = 'EXT:damlightbox/pi1/class.tx_damlightbox_ttaddress.php:tx_damlightbox_ttaddress';
}
?>
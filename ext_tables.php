<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Extension config
$_EXTCONF = unserialize($_EXTCONF);

// Adding the TS Objects
t3lib_extMgm::addStaticFile($_EXTKEY,'static/','DAM Lightbox: basics');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/tt_content','DAM Lightbox: tt_content');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/tt_news','DAM Lightbox: tt_news');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/pmkslimbox','DAM Lightbox: pmkslimbox');
#t3lib_extMgm::addStaticFile($_EXTKEY,'static/watermarks','DAM Lightbox: watermarks');
?>
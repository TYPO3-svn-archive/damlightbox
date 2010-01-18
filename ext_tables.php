<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Extension config
$_EXTCONF = unserialize($_EXTCONF);

// basic TS Objects
t3lib_extMgm::addStaticFile($_EXTKEY,'static/','DAM Lightbox: basics');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/tt_content','DAM Lightbox: tt_content');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/pages','DAM Lightbox: pages');
#t3lib_extMgm::addStaticFile($_EXTKEY,'static/watermarks','DAM Lightbox: watermarks');

### TABLE SCRIPTS ###

# tt_news
if (t3lib_extMgm::isLoaded('tt_news')) {

	t3lib_extMgm::addStaticFile($_EXTKEY,'static/tt_news','DAM Lightbox: tt_news');

}

### LIGHTBOXES ###

# pmkslimbox
if (t3lib_extMgm::isLoaded('pmkslimbox')) {

	t3lib_extMgm::addStaticFile($_EXTKEY,'static/pmkslimbox','DAM Lightbox: pmkslimbox');

}
?>
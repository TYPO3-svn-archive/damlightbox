<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

// Extension config
$_EXTCONF = unserialize($_EXTCONF);

// basic TS Objects
t3lib_extMgm::addStaticFile($_EXTKEY,'static/','DAM Lightbox: basics');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/tt_content','DAM Lightbox: tt_content');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/pages','DAM Lightbox: pages');
#t3lib_extMgm::addStaticFile($_EXTKEY,'static/watermarks','DAM Lightbox: watermarks');

if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModulePath('tools_txdamlightboxM1', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
	t3lib_extMgm::addModule('tools', 'txdamlightboxM1', '', t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');
}

### DAMLIGHTBOX FIELDS ###

define('TX_DAMLIGHTBOX_FIELDCONF_FILE', PATH_site . 'typo3conf/damlightbox_conf.php');

$tempColumns = Array (
	'tx_damlightbox_flex' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:damlightbox/locallang_db.xml:tx_damlightbox_flex',
		'config' => Array (
			'type' => 'flex',
			'ds' => Array (
				'default' => 'FILE:EXT:damlightbox/flexform_ds.xml',
			),
		),
	),
	'tx_damlightbox_image' => txdam_getMediaTCA('image_field', 'tx_damlightbox_image'),
);

$tempColumns['tx_damlightbox_image']['label'] = 'LLL:EXT:damlightbox/locallang_db.xml:tx_damlightbox_image';
$tempColumns['tx_damlightbox_image']['exclude'] = 1;

if (@file_exists(TX_DAMLIGHTBOX_FIELDCONF_FILE)) {
		@require_once(TX_DAMLIGHTBOX_FIELDCONF_FILE);
}

### TABLE SCRIPTS ###

# tt_news
if (t3lib_extMgm::isLoaded('tt_news')) {
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/tt_news','DAM Lightbox: tt_news');
}

# tt_address
if (t3lib_extMgm::isLoaded('tt_address')) {
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/tt_address','DAM Lightbox: tt_address');
}

### LIGHTBOXES ###

# d4u_slimbox
if (t3lib_extMgm::isLoaded('d4u_slimbox')) {
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/d4u_slimbox','DAM Lightbox: d4u_slimbox');
}

# perfectlightbox
if (t3lib_extMgm::isLoaded('perfectlightbox')) {
	t3lib_extMgm::addStaticFile($_EXTKEY,'static/perfectlightbox','DAM Lightbox: perfectlightbox');
}
?>
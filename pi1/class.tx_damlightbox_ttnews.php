<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Torsten Schrade <schradt@uni-mainz.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Contains functions vor tt_news image rendering
 *
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *   54: function imageMarkerFunc($itemConfig, $pObjRef)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
/**
 *
 * @author		Torsten Schrade <schradt@uni-mainz.de>
 * @package 	TYPO3
 * @subpackage 	damlightbox
 */

final class tx_damlightbox_ttnews {
	
	/**
	 * Connects tt_news image processing to the damlightbox routine to fetch DAM images and according metadata. The rest of the processing can be done with pure TypoScript.
	 * Just have a look in the static include file for tt_news.
	 *
	 * @param	string		$itemConfig: TypoScript configuration of the current news item
	 * @param	string		$pObjRef: Reference to the parent object
	 * @return	string		The accumulated HTML for the news images
	 */
	function imageMarkerFunc($itemConfig, $pObjRef) {

		// $config of the current news item in an array
		$itemConfig = $itemConfig[1];
		
		// reference to the parent-object
		$pObj = &$pObjRef['parentObj'];

		// set current row from parent object
		$row = $pObj->local_cObj->data;
		
		// conf of the image marker function
		$conf = $pObj->conf['imageMarkerFunc.'];

		// execute damlightbox function to fetch images and metadata
		$pObj->local_cObj->cObjGetSingle($conf['executeDamlightbox'], $conf['executeDamlightbox.']);
				
		// set display mode
		$mode = $pObj->config['code'];
		
		// fistImageIsPreview mode in SINGLE: remove the first image from the list
		if ($mode == 'SINGLE' && substr_count($GLOBALS['TSFE']->register['tx_damlightbox']['damImages'], ',') > 0 && $pObj->config['firstImageIsPreview']) {
			$GLOBALS['TSFE']->register['tx_damlightbox']['damImages'] = substr($GLOBALS['TSFE']->register['tx_damlightbox']['damImages'], strpos($GLOBALS['TSFE']->register['tx_damlightbox']['damImages'], ',')+1);
			array_shift($GLOBALS['TSFE']->register['tx_damlightbox']['metaData']);
		}
				
		// get the final image list / enable TS processing
		$images = t3lib_div::trimExplode(',', $pObj->local_cObj->cObjGetSingle($conf[$mode.'.']['imgList'], $conf[$mode.'.']['imgList.']), 1);
		
		// reset image marker
		$markerArray['###NEWS_IMAGE###'] = '';

		debug($itemConfig);
		
		// processing of the images
		if ($images) {
			
			// image count in tt_news
			$imageCount = isset($itemConfig['imageCount']) ? $itemConfig['imageCount']:1;
			
			// set global counter - can be accessed like IMAGE_NUM_CURRENT from TS but in tt_news context
			$GLOBALS['TSFE']->register['currentImg'] = 0;
			
			// walk through each image
			foreach ($images as $img) {
				
				// if imgCount is reached stop the processing
				if ($GLOBALS['TSFE']->register['currentImg'] == $imageCount) break;
				
				// execute the TS configuration
				$theImgCode .= $pObj->local_cObj->IMAGE($conf[$mode.'.']['image.']);
				
				// raise the global image count
				$GLOBALS['TSFE']->register['currentImg']++;
				
			}
			
			// fill the accumulated image code into the marker
			$markerArray['###NEWS_IMAGE###'] = $pObj->local_cObj->wrap(trim($theImgCode), $conf[$mode.'.']['imageWrapIfAny']);
			
		} else {
			
			// if no images are there execute noImage_stdWrap as normal
			$markerArray['###NEWS_IMAGE###'] = $pObj->local_cObj->stdWrap($markerArray['###NEWS_IMAGE###'], $conf[$mode.'.']['noImage_stdWrap.']);
		}		
		
		// pass the image HTML back to tt_news
		return $markerArray;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_ttnews.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_ttnews.php']);
}
?>
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Torsten Schrade <schradt@uni-mainz.de>
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
 *   48: class tx_damlightbox_ttnews extends tx_ttnews
 *   58:     public function imageMarkerFunc($itemConfig, $pObjRef)
 *  146:     private function getImageMarkersClassic($pObj)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
/**
 *
 * @author		Torsten Schrade <schradt@uni-mainz.de>
 * @package 	TYPO3
 * @subpackage 	damlightbox
 */

class tx_damlightbox_ttnews extends tx_ttnews {

	/**
	 * Connects tt_news image processing to the damlightbox routine to fetch DAM images and according metadata. The rest of the processing can be done with pure TypoScript.
	 * Just have a look in the static include file for tt_news.
	 * 
	 * @param	string		$itemConfig: TypoScript configuration of the current news item
	 * @param	string		$pObjRef: Reference to the parent object
	 * @return	string		The accumulated HTML for the news images
	 */
	public function imageMarkerFunc($itemConfig, $pObjRef) {

			// reference to the parent-object
		$pObj = $pObjRef['parentObj'];

			// $config of the current news item in an array
		$this->itemConfig = $itemConfig[1];

			// current marker array
		$markerArray = $itemConfig[0];

			// set current row from parent object
		$this->row = $pObj->local_cObj->data;

			// conf of the image marker function
		$this->conf = $pObj->conf['imageMarkerFunc.'];

			// execute damlightbox function to fetch images and metadata
		$pObj->local_cObj->cObjGetSingle($this->conf['executeDamlightbox'], $this->conf['executeDamlightbox.']);

			// set display mode
		$this->mode = $pObj->config['code'];

			// maxW & maxH
		$this->conf[$this->mode.'.']['image.']['file.']['maxW'] = $this->itemConfig['image.']['file.']['maxW'];
		$this->conf[$this->mode.'.']['image.']['file.']['maxH'] = $this->itemConfig['image.']['file.']['maxH'];

			// fistImageIsPreview mode in SINGLE: remove the first image from the list
		if ($this->mode == 'SINGLE' && substr_count($GLOBALS['TSFE']->register['tx_damlightbox']['damImages'], ',') > 0 && $pObj->config['firstImageIsPreview']) {
			$GLOBALS['TSFE']->register['tx_damlightbox']['damImages'] = substr($GLOBALS['TSFE']->register['tx_damlightbox']['damImages'], strpos($GLOBALS['TSFE']->register['tx_damlightbox']['damImages'], ',')+1);
			array_shift($GLOBALS['TSFE']->register['tx_damlightbox']['metaData']);
		}

			// get the final image list / enable TS processing
		$images = t3lib_div::trimExplode(',', $pObj->local_cObj->cObjGetSingle($this->conf[$this->mode.'.']['imgList'], $this->conf[$this->mode.'.']['imgList.']), 1);

			// reset image marker
		$markerArray['###NEWS_IMAGE###'] = '';

			// processing of the images
		if ($images) {

				// image count in tt_news
			$imageCount = isset($this->itemConfig['imageCount']) ? $this->itemConfig['imageCount']:1;

				// set global counter - can be accessed like IMAGE_NUM_CURRENT from TS but in tt_news context
			$GLOBALS['TSFE']->register['currentImg'] = 0;

				// walk through each image
			foreach ($images as $img) {

					// if imgCount is reached stop the processing
				if ($GLOBALS['TSFE']->register['currentImg'] == $imageCount) break;

					// image
				$theImgCode .= $pObj->local_cObj->IMAGE($this->conf[$this->mode.'.']['image.']);
	
					// caption
				$theImgCode .= $pObj->local_cObj->stdWrap($pObj->local_cObj->cObjGetSingle($this->conf[$this->mode.'.']['caption'], $this->conf[$this->mode.'.']['caption.']), $this->conf[$this->mode.'.']['caption_stdWrap.']);

					// raise the global image count
				$GLOBALS['TSFE']->register['currentImg']++;

			}

				// fill the accumulated image code into the marker
			$markerArray['###NEWS_IMAGE###'] = $pObj->local_cObj->wrap(trim($theImgCode), $this->conf[$this->mode.'.']['imageWrapIfAny']);

			// to allow easy transition from classic newsimages to damlightbox handling implement the standard function from tt_news
		} elseif ($this->row['image']) {

			$theImgCode = $this->getImageMarkersClassic($pObj);

			if ($theImgCode) $markerArray['###NEWS_IMAGE###'] = $pObj->local_cObj->wrap(trim($theImgCode), $this->conf[$this->mode.'.']['imageWrapIfAny']);

			// if no images are present execute noImage_stdWrap as normal
		} else {
			$markerArray['###NEWS_IMAGE###'] = $pObj->local_cObj->stdWrap($markerArray['###NEWS_IMAGE###'], $this->conf[$this->mode.'.']['noImage_stdWrap.']);
		}

			// pass the image HTML back to tt_news
		return $markerArray;
	}

	/**
	 * The 'classic' image function from tt_news. It's used as long as there are no DAM images present to provide a smooth transition to damlightbox handling of images
	 * 
	 * @param	object		$pObj: The calling tt_news parent object
	 * 
	 * @return	string		The HTML code for the images
	 */
	private function getImageMarkersClassic($pObj) {

		$imageNum = isset($this->itemConfig['imageCount']) ? $this->itemConfig['imageCount']:1;
		$imageNum = t3lib_div::intInRange($imageNum, 0, 100);
		$theImgCode = '';
		$imgs = t3lib_div::trimExplode(',', $this->row['image'], 1);
		$imgsCaptions = explode(chr(10), $this->row['imagecaption']);
		$imgsAltTexts = explode(chr(10), $this->row['imagealttext']);
		$imgsTitleTexts = explode(chr(10), $this->row['imagetitletext']);

		reset($imgs);

		$cc = 0;
			// remove first img from the image array in single view if the TSvar firstImageIsPreview is set
		if ((	(count($imgs) > 1 && $this->itemConfig['firstImageIsPreview'])
				||
				(count($imgs) >= 1 && $this->itemConfig['forceFirstImageIsPreview'])
			) && $this->mode == 'SINGLE') {
			array_shift($imgs);
			array_shift($imgsCaptions);
			array_shift($imgsAltTexts);
			array_shift($imgsTitleTexts);
		}
			// get img array parts for single view pages
		if ($pObj->piVars[$this->itemConfig['singleViewPointerName']]) {
			$spage = $pObj->piVars[$this->itemConfig['singleViewPointerName']];
			$astart = $imageNum*$spage;
			$imgs = array_slice($imgs,$astart,$imageNum);
			$imgsCaptions = array_slice($imgsCaptions,$astart,$imageNum);
			$imgsAltTexts = array_slice($imgsAltTexts,$astart,$imageNum);
			$imgsTitleTexts = array_slice($imgsTitleTexts,$astart,$imageNum);
		}

		while (list(, $val) = each($imgs)) {
			if ($cc == $imageNum) break;
			if ($val) {

				$this->itemConfig['image.']['altText'] = $imgsAltTexts[$cc];
				$this->itemConfig['image.']['titleText'] = $imgsTitleTexts[$cc];
				$this->itemConfig['image.']['file'] = 'uploads/pics/' . $val;
			}
			$theImgCode .= $pObj->local_cObj->IMAGE($this->itemConfig['image.']) . $pObj->local_cObj->stdWrap($imgsCaptions[$cc], $this->itemConfig['caption_stdWrap.']);
			$cc++;
		}

		return($theImgCode);
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_ttnews.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_ttnews.php']);
}
?>
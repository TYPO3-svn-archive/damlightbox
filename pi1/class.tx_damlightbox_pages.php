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
 * Contains damlightbox functions for pages table
 *
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *   45: class tx_damlightbox_pages extends tx_damlightbox_pi1
 *   54: public function rootLineSlide($content, $conf) 
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

class tx_damlightbox_pages extends tx_damlightbox_pi1 {

	/**
	 * 'Slide' along the rootline and check if one of the damlightbox fields in the pages properties carries images. If so render them.
	 *
	 * @param	string		Current content
	 * @param	array		Current TypoScript configuration
	 * @return	string		HTML for the images in case some were found along the rootline
	 */
	public function rootLineSlide($content, $conf) {

			// get the rootline
		$rootLine = $GLOBALS['TSFE']->rootLine;

			// leave out the current level (this function is called at a point where it's clear that there are no images at the current level)
		if ($conf['start'] == -1) array_shift($rootLine);

			// walk along the rootline and look if a damlightbox field carries images
		foreach ($rootLine as $value) {

				// change the page id to the according level
			$this->cObj->currentRecord = 'pages:'.$value['uid'];

				// change the uid as if being on that page
			$this->cObj->data['uid'] = $value['uid'];

				// execute damlightbox with the newly set properties			
			$this->main($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_damlightbox_pi1'], $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_damlightbox_pi1.']);

				// if there are DAM images on the according level, render them
			if ($GLOBALS['TSFE']->register['tx_damlightbox']['damImages']) {

					// calling the iterator in case there are several images
				$content = $this->frontendImageIterator($content, $conf);

					// stop the walk since images have been found
				break;
			}
		}
		return $content;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_pages.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_pages.php']);
}
?>
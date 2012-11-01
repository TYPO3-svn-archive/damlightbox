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
 * Uses hook in tt_address to insert an extra marker for DAM images
 *
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   49: class tx_damlightbox_ttaddress extends tx_ttaddress_pi1
 *   61:     public function extraItemMarkerProcessor($markerArray, $address, &$lConf, &$pObj)
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

class tx_damlightbox_ttaddress extends tx_ttaddress_pi1 {

	/** Hook function for inserting custom markers in tt_address. Used for connecting DAM Lightbox to tt_address
	 *
	 * @param	array		The existing marker array for tt_address
	 * @param	array		The current tt_address record
	 * @param	array		The TypoScript configuration of the parent object
	 * @param	object		The parent object that is calling the hook function
	 *
	 * @return	array		The processed marker array with the DAM image marker
	 *
	 */
	public function extraItemMarkerProcessor($markerArray, $address, &$lConf, &$pObj) {

		// prepare the context: set the address record as current cObject
		$pObj->cObj->data = $address;
		$pObj->cObj->currentRecord = 'tt_address:'.$address['uid'];

		// insert marker for DAM images with stdWrap properties
		$markerArray['###DAM_IMAGE###'] = $pObj->cObj->stdWrap($lConf['dam_image.'], $lConf['dam_image.']);

		return $markerArray;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_ttaddress.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_ttaddress.php']);
}
?>
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Torsten Schrade <schradt@uni-mainz.de>
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
 * Contains damlightbox functions for pmkslimbox usage
 *
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
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

class tx_damlightbox_pmkslimbox extends tx_damlightbox_pi1 {
	
	var $uid = 0;
	var $table = '';
	var $title = '';
	
	/**
	 * If the preview mode is set and pmkslimbox is used the remaining imagelinks need to be inserted in a hidden div. Otherwise the lightbox will not be browsable and just open the preview image.
	 *
	 * @param	[type]		$content: ...
	 * @param	[type]		$conf: ...
	 * @return	string		Hidden div with the remaining imagelinks
	 */
	public function addHiddenImgs($content, $conf) {
		
		// check if there is more than one image and if yes insert a hidden div
		if (count($GLOBALS['TSFE']->register['tx_damlightbox']['metaData']) > 1) {
			foreach ($GLOBALS['TSFE']->register['tx_damlightbox']['metaData'] as $key => $value) {

				// leave out the first image and any image that is hidden in DAM
				if ($key == '0' || $value['hidden'] == 1) continue;

				// get uid & table of the current record
				$this->uid = $this->cObj->stdWrap($conf['content'], $conf['content.']);
				$this->table = $conf['content.']['table'];
				
				// get the caption for the title attribute of the links
				$lbCaption = $GLOBALS['TSFE']->register['tx_damlightbox']['config']['sLIGHTBOX']['lbCaption'];
				$this->title = $GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$key][''.$lbCaption.''];

				// specific width/height calculations
				$hCalc = t3lib_div::trimExplode('|',$this->cObj->stdWrap(null,$conf['hCalc.']));
				$vCalc = t3lib_div::trimExplode('|',$this->cObj->stdWrap(null,$conf['vCalc.']));			
				$GLOBALS['TSFE']->register['widthCalc'] = intval(t3lib_div::calcParenthesis($hCalc[0].$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$key]['hpixels'].$hCalc[1]));
				$GLOBALS['TSFE']->register['heightCalc'] = intval(t3lib_div::calcParenthesis($vCalc[0].$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$key]['vpixels'].$vCalc[1]));
				
				$fullPath = t3lib_div::getIndpEnv('TYPO3_SITE_URL').$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$key]['fullPath'];

				// check if specific dimensions are set in the flexform
				if ($GLOBALS['TSFE']->register['tx_damlightbox']['config']['sLIGHTBOX']['setSpecificDimensions']) $this->overrideDimsFromFlexform($key,null);

				// configurations for the typolink
				$linkConfig=array();
				$linkConfig['parameter'] = $GLOBALS['TSFE']->id;
				$linkConfig['no_cache'] = 0;
				$linkConfig['useCacheHash'] = 1;
				$linkConfig['additionalParams'] = '&type=313&content='.$this->table.'_'.$this->uid.'&img='.$key.'';
				$linkConfig['ATagParams'] = 'title="'.$this->title.'" rev="width='.$GLOBALS['TSFE']->register['widthCalc'].', height='.$GLOBALS['TSFE']->register['heightCalc'].', src='.$fullPath.'" rel="lightbox[sb'.$this->uid.']"';
				$linkConfig['ATagBeforeWrap'] = 1;

				$hiddenLinks .= $this->cObj->typoLink(null,$linkConfig);
			}
			$content = '<div style="display: none;">'.$hiddenLinks.'</div>';
		}
		return $content;
	}



	/**
	 * Checks if there are custom dimension set for the lightbox of the current image in the flexform of the content element and if yes overrides the calculated
	 * values from TS. The expected notation in the flexorm is "imagenumber:width,height" starting with number 1 for the first image
	 *
	 * @param	int			The current image number
	 * @param	array		TypoScript configuration array
	 * @return	string		Hidden div with the remaining imagelinks
	 */
	public function overrideDimsFromFlexform($content, $conf) {
		
		// check if some specific dimensions are set in the flexform
		$customDims = array();
		$customDims = t3lib_div::trimExplode(';', $GLOBALS['TSFE']->register['tx_damlightbox']['config']['sLIGHTBOX']['setSpecificDimensions'], 1);

		if ($customDims) {
			foreach($customDims as $value) {
				// check if the current image dimensions in the GLOBAL register need to be overridden
				if (substr($value, 0, 1)-1 == $content) {
					$dims = t3lib_div::trimExplode(',',substr($value,strpos($value,':')+1));
					if ($dims) {
						$GLOBALS['TSFE']->register['widthCalc'] = htmlspecialchars($dims[0]);
						$GLOBALS['TSFE']->register['heightCalc'] = htmlspecialchars($dims[1]);
					}
				}
			}
		}
		return;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_pmkslimbox.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_pmkslimbox.php']);
}
?>
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
* Fetch DAM data of images and transfer it into TSFE registers
*
* @author Torsten Schrade <schradt@uni-mainz.de>
*
*/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   52: class tx_damlightbox_pi1 extends tslib_pibase
 *   70:     function main ($content, $conf)
 *  119:     function getDamRecords()
 *  226:     function addHiddenImgs($content, $conf)
 *  272:     function overrideDimsFromFlexform($content, $conf)
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('damlightbox').'pi1/class.tx_damlightbox_div.php');
require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_damlightbox_pi1 extends tslib_pibase {

	var $prefixID = 'tx_damlightbox_pi1';
	var $scriptRelPath = 'pi1/class.tx_damlightbox_pi1.php';
	var $extKey = 'damlightbox';
	var $pi_checkCHash = true;
	var $pi_USER_INT_obj = 0;
	var $conf;	// The TS configuration received by the class


	/**
	 * The main function initializes the flexform and calls getDamRecords to fetch the image information which will be written into $GLOBALS.
	 * Has to be called before the images are rendered, eg. with tt_content.image.15.userFunc = tx_damlightbox_pi1->main
	 *
	 * @param	string		$content
	 * @param	string		$conf
	 * @return	void
	 */
	public function main ($content, $conf) {

		// making TypoScript $conf generally available in class
		$this->conf = $conf;
		
		// get the current tablename and record uid which invoked this function call - either we are in the standard page context and a current record is set in TSFE
		if ($this->cObj->currentRecord) {
			$this->currentTable = substr($this->cObj->currentRecord, 0, strpos($this->cObj->currentRecord, ':'));
			$this->currentUid = (int) substr($this->cObj->currentRecord, strpos($this->cObj->currentRecord, ':')+1);			
		// or we are in a lightbox context (pagetype 313) and need to retrieve the current record via parameters	
		} else {
			$this->currentTable = substr(t3lib_div::_GP('content'), 0, strrpos(t3lib_div::_GP('content'), '_')); // value fully quoted below
			$this->currentUid = (int) substr(t3lib_div::_GP('content'), strrpos(t3lib_div::_GP('content'), '_')+1);
		}

		// initialize the flexform
		$flexFieldName = 'tx_damlightbox_flex';
		$this->cObj->data[$flexFieldName] = tx_damlightbox_div::getFlexFormForRecord($this->currentUid, $this->currentTable);
		$this->pi_initPIflexForm($flexFieldName);

		//clean the registers from any previous data
		$GLOBALS['TSFE']->register['tx_damlightbox'] = array();

		// get the records
		$this->getDamRecords();

		// if a result is returned, set the config registers; create them from flexform or from TS using the same notation
		if (isset($GLOBALS['TSFE']->register['tx_damlightbox']['imgCount']) && $this->cObj->data['tx_damlightbox_flex']['data']) {
			// get the sheets and values
			foreach($this->cObj->data['tx_damlightbox_flex']['data'] as $sheet => $content) {
				foreach($this->cObj->data['tx_damlightbox_flex']['data'][$sheet]['lDEF'] as $key => $value) {
					($value['vDEF']) ? $GLOBALS['TSFE']->register['tx_damlightbox']['config'][$sheet][$key] = $value['vDEF'] : $GLOBALS['TSFE']->register['tx_damlightbox']['config'][$sheet][$key] = $this->conf['config.'][''.$sheet.'.'][$key];
				}
			}
			// no flexform field is set, check if something is set from TS and if yes write the values to $GLOBALS
		} elseif (isset($GLOBALS['TSFE']->register['tx_damlightbox']['imgCount']) && !$this->cObj->data['tx_damlightbox_flex']['data'] && $this->conf['config.']) {
			foreach($this->conf['config.'] as $sheet => $content) {
				$sheet = substr($sheet,0,(strlen($sheet)-1));
				foreach($content as $key => $value) {
					$GLOBALS['TSFE']->register['tx_damlightbox']['config'][$sheet][$key] = $value;
				}
			}
		}

		// possibility to debug from TS
		if ($this->conf['debugData'] == 1) {
			debug($GLOBALS['TSFE']->register['tx_damlightbox']);
		}

		return;
	}



	/**
	 * Fetches metadata from DAM and writes it to registers into $GLOBALS['TSFE']->register.
	 *
	 * @return	void
	 */
	protected function getDamRecords() {
		
		// fetch only the fields for the DAM record
		if ($this->conf['select.']['damFields'] == '*') {
			$selectFields = 'tx_dam.*';
		} else {
			$selectFields = t3lib_div::trimExplode(',',$this->conf['select.']['damFields'],1);
			foreach ($selectFields as $i => $field) {
				$selectFields[$i] = 'tx_dam.'.$field;
			}
			$selectFields = implode(',', $selectFields);
		}

		// set the parts for the select query
		$mmTable = $this->conf['select.']['mmTable'];
		$foreignTable = $this->conf['select.']['foreignTable'];
		$whereClause = $this->cObj->stdWrap($this->conf['select.']['whereClause'], $this->conf['select.']['whereClause.']);
		$sorting = $this->conf['select.']['sorting'];
		
		// automatically try to determine which table and record we are dealing with
		if (!$mmTable || !$whereClause) {
			$foreignTable = $this->currentTable;
			$whereClause = 'AND '.$this->currentTable.'.uid='.$this->currentUid.' AND tx_dam_mm_ref.tablenames LIKE \''.$this->currentTable.'\'';
		}
		
		// include the enable fields for the table
		$whereClause .= $this->cObj->enableFields('tx_dam');

		// in debug mode store the query
		if ($this->conf['debugData'] == 1) {$GLOBALS['TYPO3_DB']->store_lastBuiltQuery = TRUE;}

		// exec select query
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
			$selectFields,
			'tx_dam',
			$mmTable,
			$foreignTable,
			$whereClause,
			'',
			$sorting,
			''
		);

		if ($this->conf['debugData'] == 1) {$GLOBALS['TSFE']->register['tx_damlightbox']['query'] = $GLOBALS['TYPO3_DB']->debug_lastBuiltQuery;}

		$i = 0;
		$tmpWidth = array();
		$tmpHeight = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

			// build some comma lists for usage from TS
			$GLOBALS['TSFE']->register['tx_damlightbox']['imgCount'] .= $i.',';
			$GLOBALS['TSFE']->register['tx_damlightbox']['damImages'] .= $row['file_name'].',';

			// id needed for each img to access with listNum from TS for the typolinks
			$GLOBALS['TSFE']->register['tx_damlightbox']['idCount'] .= $GLOBALS['TSFE']->id.',';

			// write the DAM data into the GLOBAL register 'tx_dam_imgdata' for retrival from TS
			$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$i] = $row;
			// filepath is glued here - just saves some TS
			$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$i]['fullPath'] = $row['file_path'].$row['file_name'];

			// if the width/height exceeds maxW/maxH the image will be generated - the DAM register therefore needs to be updated so that the lightbox will fit seamlessly
			if ($row['hpixels'] > $this->conf['settings.']['maxW'] || $row['vpixels'] > $this->conf['settings.']['maxH']) {
					// the new dimensions will be found out using gifbuilder
					$gifbuilder = t3lib_div::makeInstance('tslib_gifbuilder');
					$imageScale = $gifbuilder->getImageScale(array(0 => $row['hpixels'], 1 => $row['vpixels']), '', '', array('maxW' => $this->conf['settings.']['maxW'], 'maxH' => $this->conf['settings.']['maxH']));
					$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$i]['hpixels'] = $imageScale[0];
					$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$i]['vpixels'] = $imageScale[1];
			}

			// if categories are wanted, fetch them and put them in a commalist
			if ($GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$i]['category']) {
				// exec select query for DAM record info
				$subRes = $GLOBALS['TYPO3_DB']->exec_SELECT_mm_query(
					'tx_dam.uid,tx_dam_cat.title',
					'tx_dam',
					'tx_dam_mm_cat',
					'tx_dam_cat',
					'AND tx_dam_mm_cat.uid_local='.$row['uid'].'',
					'',
					'tx_dam_mm_cat.sorting',
					''
				);
				$tmpCategories = array();
				while($subRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($subRes)) {
					$tmpCategories[] = $subRow['title'];
				}
				$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$i]['category'] = implode(',', $tmpCategories);
				// free memory
				$GLOBALS['TYPO3_DB']->sql_free_result($subRes);
			}

			$i++;
		}

		// free memory
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		// remove the final commas from the lists
		$GLOBALS['TSFE']->register['tx_damlightbox']['imgCount'] = substr($GLOBALS['TSFE']->register['tx_damlightbox']['imgCount'], 0,-1);
		$GLOBALS['TSFE']->register['tx_damlightbox']['damImages'] = substr($GLOBALS['TSFE']->register['tx_damlightbox']['damImages'], 0,-1);
		$GLOBALS['TSFE']->register['tx_damlightbox']['idCount'] = substr($GLOBALS['TSFE']->register['tx_damlightbox']['idCount'], 0,-1);

		return;
	}



	/**
	 * If the preview mode is set the remaining imagelinks need to be inserted in a hidden div. Otherwise the lightbox will not be browsable and just open the preview image
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

				$uid = $this->cObj->stdWrap($conf['content'],$conf['content.']);
				$lbCaption = $GLOBALS['TSFE']->register['tx_damlightbox']['config']['sLIGHTBOX']['lbCaption'];
				$title = $GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$key][''.$lbCaption.''];

				$hCalc = t3lib_div::trimExplode('|',$this->cObj->stdWrap(null,$conf['hCalc.']));
				$vCalc = t3lib_div::trimExplode('|',$this->cObj->stdWrap(null,$conf['vCalc.']));
				$GLOBALS['TSFE']->register['widthCalc'] = intval(t3lib_div::calcParenthesis($hCalc[0].$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$key]['hpixels'].$hCalc[1]));
				$GLOBALS['TSFE']->register['heightCalc'] = intval(t3lib_div::calcParenthesis($vCalc[0].$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$key]['vpixels'].$vCalc[1]));

				// check if specific dimensions are set in the flexform
				if ($GLOBALS['TSFE']->register['tx_damlightbox']['config']['sLIGHTBOX']['setSpecificDimensions']) $this->overrideDimsFromFlexform($key,null);

				$linkConfig=array();
				$linkConfig['parameter'] = $GLOBALS['TSFE']->id;
				$linkConfig['no_cache'] = 0;
				$linkConfig['useCacheHash'] = 1;
				$linkConfig['additionalParams'] = '&type='.$conf['type'].'&content='.$uid.'&img='.$key.'';
				$linkConfig['ATagParams'] = 'title="'.$title.'" rev="width='.$GLOBALS['TSFE']->register['widthCalc'].', height='.$GLOBALS['TSFE']->register['heightCalc'].', src='.$GLOBALS['TSFE']->register['fullPath'].'" rel="lightbox[sb'.$uid.']"';
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

	
	
	/* Iterates through all images given in the damImages register. Usefull for frontend rendering in case more than one image from a damlightbox field of a given table has to be rendered.
	 * 
	 * @param	string		Content to be processed
	 * @param	array		TypoScript configuration of the user object (have a look at the static TS example for pages table)
	 * @return	string		The generated image tags
	 */
	public function frontendImageIterator($content, $conf) {
		
		// first gather the list of files
		$images = array();
		$images = t3lib_div::trimExplode(',', $GLOBALS['TSFE']->register['tx_damlightbox']['damImages'], 1);
		
		if (count($images) > 0) {

			// iterate through the images
			$GLOBALS['TSFE']->register['currentImg'] = 0;
					
			foreach ($images as $image) {
				
				// produce the image
				$content .= $this->cObj->cObjGetSingle($conf['image'], $conf['image.']);
				
				// first image is preview
				if ($GLOBALS['TSFE']->register['tx_damlightbox']['config']['sDEF']['imgPreview']) break;
				
				// raise counter
				$GLOBALS['TSFE']->register['currentImg']++;
			}
		}
		
		return $this->cObj->stdWrap($content, $conf['stdWrap.']);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_pi1.php']);
}
?>
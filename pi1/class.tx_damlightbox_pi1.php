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
 *   51: class tx_damlightbox_pi1 extends tslib_pibase
 *   69:     public function main ($content, $conf)
 *  129:     protected function getDamRecords()
 *  244:     public function frontendImageIterator($content, $conf)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

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

			// get the current tablename and record uid which invoked this function call; normally both values should be in currentRecord property like 'pages:5'
		if ($this->cObj->currentRecord) {

				// set the table name of the current record
			$this->currentTable = substr($this->cObj->currentRecord, 0, strpos($this->cObj->currentRecord, ':'));

				// if this fails, try a fallback from TS
			if ($this->currentTable == '_NO_TABLE' || $this->currentTable == '') {
				$this->currentTable = $this->cObj->stdWrap($this->conf['select.']['foreignTable'], $this->conf['select.']['foreignTable']);
			}

				// set the current uid from cObj
			$this->currentUid = (int) $this->cObj->data['uid'];

			// or we are in a lightbox context (pagetype 313) and need to retrieve the current record via parameters
		} else {
				// get table by going reverse from the last underscore - make sure its a valid table from $TCA
			$table = substr(t3lib_div::_GP('content'), 0, strrpos(t3lib_div::_GP('content'), '_'));
			if (isset($GLOBALS['TCA'][$table])) {
				$this->currentTable = $GLOBALS['TYPO3_DB']->fullQuoteStr($table, $table);
				// invalid incoming value, return
			} else {
				return;
			}
				// get uid of record by starting from last underscore - must be int
			$this->currentUid = (int) substr(t3lib_div::_GP('content'), strrpos(t3lib_div::_GP('content'), '_')+1);
		}

			// initialize the flexform
		$flexFieldName = 'tx_damlightbox_flex';
		$this->cObj->data[$flexFieldName] = tx_damlightbox_div::getFlexFormForRecord($this->currentUid, $this->currentTable);
		$this->pi_initPIflexForm($flexFieldName);

			//clean the registers from any previous data
		$GLOBALS['TSFE']->register['tx_damlightbox'] = array();

			// fetch the metadata records from DAM
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
	 * Fetches metadata from DAM and writes it to register into $GLOBALS['TSFE']->register.
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
		$foreignTable = $this->cObj->stdWrap($this->conf['select.']['foreignTable'], $this->conf['select.']['foreignTable.']);
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

				// get file usage
			if ($this->conf['select.']['damFields'] == '*' || strpos($this->conf['select.']['damFields'], 'file_usage')) {

				$fileUsage = tx_dam_db::getMediaUsageReferences($row['uid']);
				$getTables = t3lib_div::trimExplode(',', $this->conf['settings.']['fileUsage.']['getTables'], 1);

				if (is_array($fileUsage)) {
					$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$i]['file_usage'] = array();
					foreach ($fileUsage as $value) {
						if (in_array($value['tablenames'], $getTables)) {
							$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$i]['file_usage'][] = $value['tablenames'].'_'.$value['uid_foreign'];
						}
					}
					$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$i]['file_usage'] = implode(',', $GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$i]['file_usage']);
				}
			}

			$i++;
		}

			// free memory
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

			// translation handling
		(t3lib_div::_GP('L')) ? $language = (int) t3lib_div::_GP('L') : $language = (int) $this->conf['select.']['language'];

		if ($language > 0 && is_array($GLOBALS['TSFE']->register['tx_damlightbox']['metaData']) === TRUE) {

				// fetch the overlays from DB
			$i = 0;
			foreach ($GLOBALS['TSFE']->register['tx_damlightbox']['metaData'] as $key => $value) {

					// exec select query to fetch the overlay
				$language_overlay = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($selectFields, 'tx_dam', 'tx_dam.sys_language_uid='.$language.' AND l18n_parent='.$value['uid'].'', '', '', '', '');

					// if there was a query result
				if (count($language_overlay)) {

						// merge the default data with any translated data
					$translated_data = t3lib_div::array_merge_recursive_overrule($value, $language_overlay[0], 0, 1);

						// replace the register with the translated data
					$GLOBALS['TSFE']->register['tx_damlightbox']['metaData'][$i] = $translated_data;
				
				}

				$i++;

			}

		}

			// remove the final commas from the lists
		$GLOBALS['TSFE']->register['tx_damlightbox']['imgCount'] = substr($GLOBALS['TSFE']->register['tx_damlightbox']['imgCount'], 0,-1);
		$GLOBALS['TSFE']->register['tx_damlightbox']['damImages'] = substr($GLOBALS['TSFE']->register['tx_damlightbox']['damImages'], 0,-1);
		$GLOBALS['TSFE']->register['tx_damlightbox']['idCount'] = substr($GLOBALS['TSFE']->register['tx_damlightbox']['idCount'], 0,-1);

		return;
	}



	/** Iterates through all images given in the damImages register. Usefull for frontend rendering in case more than one image from a damlightbox field of a given table has to be rendered.
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
				$content .= $this->cObj->cObjGetSingle('IMAGE', $conf);

					// first image is preview
				if ($GLOBALS['TSFE']->register['tx_damlightbox']['config']['sDEF']['imgPreview']) break;

					// raise counter
				$GLOBALS['TSFE']->register['currentImg']++;
			}
		}
		return $content;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_pi1.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_pi1.php']);
}
?>
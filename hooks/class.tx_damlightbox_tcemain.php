<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Torsten Schrade (schradt@uni-mainz.de)
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   50: class tx_damlightbox_tcemain
 *   61:     function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, &$pObj)
 *   98:     function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, &$pObj)
 *  164:     function processCmdmap_postProcess($command, $table, $id, $value, $pObj)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
/**
 * Hook functions for use in tcemain
 *
 * @author		Torsten Schrade <schradt@uni-mainz.de>
 * @package 	TYPO3
 * @subpackage 	damlightbox
 */

class tx_damlightbox_tcemain {

	/**
	 * Hook function that stores incoming values from the generic damlightbox fields into the parent object for post processing.
	 * After storing values unsets the generic fields to avoid sql errors.
	 *
	 * @param	array		$incomingFieldArray: The fields coming in from TCEForm
	 * @param	string		$table: The current tablename
	 * @param	integer		$id: The uid of the current record or NEW
	 * @param	object		$pObj:The parent object
	 * @return	void
	 */
	function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, &$pObj) {

		if (tx_damlightbox_div::tableAllowedForDamlightbox($table)) {

				// keep incoming damlightbox values for post processing and unset the pseudo field
			if (array_key_exists('tx_damlightbox_flex', $incomingFieldArray)) {
				$pObj->tx_damlightbox_flex = $incomingFieldArray['tx_damlightbox_flex'];
				unset($incomingFieldArray['tx_damlightbox_flex']);
			}

				// tx_damlightbox_image exists in field array and has values
			if (empty($incomingFieldArray['tx_damlightbox_image']) == FALSE) {
				$pObj->tx_damlightbox_image = $incomingFieldArray['tx_damlightbox_image'];
				unset($incomingFieldArray['tx_damlightbox_image']);
				// tx_damlightbox_image exists but is empty (=removed relations)
			} elseif (array_key_exists('tx_damlightbox_image', $incomingFieldArray)) {
				$pObj->tx_damlightbox_image = '-1';
				unset($incomingFieldArray['tx_damlightbox_image']);
				// tx_damlightbox_image doesn't exist in field array
			} else {
				$pObj->tx_damlightbox_image = '-2';
			}
		}
	}

	/**
	 * Hook function that 
	 * 1. updates the MM relations of referenced images in the generic image field.
	 * 2. processes the damlightbox flexform values after the current record has been created/updated. Writes or updates the values for the current record in the tx_damlightbox_ds table.
	 *
	 * @param	string		$status: New or update
	 * @param	string		$table: The current tablename
	 * @param	integer		$id: THe uid of the current record
	 * @param	array		$fieldArray: The processed fields of the record
	 * @param	object		$pObj: The parent object
	 * @return	void
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, &$pObj) {

		if (tx_damlightbox_div::tableAllowedForDamlightbox($table)) {

				// MM relations for image field: Note that the field always needs to be processed to make sure that any MM relations are removed in case they were removed in the parent record (=the $incomingFieldArray had an empty value for the field)
			if ($pObj->tx_damlightbox_image !== '-2') {

					// get the config for the field
				$tcaFieldConf = $GLOBALS['TCA'][$table]['columns']['tx_damlightbox_image']['config'];

					// has the field values or should relations be removed
				($pObj->tx_damlightbox_image !== '-1') ? $valueArray = t3lib_div::trimExplode(',', $pObj->tx_damlightbox_image, 1) : $valueArray = array();

					// execute according TCEMAIN function to build/update MM relations
				$pObj->checkValue_group_select_processDBdata($valueArray, $tcaFieldConf, $id, $status, 'group', $table, 'tx_damlightbox_image');
			}

				// MM relations for flexform field
			if ($pObj->tx_damlightbox_flex) {

					// if the field is in array form, transform it to xml
				if (is_array($pObj->tx_damlightbox_flex)) {
					$flexformtools = t3lib_div::makeInstance('t3lib_flexformtools');
					$tx_damlightbox_flex = $flexformtools->flexArray2Xml($pObj->tx_damlightbox_flex, 1);
				}

				if ($status == 'update') {

						// find out if a relation already exists
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local', 'tx_damlightbox_ds', 'tablenames=\''.$table.'\' AND uid_foreign='.(int)$id.' AND deleted=0', null, null, null);

						// if yes, update the relation
					if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_damlightbox_ds', 'tablenames=\''.$table.'\' AND uid_foreign='.(int)$id.'', array('tx_damlightbox_flex' => $tx_damlightbox_flex), null);
						// create the relation
					} else {
						$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_damlightbox_ds', array('tablenames' => $table, 'uid_foreign' => (int)$id, 'tx_damlightbox_flex' => $tx_damlightbox_flex), null);
					}

						// free memory
					$GLOBALS['TYPO3_DB']->sql_free_result($res);

				} elseif ($status == 'new') {

						// get the new uid of the record
					$id = $pObj->substNEWwithIDs[$id];

						// insert relation
					$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_damlightbox_ds', array('tablenames' => $table, 'uid_foreign' => (int)$id, 'tx_damlightbox_flex' => $tx_damlightbox_flex), null);
				}
			}
		}
	}

	/**
	 * Hook function that sets
	 * 1. the MM relations for copied records
	 * 2. the relation between the current record and it's damlightbox flexform to deleted or not
	 *
	 * @param	string		$command: The current action
	 * @param	string		$table: The current tablename
	 * @param	integer		$id: Uid of the current record
	 * @param	string		$value: The value of the current cmd
	 * @param	object		$pObj: The parent object
	 * @return	void
	 */
	function processCmdmap_postProcess($command, $table, $id, $value, $pObj) {

		switch ($command) {

			case 'copy':

					// traverse the copyMappingArray, fetch values for the generic fields from the original records and then update the MM relations for the copied (=new) records
				foreach ($pObj->copyMappingArray as $copiedTable => $copiedRecords) {

						// only if the generic fields are activated for the current table
					if (tx_damlightbox_div::tableAllowedForDamlightbox($copiedTable)) {

							// traverse the sets of original ids from the copied table & get the values for the generic fields of the original records
						foreach ($copiedRecords as $origId => $copyId) {

								// MM relations for generic flexform field on copied records
							$row = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('tx_damlightbox_flex', 'tx_damlightbox_ds', 'tablenames=\''.$copiedTable.'\' AND uid_foreign='.(int)$origId.' AND deleted=0', null, null, null, null);
							if (is_array($row)) {
									// insert a new entry in the datastructure table
								$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_damlightbox_ds', array('tablenames' => $copiedTable, 'uid_foreign' => (int)$copyId, 'tx_damlightbox_flex' => $row['0']['tx_damlightbox_flex']), null);
							}

								// MM relations of generic image field on copied records
							$damDB = t3lib_div::makeInstance('tx_dam_db');
							// first find out what files are referenced in the original record - tx_damlightbox_image is used as ident field, if another field (like from dam_ttcontent) is used, nothing happens
							$damData = $damDB->getReferencedFiles($copiedTable, $origId, 'tx_damlightbox_image', $MM_table='tx_dam_mm_ref', '', array(), '', 'sorting_foreign', 1000);
							if (is_array($damData)) {

									// determine the referenced ids of the dam records
								$imgs = array_keys($damData['files']);
									// now prepare a value array for using the group_select function from TCEMain
								$valueArray = array();
								foreach ($imgs as $key => $value) {
									$valueArray[$key] = 'tx_dam_'.$value;
								}
									// load $TCA configuration for tx_damlightbox_image
								$tcaFieldConf = $GLOBALS['TCA'][$table]['columns']['tx_damlightbox_image']['config'];
									// let the group_select function do the rest
								$pObj->checkValue_group_select_processDBdata($valueArray, $tcaFieldConf, (int)$copyId, 'update', 'group', $copiedTable, 'tx_damlightbox_image');
							}
						}
					}
				}

			break;

			case 'delete':
				if (tx_damlightbox_div::tableAllowedForDamlightbox($table)) {
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_damlightbox_ds', 'tablenames=\''.$table.'\' AND uid_foreign='.(int)$id.'', array('deleted' => '1'), null);
				}
			break;

			case 'undelete':
				if (tx_damlightbox_div::tableAllowedForDamlightbox($table)) {
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_damlightbox_ds', 'tablenames=\''.$table.'\' AND uid_foreign='.(int)$id.'', array('deleted' => '0'), null);
				}
			break;
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/hooks/class.tx_damlightbox_tcemain.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/hooks/class.tx_damlightbox_tcemain.php']);
}
?>
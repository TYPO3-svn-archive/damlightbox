<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Torsten Schrade (schradt@uni-mainz.de)
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
 *   49: class tx_damlightbox_tcemain
 *   60:     function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, &$pObj)
 *   88:     function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, &$pObj)
 *  141:     function processCmdmap_postProcess($command, $table, $id, $value, $pObj)
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

require_once(t3lib_extMgm::extPath('damlightbox').'pi1/class.tx_damlightbox_div.php');
require_once(PATH_t3lib . 'class.t3lib_flexformtools.php');
require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam_db.php');

class tx_damlightbox_tcemain {
	
	/**
	 * Hook function that safes any incoming values into the parent object for post processing and unsets the pseudo fields to avoid any sql errors 
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
	 * Hook function that processes the damlightbox flexform values after the current record has been created/updated. Writes or updates the values 
	 * for the current record in the tx_damlightbox_ds table.
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
				$pObj->checkValue_group_select_processDBdata($valueArray, $tcaFieldConf, $id, $status, 'group', $table);
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
	
	/* In case this is a copy action the values of the generic fields for the current record need to be fetched and saved to the parent object for later retrieval
	 * by the processCmdmap_postProvess hook. Since copy actions trigger a copied instance of TCEMAIN field values cannot be transfered to this copied opbject.
	 * The solution is to store them here and build the MM relations after DB copy action based on the copied records id that's in the copyMappingArray.
	 * 
	 * @param	string		$command: The current action
	 * @param	string		$table: The current tablename
	 * @param	integer		$id: uid of the current record (NOT the new copied record!)
	 * @param	string		$value: The value of the current cmd
	 * @param	object		$pObj: The parent object
	 * @return	void	
	 * 
	 */	
	function processCmdmap_preProcess($command, $table, $id, $value, &$pObj) {
		
		if (tx_damlightbox_div::tableAllowedForDamlightbox($table)) {
			
			switch ($command) {
				
				case 'copy':
					
					// initialize a storage var
					$pObj->copyDamlightboxFields = array();
					
					// set the flexform field if there was one in the record that is copied
					$ds = tx_damlightbox_div::getFlexFormForRecord($id, $table);
					if ($ds) $pObj->copyDamlightboxFields['tx_damlightbox_flex'] = $ds;

					// image field
					$damDB = t3lib_div::makeInstance('tx_dam_db');
					$imgs = $damDB->getReferencedFiles($table, $id, 'tx_damlightbox_image', $MM_table='tx_dam_mm_ref', '', array(), '', 'sorting_foreign', 1000);				
					if (is_array($imgs)) $pObj->copyDamlightboxFields['tx_damlightbox_image'] = array_keys($imgs['files']);

				break;
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

		if (tx_damlightbox_div::tableAllowedForDamlightbox($table)) {

			switch ($command) {
				
				case 'copy':
													
					// MM relations for flexform field
					if ($pObj->copyDamlightboxFields['tx_damlightbox_flex']) {
						// insert relation
						$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_damlightbox_ds', array('tablenames' => $table, 'uid_foreign' => (int)$pObj->copyMappingArray[$table][$id], 'tx_damlightbox_flex' => $pObj->copyDamlightboxFields['tx_damlightbox_flex']), null);
					}
					
					// MM image relations
					if (is_array($pObj->copyDamlightboxFields['tx_damlightbox_image'])) {
						$valueArray = array();
						foreach ($pObj->copyDamlightboxFields['tx_damlightbox_image'] as $key => $value) {
							$valueArray[$key] = 'tx_dam_'.$value;
						}
						$tcaFieldConf = $GLOBALS['TCA'][$table]['columns']['tx_damlightbox_image']['config'];
						$pObj->checkValue_group_select_processDBdata($valueArray, $tcaFieldConf, (int)$pObj->copyMappingArray[$table][$id], 'update', 'group', $table);
					}
					
				break;
				
				case 'delete':
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_damlightbox_ds', 'tablenames=\''.$table.'\' AND uid_foreign='.(int)$id.'', array('deleted' => '1'), null);
				break;

				case 'undelete':
					$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_damlightbox_ds', 'tablenames=\''.$table.'\' AND uid_foreign='.(int)$id.'', array('deleted' => '0'), null);
				break;
			}
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/hooks/class.tx_damlightbox_tcemain.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/hooks/class.tx_damlightbox_tcemain.php']);
}
?>
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

			// keep incoming damlightbox values for post processing and unset the pseudo fields to avoid sql errors
			if (array_key_exists('tx_damlightbox_flex', $incomingFieldArray)) {
				$pObj->tx_damlightbox_flex = $incomingFieldArray['tx_damlightbox_flex'];
				unset($incomingFieldArray['tx_damlightbox_flex']);
			}

			if (array_key_exists('tx_damlightbox_image', $incomingFieldArray)) {
				
				// add the pseudo image field to the table TCA (necessary for correct processing of MM data)
				$GLOBALS['TCA'][$table]['columns']['tx_damlightbox_image'] = txdam_getMediaTCA('image_field', 'tx_damlightbox_image');
				
				$pObj->tx_damlightbox_image = $incomingFieldArray['tx_damlightbox_image'];
				unset($incomingFieldArray['tx_damlightbox_image']);
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

			// MM relations
			$valueArray = t3lib_div::trimExplode(',', $pObj->tx_damlightbox_image, 1);
			$tcaFieldConf = $GLOBALS['TCA'][$table]['columns']['tx_damlightbox_image']['config'];
			$pObj->checkValue_group_select_processDBdata($valueArray, $tcaFieldConf, $id, $status, 'group', $table);

			if (is_array($pObj->tx_damlightbox_flex)) {

				// transform flexform to xml
				$flexformtools = t3lib_div::makeInstance('t3lib_flexformtools');
				$tx_damlightbox_flex = $flexformtools->flexArray2Xml($pObj->tx_damlightbox_flex, 1);

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
	 * Hook function that will set the relation between the current record and it's damlightbox flexform to deleted or not depending on the current cmd.
	 *
	 * @param	string		$command: The current action
	 * @param	string		$table: The current tablename
	 * @param	integer		$id: Uid of the current record
	 * @param	[type]		$value: The value of the current cmd
	 * @param	object		$pObj: The parent object
	 * @return	void
	 */
	function processCmdmap_postProcess($command, $table, $id, $value, $pObj) {

		if (tx_damlightbox_div::tableAllowedForDamlightbox($table)) {

			switch ($command) {
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
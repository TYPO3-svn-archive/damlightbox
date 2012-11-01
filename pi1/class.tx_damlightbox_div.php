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
 * Contains miscellaneous functions for use in backend and frontend
 *
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *   47: class tx_damlightbox_div
 *   55: static function tableAllowedForDamlightbox($table)
 *   76: static function getFlexFormForRecord($uid, $table)
 *  101: static function addTableToFieldnames($table, $fields)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
/**
 *
 * @author		Torsten Schrade <schradt@uni-mainz.de>
 * @package 	TYPO3
 * @subpackage 	damlightbox
 */

final class tx_damlightbox_div {

	/**
	 * Checks if a table should include the damlightbox pseudo fields
	 *
	 * @param	string		$table: table to check
	 * @return	boolean		true if table should include the field
	 */
	static function tableAllowedForDamlightbox($table) {

			// load $TCA
		t3lib_div::loadTCA($table);

			// table is not configured at all
		if (!isset($GLOBALS['TCA'][$table])) return FALSE;

			// test if damlightbox field is allowed for the table according to damlightbox extconf
		if (strpos($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['damlightbox']['configuredTables'], $table) === FALSE) return FALSE;

		return TRUE;
	}

	/**
	 * Gets the flexform field for the current record from tx_damlightbox_ds
	 *
	 * @param	integer		$uid: The uid of the record for which to fetch tx_damlightbox_flex
	 * @param	string		$table: The tablename of the record
	 * @return	string		$ds: The XML string from the tx_damlightbox_flex field of the current record
	 */
	static function getFlexFormForRecord($uid, $table) {

			// QUERY on the flexform table to find the ds belonging the incoming uid
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_damlightbox_flex', 'tx_damlightbox_ds', 'tablenames=\''.$table.'\' AND uid_foreign='.$uid.' AND deleted=0', null, null, null);
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {

				// get and set
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
			$ds = $row[0];

		}
			// free memory
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return $ds;
	}


	/**
	 * Adds the current tablename in front of the submitted fieldnames in order to do proper SQL queries
	 *
	 * @param	string		$table: Tablename
	 * @param	string		$fields: Comma list of fields for which to add the tablename
	 * @return	string		$fieldList: Comma list of fields with appended tablename
	 */
	static function addTableToFieldnames($table, $fields) {

		$fieldList = t3lib_div::trimExplode(',', $fields);
		foreach ($fieldList as $key => $field) {
			$fieldList[$key] = $table.'.'.$field;
		}
		return implode(',', $fieldList);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_div.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_div.php']);
}
?>
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Torsten Schrade <schradt@uni-mainz.de>
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
 *
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
	 * Checks if a table should include the damlightbox field
	 *
	 * @param	string		$table: table to check
	 * @return	boolean		true if table should include the field
	 */
	static function tableAllowedForDamlightbox($table) {
			
		// table is not configured at all
		if (!isset($GLOBALS['TCA'][$table])) return FALSE;
		
		// test if damlightbox field is allowed for the table according to damlightbox extconf
		if (strpos($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['damlightbox']['allowedTables'], $table) === FALSE) return FALSE;

		return TRUE;		
	}
	
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
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_div.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_div.php']);
}
?>

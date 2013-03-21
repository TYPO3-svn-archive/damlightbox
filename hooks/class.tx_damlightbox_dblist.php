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
 *   47: class tx_damlightbox_dblist implements t3lib_localRecordListGetTableHook
 *   60:     function getDBlistQuery($table, $pageId, &$additionalWhereClause, &$selectedFieldsList, &$pObj)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
/**
 * Hook functions for use in db_list
 *
 * @author		Torsten Schrade <schradt@uni-mainz.de>
 * @package 	TYPO3
 * @subpackage 	damlightbox
 */

class tx_damlightbox_dblist implements t3lib_localRecordListGetTableHook {

	/**
	 * This hook function takes care that no mysql errors happen in the list module when the damlightbox pseudo fields are selected for display. Uses subselects to retrieve the adquate values from the relation tables.
	 * For tx_dam_mm_ref relations COUNT is used, since that seems to provide the best solution for ensuring compatibility across database engines.
	 *
	 * @param	string		$table: The current tablename
	 * @param	integer		$pageId: The current page id
	 * @param	string		$additionalWhereClause: Modified where clause for the query
	 * @param	string		$selectedFieldsList: Comma list with fieldnames for the query
	 * @param	object		$pObj: The parent object
	 * @return	void
	 */
	function getDBlistQuery($table, $pageId, &$additionalWhereClause, &$selectedFieldsList, &$pObj) {

			// case 1: tx_damlightbox_image set
		if (tx_damlightbox_div::tableAllowedForDamlightbox($table) && strpos($selectedFieldsList, 'tx_damlightbox_image') !== FALSE && strpos($selectedFieldsList, 'tx_damlightbox_flex') === FALSE) {

				// remove the field from the list
			$selectedFieldsList = str_replace('tx_damlightbox_image,', '', $selectedFieldsList);
				// and add it at the end using a subselect
			$selectedFieldsList = tx_damlightbox_div::addTableToFieldnames($table, $selectedFieldsList).', (SELECT COUNT(*) FROM tx_dam_mm_ref, tx_dam WHERE tx_dam_mm_ref.uid_foreign='.$table.'.uid AND tx_dam.uid=tx_dam_mm_ref.uid_local AND tx_dam_mm_ref.tablenames=\''.$table.'\' AND tx_dam_mm_ref.ident=\'tx_damlightbox_image\' ORDER BY tx_dam_mm_ref.sorting_foreign) AS tx_damlightbox_image';

		}

			// case 2: tx_damlightbox_flex set
		if (tx_damlightbox_div::tableAllowedForDamlightbox($table) && strpos($selectedFieldsList, 'tx_damlightbox_flex') !== FALSE && strpos($selectedFieldsList, 'tx_damlightbox_image') === FALSE) {

				// remove the field from the list
			$selectedFieldsList = str_replace('tx_damlightbox_flex,', '', $selectedFieldsList);
				// and add it at the end using a subselect
			$selectedFieldsList = tx_damlightbox_div::addTableToFieldnames($table, $selectedFieldsList).', (SELECT tx_damlightbox_flex FROM tx_damlightbox_ds WHERE tx_damlightbox_ds.uid_foreign='.$table.'.uid AND tx_damlightbox_ds.tablenames=\''.$table.'\') AS tx_damlightbox_flex';

		}

			// case 3: tx_damlightbox_image && tx_damlightbox_flex set
		if (tx_damlightbox_div::tableAllowedForDamlightbox($table) && strpos($selectedFieldsList, 'tx_damlightbox_image') !== FALSE && strpos($selectedFieldsList, 'tx_damlightbox_flex') !== FALSE) {

				// remove both fields from the list
			$selectedFieldsList = str_replace('tx_damlightbox_image,', '', $selectedFieldsList);
			$selectedFieldsList = str_replace('tx_damlightbox_flex,', '', $selectedFieldsList);	
				// and add them at the end using subselects
			$selectedFieldsList = tx_damlightbox_div::addTableToFieldnames($table, $selectedFieldsList).', (SELECT COUNT(*) FROM tx_dam_mm_ref, tx_dam WHERE tx_dam_mm_ref.uid_foreign='.$table.'.uid AND tx_dam.uid=tx_dam_mm_ref.uid_local AND tx_dam_mm_ref.tablenames=\''.$table.'\' AND tx_dam_mm_ref.ident=\'tx_damlightbox_image\' ORDER BY tx_dam_mm_ref.sorting_foreign) AS tx_damlightbox_image, (SELECT tx_damlightbox_flex FROM tx_damlightbox_ds WHERE tx_damlightbox_ds.uid_foreign='.$table.'.uid AND tx_damlightbox_ds.tablenames=\''.$table.'\') AS tx_damlightbox_flex';

		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/hooks/class.tx_damlightbox_dblist.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/hooks/class.tx_damlightbox_dblist.php']);
}
?>
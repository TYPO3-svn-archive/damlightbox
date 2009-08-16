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
 * Hook functions for tceform 
 *
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 */
/**
 * Hook functions for use in tceform
 *
 * @author	Torsten Schrade <schradt@uni-mainz.de>
 * @package 	TYPO3
 * @subpackage 	damlightbox
 */

require_once(PATH_t3lib.'class.t3lib_transferdata.php');
require_once(t3lib_extMgm::extPath('damlightbox').'pi1/class.tx_damlightbox_div.php');

class tx_damlightbox_tceform {

	function getMainFields_preProcess($table, &$row, $pObj) {
			
		// create a 'fake' damlightbox field in the tca of the incoming table
		if (tx_damlightbox_div::tableAllowedForDamlightbox($table)) {
			
			// if it's not a new record fetch the configuration from the tx_damlightbox_ds table
			if (substr($row['uid'], 0, 3) != 'NEW') $row['tx_damlightbox_flex'] = tx_damlightbox_div::getFlexFormForRecord($row['uid'], $table);

		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/hooks/class.tx_damlightbox_tceform.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/hooks/class.tx_damlightbox_tceform.php']);
}
?>
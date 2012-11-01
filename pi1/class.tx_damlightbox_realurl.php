<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Christopher Torgalson <manager@bedlamhotel.com>
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
* Autoconfiguration for usage with the extensions realurl
*
* @author Christopher Torgalson <manager@bedlamhotel.com>
* @coauthor Torsten Schrade <schradt@uni-mainz.de>
*/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   45: class tx_damlightbox_realurl
 *   54:     function addDamlightboxConfig($params, &$pObj)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('realurl', 'class.tx_realurl_advanced.php'));

class tx_damlightbox_realurl {

	/**
	 * Generates additional RealURL configuration and merges it with provided configuration
	 *
	 * @param	array						$params	Default configuration
	 * @param	tx_realurl_autoconfgen		$pObj	Parent object
	 * @return	array						Updated configuration
	 */
	function addDamlightboxConfig($params, &$pObj) {
		return array_merge_recursive($params['config'], array(
				'fileName' => array(
					'index' => array(
						'image.html' => array(
							'keyValues' => array(
								'type' => 313,
							)
						)
					)
				),
				'postVarSets' => array(
					'_DEFAULT' => array(
						'table' => array(
							array(
								'GETvar' => 'table',
							),
						),				
						'content' => array(
							array(
								'GETvar' => 'content',
							),
						),
						'picture' => array(
							array(
								'GETvar' => 'img',
							)
						),
					)
				)
			)
		);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_realurl.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/pi1/class.tx_damlightbox_realurl.php']);
}
?>
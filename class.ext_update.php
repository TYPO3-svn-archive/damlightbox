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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Class for transfering values from the old damlightbox_flex field to the
 * new tx_damlightbox_ds table
 *
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   49: class ext_update
 *   56:     public function main()
 *   84:     public function access($what = 'all')
 *   93:     public function query()
 *  153:     private function showUpdateForm()
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
/**
 * @author		Torsten Schrade <schradt@uni-mainz.de>
 * @package 	TYPO3
 * @subpackage 	damlightbox
 */
class ext_update {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	public function main() {

		// if form was submitted, perform update
		if ((int) t3lib_div::_GP('update') == 1) {

			$records2update = $this->query();

			if ($records2update) {
				$content = '<p>The database update has been performed. <strong>'.$records2update.'</strong> values have been transferred. You may now drop the tx_damlightbox_flex field from the tt_content table using the COMPARE function of the TYPO3 Install Tool.</p>';
			} else {
				$content = '<p>An error has occured. No values for the old tx_damlightbox_flex field could be fetched from tt_content. Please make sure that the field still exists in the database.</p>';
			}

		// else show update form
		} else {
			$content = $this->showUpdateForm();
		}

		return $content;

	}

	/**
	 * [Describe function...]
	 *
	 * @param	[type]		$what: ...
	 * @return	[type]		...
	 */
	public function access($what = 'all') {
		return 1;
	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	public function query() {

		// fetch values for old damlightbox field
		$updateArr = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid AS uid_foreign, deleted, tx_damlightbox_flex',
			'tt_content',
			'tx_damlightbox_flex != \'\'',
			$groupBy = '',
			$orderBy = 'uid',
			$limit = '',
			$uidIndexField = ''
		);

		if (is_array($updateArr)) {

			foreach ($updateArr as $record) {

				// does the record already exist in tx_damlightbox_ds
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid_local',
					'tx_damlightbox_ds',
					'uid_foreign = '.$record['uid_foreign'].' AND tablenames=\'tt_content\'',
					null,
					null,
					null
				);

				// then UPDATE
				if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {

					$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
						'tx_damlightbox_ds',
						'uid_foreign = '.$record['uid_foreign'].' AND tablenames=\'tt_content\'',
						array('tx_damlightbox_flex' => $record['tx_damlightbox_flex']),
						null
					);

				// else do an INSERT
				} else {

					$record['tablenames'] = 'tt_content';

					$GLOBALS['TYPO3_DB']->exec_INSERTquery(
						'tx_damlightbox_ds',
						$record,
						null
					);
				}

			}
			return count($updateArr);
		} else {
			return FALSE;
		}
	}

	/* Shows the submit form for doing the DB update
	 *
	 * return 		string		The HTML for the submit form
	 */
	private function showUpdateForm() {

			$onClick = "document.location='".t3lib_div::linkThisScript(array('update' => 1))."'; return false;";

			// start form
			$content = '
			<fieldset>
			<legend style="font-weight: bold;">Perform Database Update</legend>
			<p style="margin-bottom: 1em;">When upgrading from version 0.0.2 of damlightbox, a database update has to be performed. This script will transfer the existing damlightbox values from the tt_content table to the
			new damlightbox table. Values in tt_content are not touched, but PLEASE make sure that you backup your database before running this script anyway.</p>
			<input type="submit" value="Do the Update!" onclick="'.htmlspecialchars($onClick).'">
			</fieldset>
			';

			return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/class.ext_update.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/class.ext_update.php']);
}
?>

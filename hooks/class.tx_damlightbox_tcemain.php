<?php

require_once(t3lib_extMgm::extPath('damlightbox').'pi1/class.tx_damlightbox_div.php');
require_once(PATH_t3lib . 'class.t3lib_flexformtools.php');

class tx_damlightbox_tcemain {
	 
	function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, &$pObj) {

		if (tx_damlightbox_div::tableAllowedForDamlightbox($table) && is_array($incomingFieldArray['tx_damlightbox_flex'])) {
			
				// keep any incoming damlightbox ds for post processing
				$pObj->tx_damlightbox_flex = $incomingFieldArray['tx_damlightbox_flex'];
				
				// unset the fake field in order to avoid any sql-errors
				unset($incomingFieldArray['tx_damlightbox_flex']);
		}
	}

	function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $pObj) {
	
		if (tx_damlightbox_div::tableAllowedForDamlightbox($table) && is_array($pObj->tx_damlightbox_flex)) {
			
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
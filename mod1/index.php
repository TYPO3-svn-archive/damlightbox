<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Torsten Schrade <Torsten.Schrade@adwmainz.de>
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
 * Backend module for table configuration of damlightbox
 * 
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 */

$LANG->includeLLFile('EXT:damlightbox/mod1/locallang.xml');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.

/**
 * Configuration module for the damlightbox extension
 *
 * @author	Torsten Schrade <schradt@uni-mainz.de>
 * @package	TYPO3
 * @subpackage	damlightbox
 */
class  tx_damlightbox_module1 extends t3lib_SCbase {

		// class vars
		public $pageinfo;					// current page in BE
		public $conf = array();				// configuration for the module
		public $params = array();			// incoming parameters
		public $content;					// the content of the module
		public $selectedTables = array();	// selected backend tables for damlightbox
		public $tableConf = array();		// current configuration for the tables

		/**
		 * Initializes the Module
		 *
		 * @return	void
		 */
		public function init()	{
			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

			parent::init();
		}

		/**
		 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
		 *
		 * @return	void
		 */
		public function menuConfig()	{
			global $LANG;
			$this->MOD_MENU = Array (
				'function' => Array (
					'1' => $LANG->getLL('function1'),
				)
			);
			parent::menuConfig();
		}

		/**
		 * Prints out the module HTML
		 *
		 * @return	void
		 */
		public function printContent()	{
			$this->content.=$this->doc->endPage();
			echo $this->content;
		}

		/**
		 * Main function of the module.
		 *
		 * @return	void
		 */
		public function main()	{

			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

				// Access check!
				// The page will show only if there is a valid page and if this page may be viewed by the user
			$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
			$access = is_array($this->pageinfo) ? 1 : 0;

				// get general settings
			$this->extConf = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['damlightbox']);

				// get params
			$this->params['action'] = (int) t3lib_div::_GP('action');
			$this->params['function'] = (int) $this->MOD_SETTINGS['function'];
			$this->params['postVars'] = t3lib_div::_POST();
			$this->params['getVars'] = t3lib_div::_GET();

				// initialize doc
			$this->doc = t3lib_div::makeInstance('template');
			$this->doc->setModuleTemplate(t3lib_extMgm::extPath('damlightbox') . 'mod1/mod_template.html');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->docType = 'xhtml_trans';

			if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

					// Draw the form
				$this->doc->form = '<form action="mod.php?M=tools_txdamlightboxM1&amp;id='.$this->id.'" method="post" enctype="'.$GLOBALS["TYPO3_CONF_VARS"]["SYS"]["form_enctype"].'" name="txdamlightboxM1" id="txdamlightboxM1">';

					// JavaScript
				$this->doc->JScode = '
					<script language="javascript" type="text/javascript">
						script_ended = 0;
						function jumpToUrl(URL)	{
							document.location = URL;
						}
					</script>
				';
				$this->doc->postCode='
					<script language="javascript" type="text/javascript">
						script_ended = 1;
						if (top.fsMod) top.fsMod.recentIds["web"] = 0;
					</script>
				';

					// get module content
				$this->moduleContent();

					// fill content to marker
				$markers['CONTENT'] = $this->content;

				// no access
			} else {

					// no access message
				$markers['CONTENT'] = $GLOBALS['LANG']->getLL('errmsg.noAccess');
			}

				// Build the <body> for the module
			$this->content = $this->doc->startPage($GLOBALS['LANG']->getLL('title'));
			$markers['FUNC_MENU'] = t3lib_BEfunc::getFuncMenu($this->id, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function']);
			$this->content .= $this->doc->moduleBody($this->pageinfo, '', $markers);
			$this->content = $this->doc->insertStylesAndJS($this->content);
		}

		/**
		 * Implements the application logic and generates the module output
		 *
		 * @return	void
		 */
		protected function moduleContent()	{

			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

				// if damlightbox was alreary configured, reload the configuration
			if ($TYPO3_CONF_VARS['EXTCONF']['damlightbox']['configuredTables']) $this->damlightboxConf = unserialize($TYPO3_CONF_VARS['EXTCONF']['damlightbox']['configuredTables']);

				// check if some tables were already selected
			if ($this->params['postVars']['selectedTables'] || $this->params['postVars']['tableConf']) {
				$this->selectedTables = $this->params['postVars']['selectedTables'];
				$this->tableConf = $this->params['postVars']['tableConf'];

			} elseif (is_array($this->damlightboxConf)) {
				$this->selectedTables = array_keys($this->damlightboxConf);
				$this->tableConf = $this->damlightboxConf;
			}

				// set write action if submitted
			($this->params['postVars']['write']) ? $this->params['action'] = 2 : $this->params['action'] = 1;

				// function 1: table configuration
			if ($this->params['function'] == 1) {

				switch ($this->params['action']) {

						// detail configuration for the chosen tables
					case 1:

							// function header
						$this->content .= $this->doc->section($GLOBALS['LANG']->getLL('configAction'), $content, 0, 1);

							// table selection
						$content = $this->displayTableSelector();

						$content .= '<h3 class="uppercase">'.$GLOBALS['LANG']->getLL('detailConfig').'</h3>';

							// detailed config for each selected table
						$content .= $this->getConfigOptions();

							// button for next step
						$content .= '
						</select>
						</fieldset>
						<div style="margin-top: 1em;">
						<input type="hidden" name="action" value="1" />
						<input type="submit" name="refresh" value="'.$GLOBALS['LANG']->getLL('refreshButton').'" />
						<input type="submit" name="write" value="'.$GLOBALS['LANG']->getLL('writeButton').'" />
						</div>
						';

					break;

						// write configuration
					case 2:

							// write action
						$this->generateDamlightboxConfigFile();

							// function header
						$this->content .= $this->doc->section($GLOBALS['LANG']->getLL('writeAction'), $content, 0, 1);

							// notification
						$this->content .= '<p>'.$GLOBALS['LANG']->getLL('configWritten').'</p>';

					break;

						// standard case: show tables to chose from
					default:

							// set header
						$this->content .= $this->doc->section($GLOBALS['LANG']->getLL('defaultAction'), $content, 0, 1);

							// display table selector
						$content = $this->displayTableSelector();

							// display submit button
						$content .= '
						<div style="margin-top: 1em;">
							<input type="hidden" name="action" value="1" />
							<input type="submit" value="'.$GLOBALS['LANG']->getLL('nextButton').'" />
						</div>';
						
					break;
				}
				// if the script ends up here, something went completely wrong
			} else {
				$content = '<p>'.$GLOBALS['LANG']->getLL('errmsg.generalerror').'<p>';
			}

				// assign output
			$this->content .= $content;
		}

		/** Generates the table selector for the damlightbox configuration
		 * 
		 * @return	string		HTML string
		 */
		protected function displayTableSelector() {

			global $TCA;

				// show a select box for all reasonable BE tables; already selected tables are marked selected
			$tableSelector .= '
			<fieldset>
			<legend style="font-weight: bold;">'.$GLOBALS['LANG']->getLL('selectTable').'</legend>
			<label for="selectedTables" style="display: block; margin: 0.5em 0 1em 0;">'.$GLOBALS['LANG']->getLL('tcaTables').'</label>
			<select name="selectedTables[]" id="selectedTables" multiple="multiple" size="10">
			';
				// fetch the localized table options
			while (list($table)=each($TCA)) {
				(in_array($table, $this->selectedTables)) ? $selected = ' selected="selected"' : $selected = '';
				if (substr($table, 0, 4) == 'sys_' || substr($table, 0, 3) == 'be_' || substr($table, 0, 7) == 'static_') continue;
				$label = $GLOBALS['LANG']->sl($GLOBALS['TCA'][$table]['ctrl']['title']);
				if (!$label) $label = $table;
				$tableSelector .= '<option value="'.$table.'"'.$selected.'>'.$label.'</option>';
			}
			
			$tableSelector .= '
			</select>
			</fieldset>';
			
			return $tableSelector;
		}

		/** Generates fieldsets with detail configuration for each chosen table
		 * 
		 * @return	string		HTML string
		 */
		protected function getConfigOptions() {

			$configOptions = '';

				// walk through the chosen tables
			foreach ($this->selectedTables as $table) {

					// get the TCA
				t3lib_div::loadTCA($table);

					// process existing conf values
				if (in_array($table, $this->selectedTables)) {
					($this->tableConf[$table]['tx_damlightbox_image'] == 'on') ? $tx_damlightbox_image_checked = ' checked="ckecked"' : $tx_damlightbox_image_checked = '';
					($this->tableConf[$table]['tx_damlightbox_flex'] == 'on') ? $tx_damlightbox_flex_checked = ' checked="ckecked"' : $tx_damlightbox_flex_checked = '';					
				}

					// making sure we are dealing with an array
				if (!is_array($this->tableConf[$table]['types'])) $this->tableConf[$table]['types'] = array();

					// check if all types option was configured
				($this->tableConf[$table]['types'][0] == 'all') ? $tx_damlightbox_types_all = ' selected="selected"' : $tx_damlightbox_types_all = '';

					// build the form with localized labels
				$configOptions .= '
					<fieldset style="margin: 1em; padding: 0.5em;">
						<legend style="font-weight: bold;">'.$GLOBALS['LANG']->sl($GLOBALS['TCA'][$table]['ctrl']['title']).'</legend>
						<p style="margin-bottom: 0.8em;">
						<input type="checkbox" name ="tableConf['.$table.'][tx_damlightbox_image]" id="tx_damlightbox_flex"'.$tx_damlightbox_image_checked.' />
						<label for="tx_damlightbox_image">'.$GLOBALS['LANG']->getLL('imageFieldConf').'</label>
						</p>
						<p style="margin-bottom: 0.8em;">
						<input type="checkbox" name ="tableConf['.$table.'][tx_damlightbox_flex]" id="tx_damlightbox_flex"'.$tx_damlightbox_flex_checked.' />
						<label for="tx_damlightbox_flex">'.$GLOBALS['LANG']->getLL('flexFieldConf').'</label>
						</p>
						';
						if (is_array($GLOBALS['TCA'][$table]['types'])) {
						$configOptions .= '
						<p style="margin-bottom: 0.8em;">
						<select name ="tableConf['.$table.'][types][]" id="types" multiple="multiple" size="5" />
						<option value="all"'.$tx_damlightbox_types_all.'>'.$GLOBALS['LANG']->getLL('allTypes').'</option>
						';
						// get localized labels for the record types, exluding numeric returns (=invalid labels)
						foreach ($GLOBALS['TCA'][$table]['types'] as $key => $value) {
							$typefield = $GLOBALS['TCA'][$table]['ctrl']['type'];
							$label = $GLOBALS['LANG']->sl(t3lib_BEfunc::getLabelFromItemlist($table, $typefield, $key));
							if (!$label) $label = $key;
							if (is_numeric($label)) continue;
							(in_array($key, $this->tableConf[$table]['types']) && $this->tableConf[$table]['types'][0] != 'all') ? $tx_damlightbox_types_selected = ' selected="selected"' : $tx_damlightbox_types_selected = '';
							$configOptions .= '<option value="'.$key.'"'.$tx_damlightbox_types_selected.'>'.$label.'</option>';
							$i++;
						}
						$configOptions .= '
						</select>
						<label for="types">'.$GLOBALS['LANG']->getLL('typesConf').'</label>
						</p>
						';
						}
						$configOptions .= '
						<p style="margin-bottom: 0.8em;">
						<select name ="tableConf['.$table.'][after]" id="after" />
						';
							// get localized lables for all fields of the table
						foreach ($GLOBALS['TCA'][$table]['columns'] as $key => $value) {
							$label = rtrim($GLOBALS['LANG']->sl($GLOBALS['TCA'][$table]['columns'][$key]['label']), ':');
							if (!$label) $label = $key;
							($key == $this->tableConf[$table]['after']) ? $tx_damlightbox_after_selected = ' selected="selected"' : $tx_damlightbox_after_selected = '';							
							$configOptions .= '<option value="'.$key.'"'.$tx_damlightbox_after_selected.'>'.$label.'</option>';
						}
						$configOptions .= '
						</select>
						<label for="after">'.$GLOBALS['LANG']->getLL('afterConf').'</label>
						</p>
					</fieldset>
				';
			}

			return $configOptions;
		}

	/** Writes the table configuration to a file in typo3conf that is included by ext_tables.php. The file will be newly written each time the 
	 * write action is performed.
	 * 
	 * @return void
	 */
	protected function generateDamlightboxConfigFile() {

			// get the filepath from constant set in ext_tables
		$fileName = TX_DAMLIGHTBOX_FIELDCONF_FILE;

			// file handling - create and/or open and truncate it
		$fd = @fopen($fileName, 'w+');

		if ($fd) {

				// lock the file
			@flock($fd, LOCK_EX);

				// write the configuration into the file
			$this->generateDamlightboxFieldConf($fd);

				// release the write lock
			@flock($fd, LOCK_UN);

				// close the handle
			fclose($fd);
		}
	}

	/** Writes the $tempColumns array and appropriate calls to t3lib_extMgm for each table into the configuration file
	 * 
	 * @return void
	 */
	protected function generateDamlightboxFieldConf($fd) {

			// get the table configuration from module
		if (is_array($this->params['postVars']['tableConf'])) {

				// reset conf
			$conf = '';

				// walk through the configuration for each table and generate the call to t3lib_extmgm
			foreach ($this->params['postVars']['tableConf'] as $table => $configuration) {

					// by default both fields are included
				$fields = 'tx_damlightbox_image,tx_damlightbox_flex';

					// if only one of the fields is included, change $fields
				if ($configuration['tx_damlightbox_image'] == 'on' && $configuration['tx_damlightbox_flex'] !== 'on') {

					$fields = 'tx_damlightbox_image';

					// flexfield
				} elseif ($configuration['tx_damlightbox_flex'] == 'on' && $configuration['tx_damlightbox_image'] !== 'on') {

					$fields = 'tx_damlightbox_flex';

					// neither - the whole thing
				} elseif ($configuration['tx_damlightbox_flex'] !== 'on' && $configuration['tx_damlightbox_image'] !== 'on') {					

					continue;

				}

					// after configuration
				$after = $configuration['after'];

					// types configuration
				(!$configuration['types'] || $configuration['types']['0'] == 'all') ? $types = '' : $types = implode(',', $configuration['types']);

					// build the calls to t3lib_extMgm
				$conf .= 't3lib_extMgm::addTCAcolumns(\''.$table.'\', $tempColumns, 1);'.chr(10);
				$conf .= 't3lib_extMgm::addToAllTCAtypes(\''.$table.'\', \''.$fields.'\', \''.$types.'\', \'after:'.$after.'\');'.chr(10);
			}

				// write the contens of the file
			fwrite($fd, '<?php'.chr(10).$conf.'$GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'damlightbox\'][\'configuredTables\']=\''.serialize($this->params['postVars']['tableConf']).'\';'.chr(10).'?'.'>');
		}
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/mod1/index.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_damlightbox_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();
?>
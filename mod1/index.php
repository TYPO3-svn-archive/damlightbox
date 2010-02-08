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

// DEFAULT initialization of a module
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

$LANG->includeLLFile('EXT:damlightbox/mod1/locallang.xml');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.

// require module base
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
// require TCEmain for record management
require_once(PATH_t3lib . 'class.t3lib_tceforms.php');
require_once(PATH_t3lib . 'class.t3lib_tcemain.php');
// require class with helper functions
require_once(t3lib_extMgm::extPath('damlightbox') . 'pi1/class.tx_damlightbox_div.php');
require_once(t3lib_extMgm::extPath('dam') . 'tca_media_field.php');

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
		public $extconf=array();			// extension configuration from EM

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
					//'2' => $LANG->getLL('function2'),
				)
			);
			parent::menuConfig();
		}

		/**
		 * Create the panel of buttons for submitting the form or otherwise perform operations.
		 *
		 * @return	array		all available buttons as an assoc. array
		 */
		public function getButtons()	{
/*
			$buttons = array(
				'csh' => '',
				'record_list' => '',
				'history_page' => '',
				'shortcut' => '',
			);

			// csh for module
			$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_txxmlimportM1', '', $GLOBALS['BACK_PATH']);

			// If access to Web>List for user, then link to that module.
			if ($GLOBALS['BE_USER']->check('modules','web_list')) {
				$href = $BACK_PATH . 'db_list.php?id=' . $this->pageinfo['uid'] . '&returnUrl=' . rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI'));
				$buttons['record_list'] = '<a href="' . htmlspecialchars($href) . '">'.'<img'.t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/list.gif').' title="'.$GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.showList', 1).'" alt="" />'.'</a>';
			}

			// page history
			$buttons['history_page'] = '<a href="#" onclick="'.htmlspecialchars('jumpToUrl(\''.$BACK_PATH.'show_rechis.php?element='.rawurlencode('pages:'.$this->id).'&returnUrl='.rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI')).'#latest\');return false;').'">'.'<img'.t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/history2.gif', 'width="13" height="12"').' vspace="2" hspace="2" align="top" title="'.$GLOBALS['LANG']->sL('LLL:EXT:cms/layout/locallang.xml:recordHistory', 1).'" alt="" />'.'</a>';

			// shortcut
			if ($GLOBALS['BE_USER']->mayMakeShortcut())	{
				$buttons['shortcut'] = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
			}
*/
			return $buttons;
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

			// get general settings
			$this->extConf = unserialize($TYPO3_CONF_VARS['EXT']['extConf']['damlightbox']);

			// get params
			$this->params['action'] = (int) t3lib_div::_GP('action');
			$this->params['function'] = (int) $this->MOD_SETTINGS['function'];
			$this->params['cmd'] = (string) t3lib_div::_GP('cmd');
			$this->params['postVars'] = t3lib_div::_POST();
			$this->params['getVars'] = t3lib_div::_GET();

			// initialize doc
			$this->doc = t3lib_div::makeInstance('template');
			$this->doc->setModuleTemplate(t3lib_extMgm::extPath('damlightbox') . 'mod1/mod_template.html');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->docType = 'xhtml_trans';

			if ($BE_USER->user['admin'])	{

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

#				$docHeaderButtons = $this->getButtons();

				$this->moduleContent();
			}
/*
			} else {
				// If no access or if ID == zero
				$docHeaderButtons = array(
					'csh' => '',
					'record_list' => '',
					'history_page' => '',
					'shortcut' => '',
				);
				$this->content .= $this->doc->spacer(10);
				$this->content .= '<p>'.$GLOBALS['LANG']->getLL('errmsg.idFirst').'<p>';
			}
*/
			// compile document
			$markers['FUNC_MENU'] = t3lib_BEfunc::getFuncMenu($this->id, 'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function']);
			$markers['CONTENT'] = $this->content;

			// Build the <body> for the module
			$this->content = $this->doc->startPage($GLOBALS['LANG']->getLL('title'));
			$this->content .= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
			$this->content = $this->doc->insertStylesAndJS($this->content);
		}

		/**
		 * Implements the application logic and generates the module output
		 *
		 * @return	void
		 */
		protected function moduleContent()	{

			global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

			// function 1: table configuration
			if ($this->params['function'] == 1) {

				switch ($this->params['action']) {

					// detail configuration for the chosen tables
					case 1:

						// function header
						$this->content .= $this->doc->section($GLOBALS['LANG']->getLL('configAction'), $content, 0, 1);
						
						// if tables were chosen in step 1, get the config option for this tables
						if (is_array($this->params['postVars']['selectedTables'])) $this->content .= $this->getConfigOptions();
						
						// button for next step
						$content .= '
						</select>
						</fieldset>
						<div style="margin-top: 1em;">
						<input type="hidden" name="action" value="2" />
						<input type="submit" value="'.$GLOBALS['LANG']->getLL('writeButton').'" />
						</div>
						';

					break;
					
					// write the configuration to file
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
						
						// show a select box from which to select 
						$content .= '
						<fieldset>
						<legend style="font-weight: bold;">'.$GLOBALS['LANG']->getLL('selectTable').'</legend>
						<label for="selectedTables" style="display: block; margin: 0.5em 0 1em 0;">'.$GLOBALS['LANG']->getLL('tcaTables').'</label>
						<select name="selectedTables[]" multiple="multiple" size="10">
						';
						// fetch the localized table options
						while (list($table)=each($TCA)) {
							if (substr($table, 0, 4) == 'sys_' || substr($table, 0, 3) == 'be_' || substr($table, 0, 7) == 'static_') continue;
							$label = $GLOBALS['LANG']->sl($GLOBALS['TCA'][$table]['ctrl']['title']);
							if (!$label) $label = $table;
							$content .= '<option value="'.$table.'">'.$label.'</option>';
						}
						
						$content .= '
						</select>
						</fieldset>
						<div style="margin-top: 1em;">
						<input type="hidden" name="action" value="1" />
						<input type="submit" value="'.$GLOBALS['LANG']->getLL('nextButton').'" />
						</div>
						';
						
					break;
				}
			// if the script ends up here, something went completely wrong
			} else {
				$content = '<p>'.$GLOBALS['LANG']->getLL('errmsg.generalerror').'<p>';
			}

			// assign output
			$this->content .= $content;
		}
		
		/* Generates fieldsets with detail configuration for each chosen table
		 * 
		 * @return	string		HTML string
		 */
		protected function getConfigOptions() {
			
			$configOptions = '';

			// walk through the chosen tables
			foreach ($this->params['postVars']['selectedTables'] as $table) {
				
				// get the TCA
				t3lib_div::loadTCA($table);
				
				// build the form with localized labels
				$configOptions .= '		
					<fieldset style="margin: 1em; padding: 0.5em;">
						<legend style="font-weight: bold;">'.$GLOBALS['LANG']->sl($GLOBALS['TCA'][$table]['ctrl']['title']).'</legend>
						<p style="margin-bottom: 0.8em;">
						<input type="checkbox" name ="tableConf['.$table.'][tx_damlightbox_image]" id="tx_damlightbox_flex" checked="ckecked" />
						<label for="tx_damlightbox_image">'.$GLOBALS['LANG']->getLL('imageFieldConf').'</label>					
						</p>
						<p style="margin-bottom: 0.8em;">
						<input type="checkbox" name ="tableConf['.$table.'][tx_damlightbox_flex]" id="tx_damlightbox_flex" checked="ckecked" />
						<label for="tx_damlightbox_flex">'.$GLOBALS['LANG']->getLL('flexFieldConf').'</label>							
						</p>
						';
						if (is_array($GLOBALS['TCA'][$table]['types'])) {
						$configOptions .= '
						<p style="margin-bottom: 0.8em;">
						<select name ="tableConf['.$table.'][types][]" id="types" multiple="multiple" size="5" />
						<option value="all" selected="selected">'.$GLOBALS['LANG']->getLL('allTypes').'</option>
						';
						$i = 0;
						foreach ($GLOBALS['TCA'][$table]['types'] as $key => $value) {
							$typefield = $GLOBALS['TCA'][$table]['ctrl']['type'];
							$label = $GLOBALS['LANG']->sl($GLOBALS['TCA'][$table]['columns'][$typefield]['config']['items'][$i]['0']);
							if (!$label) $label = $key;
							$configOptions .= '<option value="'.$key.'">'.$label.'</option>';
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
						foreach ($GLOBALS['TCA'][$table]['columns'] as $key => $value) {
							$label = rtrim($GLOBALS['LANG']->sl($GLOBALS['TCA'][$table]['columns'][$key]['label']), ':');
							if (!$label) $label = $key; 
							$configOptions .= '<option value="'.$key.'">'.$label.'</option>';
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
		
	/* Writes the table configuration to a file in typo3conf that is included by ext_tables.php. The file will be newly written each time the 
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
	
	/* Writes the $tempColumns array and appropriate calls to t3lib_extMgm for each table into the configuration file
	 * 
	 * @return void
	 */
	protected function generateDamlightboxFieldConf($fd) {

		// get the table configuration from module
		if (is_array($this->params['postVars']['tableConf'])) {
			
			// tempColumns for both generic damlightbox fields
			$tempColumns = Array (
				'tx_damlightbox_flex' => Array (
					'exclude' => 1,
					'label' => 'LLL:EXT:damlightbox/locallang_db.xml:tx_damlightbox_flex',
					'config' => Array (
						'type' => 'flex',
			         	'ds' => Array (
			            	'default' => $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['damlightbox']['flexformFile'],
			        	)
			        )
				),
				'tx_damlightbox_image' => Array (
					'exclude' => 1,
					'label' => 'LLL:EXT:damlightbox/locallang_db.xml:tx_damlightbox_image',
					'config' => Array (				
						'form_type' => 'user',
						'userFunc' => 'EXT:dam/lib/class.tx_dam_tcefunc.php:&tx_dam_tceFunc->getSingleField_typeMedia',
						//'userProcessClass' => 'EXT:mmforeign/class.tx_mmforeign_tce.php:tx_mmforeign_tce',
						'type' => 'group',
						'internal_type' => 'db',
						'allowed' => 'tx_dam',
						'prepend_tname' => 1,
						'MM' => 'tx_dam_mm_ref',
						//'MM_foreign_select' => 1, // obsolete in 4.1
						'MM_opposite_field' => 'file_usage',
						'MM_match_fields' => array('ident' => 'tx_damlightbox_image'),
						'allowed_types' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],	
						'max_size' => '1000',
						'show_thumbs' => 1,
						'size' => 5,
						'maxitems' => 200,
						'minitems' => 0,
						'autoSizeMax' => 30,
					)
				),
			);
			
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
				($configuration['types']['0'] == 'all') ? $types = '' : $types = implode(',', $configuration['types']);
				
				// build the calls to t3lib_extMgm
				$conf .= 't3lib_extMgm::addTCAcolumns(\''.$table.'\', $tempColumns, 1);'.chr(10);
				$conf .= 't3lib_extMgm::addToAllTCAtypes(\''.$table.'\', \''.$fields.'\', \''.$types.'\', \'after:'.$after.'\');'.chr(10);
			}
			
			// write the contens of the file
			fwrite($fd, '<?php'.chr(10).'$tempColumns = '.var_export($tempColumns, true).';'.chr(10).$conf.'?'.'>');
		}
	}
		
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/damlightbox/mod1/index.php'])	{
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
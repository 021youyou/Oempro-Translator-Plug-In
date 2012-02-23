<?php
/**
* Language Translation Plug-In For Oempro4
* Name: Translator Plug-In
* Description: Easily translate application to any other languages by using this plug-in. (c) Octeth Ltd. All rights reserved. octeth.com - support@octeth.com
* Minimum Oempro Version: 4.0.2
*/
class octtranslator extends Plugins
{
public static $ArrayLanguage	= array();
public static $ObjectCI			= null;

// ---------------------=[ Core Plug-In Functions - Start ] {
/**
 * Constructor
 *
 * @return void
 * @author Cem Hurturk
 **/
function __construct()
	{
	// Constructor not used
	}

/**
 * Plug-in enable procedures
 *
 * @return void
 * @author Cem Hurturk
 **/
function enable_octtranslator()
	{
	// Create plugin tables - Start {
	$SQLQuery = "CREATE TABLE IF NOT EXISTS `".MYSQL_TABLE_PREFIX."octtranslator_translations` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`OemproVersion` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
	`OriginalLanguageFilePath` text COLLATE utf8_unicode_ci NOT NULL,
	`LanguageCode` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
	`TargetLanguageCode` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
	`Key1` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
	`Key2` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
	`Key3` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
	`Key4` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
	`Key5` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
	`Key6` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
	`OriginalValue` text COLLATE utf8_unicode_ci NOT NULL,
	`TranslatedValue` text COLLATE utf8_unicode_ci NOT NULL,
	`Status` enum('Pending','Translated') COLLATE utf8_unicode_ci NOT NULL,
	PRIMARY KEY (`ID`),
	KEY `Status` (`Status`),
	KEY `OemproVersion` (`OemproVersion`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;";
	Database::$Interface->ExecuteQuery($SQLQuery);
	// Create plugin tables - End }
	}

/**
 * Plug-in disable procedures
 *
 * @return void
 * @author Cem Hurturk
 **/
function disable_octtranslator()
	{
	// Delete options - Start
	Database::$Interface->RemoveOption('OctTranslator_Language');
	// Delete options - End

	// Delete all translation tables from the database - Start {
	$SQLQuery = "DROP TABLE IF EXISTS `".MYSQL_TABLE_PREFIX."octtranslator_translations`";
	Database::$Interface->ExecuteQuery($SQLQuery);
	// Delete all translation tables from the database - End }
	}

/**
 * Procedures for loading this plug-in
 *
 * @return void
 * @author Cem Hurturk
 **/
function load_octtranslator()
	{
	// Register enable/disable hooks - Start {
	parent::RegisterEnableHook('octtranslator');
	parent::RegisterDisableHook('octtranslator');
	// Register enable/disable hooks - End }

	// Hooks - Start {
	parent::RegisterMenuHook('octtranslator', 'RegisterMenuItems');
	// Hooks - End }

	// Retrieve language setting - Start {
	$Language = Database::$Interface->GetOption('OctTranslator_Language');
	if (count($Language) == 0)
		{
		Database::$Interface->SaveOption('OctTranslator_Language', 'en');
		$Language = 'en';
		}
	else
		{
		$Language = $Language[0]['OptionValue'];
		}
	// Retrieve language setting - End }

	// Language file - Start {
	if (file_exists(PLUGIN_PATH.'octtranslator/languages/'.strtolower($Language).'/'.strtolower($Language).'.inc.php') == true)
		{
		include_once(PLUGIN_PATH.'octtranslator/languages/'.strtolower($Language).'/'.strtolower($Language).'.inc.php');
		}
	else
		{
		include_once(PLUGIN_PATH.'octtranslator/languages/en/en.inc.php');
		}
	self::$ArrayLanguage = $ArrayPlugInLanguageStrings;
	unset($ArrayPlugInLanguageStrings);
	// Language file - End }
	}

/**
 * Registers menu items for the user interface
 *
 * @param string $ArrayMenuItems 
 * @return void
 * @author Cem Hurturk
 */
function RegisterMenuItems($ArrayMenuItems)
	{
	$ArrayMenuItems[] = array(
				'MenuLocation'	=> 'Admin.Settings',
				'MenuID'		=> 'Translator',
				'MenuLink'		=> Core::InterfaceAppURL().'/octtranslator/wizard/',
				'MenuTitle'		=> self::$ArrayLanguage['Screen']['0001'],
				);
	return array($ArrayMenuItems);
	}
// ---------------------=[ Core Plug-In Functions - End ] }

// ---------------------=[ Controller Functions - Start ] {
function ui_index()
	{
	if (self::_Header() == false) return;
	self::ui_wizard();	
	}
	
function ui_edit($TargetLanguageCode, $StartFrom = 0, $RPP = 50)
	{
	$PageSuccessMessage = '';
	$PageErrorMessage	= '';
	
	if (self::_Header() == false) return;

	// Return the list of translations - Start {
	$Translations = self::_ReturnExistingLanguages();
	// Return the list of translations - End }

	// EVENT: Export Translation - Start {
	if (strtolower($StartFrom) == 'export')
		{
		$ArrayEventReturn = self::_EventExportTranslation($TargetLanguageCode);
		}
	// EVENT: Export Translation - End }

	// EVENT: Delete Translation - Start {
	if (strtolower($StartFrom) == 'delete')
		{
		$ArrayEventReturn = self::_EventDeleteTranslation($TargetLanguageCode);
		if ($ArrayEventReturn[0] == true)
			{
			header('Location: '.InterfaceAppURL(true).'/octtranslator/');
			exit;
			}
		}
	// EVENT: Delete Translation - End }

	// Return the list of language strings - Start {
	$TotalLanguageStrings = self::_GetLanguageStringTotal($TargetLanguageCode);
	// Return the list of language strings - End }

	// Pagination check - Start {
	if ($StartFrom > $TotalLanguageStrings) $StartFrom = $TotalLanguageStrings - $RPP;
	if ($StartFrom < 0) $StartFrom = 0;
	// Pagination check - End }

	// Return the list of language strings - Start {
	$Return = self::_GetLanguageStrings($TargetLanguageCode, $StartFrom, $RPP);
		$LanguageStrings		= $Return[0];
	// Return the list of language strings - End }

	// EVENT: Save - Start {
	if (self::$ObjectCI->input->post('Command') == 'SaveTranslation')
		{
		$ArrayEventReturn = self::_EventSaveTranslation($LanguageStrings);
		if ($ArrayEventReturn[0] == true)
			{
			$PageSuccessMessage = $ArrayEventReturn[1];
			}
		}
	// EVENT: Save - End }

	// Interface parsing - Start {
	$ArrayViewData 	= array(
							'PageTitle'				=> ApplicationHeader::$ArrayLanguageStrings['PageTitle']['AdminPrefix'].self::$ArrayLanguage['Screen']['0012'],
							'CurrentMenuItem'		=> 'Settings',
							'PluginView'			=> '../plugins/octtranslator/views/edit.php',
							'SubSection'			=> 'Translator',
							'PluginLanguage'		=> self::$ArrayLanguage,
							'Translations'			=> $Translations,
							'Languages'				=> self::_GetAvailableLanguages(),
							'TargetLanguageCode'	=> $TargetLanguageCode,
							'LanguageStrings'		=> $LanguageStrings,
							'TotalLanguageStrings'	=> $TotalLanguageStrings,
							'ItemsPerPage'			=> $RPP,
							'TotalPages'			=> ceil($TotalLanguageStrings / $RPP),
							'CurrentPage'			=> $StartFrom / $RPP,
							'StartFrom'				=> $StartFrom,
							'PageSuccessMessage'	=> $PageSuccessMessage,
							'PageErrorMessage'		=> $PageErrorMessage,
							);
	$ArrayViewData = array_merge($ArrayViewData, InterfaceDefaultValues());
	foreach ($ArrayEventReturn as $Key=>$Value)
		{
		$ArrayViewData[$Key] = $Value;
		}

	self::$ObjectCI->render('admin/settings', $ArrayViewData);
	// Interface parsing - End }
	}

function ui_wizard()
	{
	if (self::_Header() == false) return;

	// Return the list of translations - Start {
	$Translations = self::_ReturnExistingLanguages();
	// Return the list of translations - End }

	// EVENT: Proceed with the translation - Start {
	if (self::$ObjectCI->input->post('Command') == 'Proceed')
		{
		$ArrayEventReturn = self::_EventTranslate();
		}
	// EVENT: Proceed with the translation - End }

	// Interface parsing - Start {
	$ArrayViewData 	= array(
							'PageTitle'				=> ApplicationHeader::$ArrayLanguageStrings['PageTitle']['AdminPrefix'].self::$ArrayLanguage['Screen']['0011'],
							'CurrentMenuItem'		=> 'Settings',
							'PluginView'			=> '../plugins/octtranslator/views/overview.php',
							'SubSection'			=> 'Translator',
							'PluginLanguage'		=> self::$ArrayLanguage,
							'Translations'			=> $Translations,
							'Languages'				=> self::_GetAvailableLanguages(),
							);
	$ArrayViewData = array_merge($ArrayViewData, InterfaceDefaultValues());
	foreach ($ArrayEventReturn as $Key=>$Value)
		{
		$ArrayViewData[$Key] = $Value;
		}

	self::$ObjectCI->render('admin/settings', $ArrayViewData);
	// Interface parsing - End }
	}
// ---------------------=[ Controller Functions - End ] }

// ---------------------=[ Event Functions - Start ] {
function _EventTranslate()
	{
	$TargetLanguageCode = self::$ObjectCI->input->post('TargetLanguage');

	// Field validations - Start {
	$ArrayFormRules = array(
							array
								(
								'field'		=> 'TargetLanguage',
								'label'		=> self::$ArrayLanguage['Screen']['0009'],
								'rules'		=> 'required',
								),
							);

	self::$ObjectCI->form_validation->set_rules($ArrayFormRules);
	// Field validations - End }

	// Run validation - Start {
	if (self::$ObjectCI->form_validation->run() == false)
		{
		return array(false);
		}
	// Run validation - End }

	// Return the list of translations - Start {
	$Translations = self::_ReturnExistingLanguages();
	// Return the list of translations - End }

	// Load target language files to the database - Start {
	if (isset($Translations[$TargetLanguageCode]) == false)
		{
		include(TEMPLATE_PATH.'languages/en/en.inc.php');

		foreach ($ArrayLanguageStrings as $Key1=>$Value1)
			{
			if (strtolower(gettype($Value1)) == 'array')
				{
				foreach ($Value1 as $Key2=>$Value2)
					{
					if (strtolower(gettype($Value2)) == 'array')
						{
						foreach ($Value2 as $Key3=>$Value3)
							{
							if (strtolower(gettype($Value3)) == 'array')
								{
								foreach ($Value3 as $Key4=>$Value4)
									{
									if (strtolower(gettype($Value3)) == 'array')
										{
										foreach ($Value4 as $Key5=>$Value5)
											{
											// Level 5
											$SQLQuery = "INSERT INTO ".MYSQL_TABLE_PREFIX."octtranslator_translations (ID, OemproVersion, OriginalLanguageFilePath, LanguageCode, TargetLanguageCode, Key1, Key2, Key3, Key4, Key5, Key6, OriginalValue, TranslatedValue, Status) VALUES ('', '".PRODUCT_VERSION."', '/templates/weefive/languages/en/en.inc.php', 'en', '".mysql_real_escape_string($TargetLanguageCode)."', '".mysql_real_escape_string($Key1)."', '".mysql_real_escape_string($Key2)."', '".mysql_real_escape_string($Key3)."', '".mysql_real_escape_string($Key4)."', '".mysql_real_escape_string($Key5)."', '', '".mysql_real_escape_string($Value5)."', '', 'Pending')";
											Database::$Interface->ExecuteQuery($SQLQuery);
											}
										}
									else
										{
										// Level 4
										$SQLQuery = "INSERT INTO ".MYSQL_TABLE_PREFIX."octtranslator_translations (ID, OemproVersion, OriginalLanguageFilePath, LanguageCode, TargetLanguageCode, Key1, Key2, Key3, Key4, Key5, Key6, OriginalValue, TranslatedValue, Status) VALUES ('', '".PRODUCT_VERSION."', '/templates/weefive/languages/en/en.inc.php', 'en', '".mysql_real_escape_string($TargetLanguageCode)."', '".mysql_real_escape_string($Key1)."', '".mysql_real_escape_string($Key2)."', '".mysql_real_escape_string($Key3)."', '".mysql_real_escape_string($Key4)."', '', '', '".mysql_real_escape_string($Value4)."', '', 'Pending')";
										Database::$Interface->ExecuteQuery($SQLQuery);
										}
									}
								}
							else
								{
								// Level 3
								$SQLQuery = "INSERT INTO ".MYSQL_TABLE_PREFIX."octtranslator_translations (ID, OemproVersion, OriginalLanguageFilePath, LanguageCode, TargetLanguageCode, Key1, Key2, Key3, Key4, Key5, Key6, OriginalValue, TranslatedValue, Status) VALUES ('', '".PRODUCT_VERSION."', '/templates/weefive/languages/en/en.inc.php', 'en', '".mysql_real_escape_string($TargetLanguageCode)."', '".mysql_real_escape_string($Key1)."', '".mysql_real_escape_string($Key2)."', '".mysql_real_escape_string($Key3)."', '', '', '', '".mysql_real_escape_string($Value3)."', '', 'Pending')";
								Database::$Interface->ExecuteQuery($SQLQuery);
								}
							}
						}
					else
						{
						// Level 2
						$SQLQuery = "INSERT INTO ".MYSQL_TABLE_PREFIX."octtranslator_translations (ID, OemproVersion, OriginalLanguageFilePath, LanguageCode, TargetLanguageCode, Key1, Key2, Key3, Key4, Key5, Key6, OriginalValue, TranslatedValue, Status) VALUES ('', '".PRODUCT_VERSION."', '/templates/weefive/languages/en/en.inc.php', 'en', '".mysql_real_escape_string($TargetLanguageCode)."', '".mysql_real_escape_string($Key1)."', '".mysql_real_escape_string($Key2)."', '', '', '', '', '".mysql_real_escape_string($Value2)."', '', 'Pending')";
						Database::$Interface->ExecuteQuery($SQLQuery);
						}
					}
				}
			else
				{
				// Level 1
				$SQLQuery = "INSERT INTO ".MYSQL_TABLE_PREFIX."octtranslator_translations (ID, OemproVersion, OriginalLanguageFilePath, LanguageCode, TargetLanguageCode, Key1, Key2, Key3, Key4, Key5, Key6, OriginalValue, TranslatedValue, Status) VALUES ('', '".PRODUCT_VERSION."', '/templates/weefive/languages/en/en.inc.php', 'en', '".mysql_real_escape_string($TargetLanguageCode)."', '".mysql_real_escape_string($Key1)."', '', '', '', '', '', '".mysql_real_escape_string($Value1)."', '', 'Pending')";
				Database::$Interface->ExecuteQuery($SQLQuery);
				}
			}
		}
	// Load target language files to the database - End }
	
	// Redirect to edit screen - Start {
	self::$ObjectCI->load->helper('url');
	redirect(InterfaceAppURL(true).'/octtranslator/edit/'.$TargetLanguageCode, 'location', '302');
	// Redirect to edit screen - End }
	}

function _EventSaveTranslation($LanguageStrings)
	{
	// Field validations - Start {
	$ArrayFormRules = array();
	
	foreach ($LanguageStrings as $ID=>$Value)
		{
		$ArrayFormRules[] = array
								(
								'field'		=> 'TranslationNew['.$ID.']',
								'label'		=> self::$ArrayLanguage['Screen']['0014'],
								'rules'		=> '',
								);
		}

	self::$ObjectCI->form_validation->set_rules($ArrayFormRules);
	// Field validations - End }

	// Run validation - Start {
	if (self::$ObjectCI->form_validation->run() == false)
		{
		return array(false);
		}
	// Run validation - End }


	// Save translation - Start {
	$InputNewTranslation = self::$ObjectCI->input->post('TranslationNew');
	$InputOriginalTranslation = self::$ObjectCI->input->post('TranslationOrig');

	foreach ($LanguageStrings as $ID=>$Value)
		{
		$SQLQuery = "UPDATE ".MYSQL_TABLE_PREFIX."octtranslator_translations SET `TranslatedValue`='".mysql_real_escape_string(($InputNewTranslation[$ID] != '' ? $InputNewTranslation[$ID] : ''))."' WHERE `ID`='".mysql_real_escape_string($ID)."'";
		$ResultSet = Database::$Interface->ExecuteQuery($SQLQuery);
		}
	// Save translation - End }

	return array(true, self::$ArrayLanguage['Screen']['0026']);
	}

function _EventDeleteTranslation($TargetLanguageCode)
	{
	$SQLQuery = "DELETE FROM ".MYSQL_TABLE_PREFIX."octtranslator_translations WHERE `TargetLanguageCode`='".mysql_real_escape_string($TargetLanguageCode)."'";
	$ResultSet = Database::$Interface->ExecuteQuery($SQLQuery);

	return array(true, '');	
	}

function _EventExportTranslation($TargetLanguageCode)
	{
	$LanguageStrings = self::_GetLanguageStrings($TargetLanguageCode, 0, 100000, true);
	$LanguageCodes = self::_GetAvailableLanguages();
	
	self::$ObjectCI->load->library('zip');

	$FileContent = array();
	$FileContent['Readme']		= 'Upload the '.strtolower($TargetLanguageCode).' directory into '.TEMPLATE_PATH.'languages/ directory.';
	$FileContent['Info']		= "Language Code: ".strtoupper($TargetLanguageCode)."\nLanguage Name: ".$LanguageCodes[strtolower($TargetLanguageCode)];
	$FileContent['LangFile']	= "<?php\n\n/*\n	".$LanguageCodes[strtolower($TargetLanguageCode)]." language pack for \"Oempro Weefive\" user interface template (Oempro v4.1.0+)\n*/\n\n";
	$FileContent['LangFile']	.= '$ArrayLanguageStrings = array();'."\n\n";
	
	foreach ($LanguageStrings[0] as $ID=>$Params)
		{
		$ArrayIndex = '';
		if ($Params[2]['Key1'] != '')
			{
			$ArrayIndex .= "['".$Params[2]['Key1']."']";
			}
		if ($Params[2]['Key2'] != '')
			{
			$ArrayIndex .= "['".$Params[2]['Key2']."']";
			}
		if ($Params[2]['Key3'] != '')
			{
			$ArrayIndex .= "['".$Params[2]['Key3']."']";
			}
		if ($Params[2]['Key4'] != '')
			{
			$ArrayIndex .= "['".$Params[2]['Key4']."']";
			}
		if ($Params[2]['Key5'] != '')
			{
			$ArrayIndex .= "['".$Params[2]['Key5']."']";
			}
		if ($Params[2]['Key6'] != '')
			{
			$ArrayIndex .= "['".$Params[2]['Key6']."']";
			}

		$FileContent['LangFile'] .= "\$ArrayLanguageStrings".$ArrayIndex." = '".str_replace("'", "\'", ($Params[2]['TranslatedValue'] == '' ? $Params[2]['OriginalValue'] : $Params[2]['TranslatedValue']))."';\n";
		}

	$FileContent['LangFile'] .= "\n\n?>";

	$ArchiveContents = array(
							strtolower($TargetLanguageCode).'/'.strtolower($TargetLanguageCode).'.inc.php'		=> $FileContent['LangFile'],
							strtolower($TargetLanguageCode).'/info.txt'											=> $FileContent['Info'],
							'/readme.txt'																		=> $FileContent['Readme'],
							);
	
	self::$ObjectCI->zip->add_data($ArchiveContents);
	self::$ObjectCI->zip->download('lang_'.strtolower($TargetLanguageCode).'.zip');
	self::$ObjectCI->zip->clear_data(); 
	}
// ---------------------=[ Event Functions - End ] }

// ---------------------=[ Private Functions - Start ] {
function _Header()
	{
	self::$ObjectCI =& get_instance();

	if (Plugins::IsPlugInEnabled('octtranslator') == false)
		{
		// Display error message
		$Message = ApplicationHeader::$ArrayLanguageStrings['Screen']['1707'];
		self::$ObjectCI->display_public_message($Error, $Message);
		return false;
		}

	self::_CheckAuth();
	
	return true;
	}
	
function _CheckAuth()
	{
	// Load other modules - Start
	Core::LoadObject('admin_auth');
	// Load other modules - End	

	// Check the login session, redirect based on the login session status - Start
	AdminAuth::IsLoggedIn(false, InterfaceAppURL(true).'/admin/');
	// Check the login session, redirect based on the login session status - End

	return;
	}

function _ReturnExistingLanguages()
	{
	$SQLQuery = "SELECT TargetLanguageCode FROM ".MYSQL_TABLE_PREFIX."octtranslator_translations GROUP BY TargetLanguageCode";
	$ResultSet = Database::$Interface->ExecuteQuery($SQLQuery);

	$Languages = array();
	
	if (mysql_num_rows($ResultSet) > 0)
		{
		while ($EachRow = mysql_fetch_array($ResultSet))
			{
			$Languages[$EachRow['TargetLanguageCode']] = true;
			}
		}

	return $Languages;
	}

function _GetAvailableLanguages()
	{
	asort(self::$ArrayLanguage['Screen']['0002']);

	return self::$ArrayLanguage['Screen']['0002'];
	}

function _GetLanguageStringTotal($LanguageCode)
	{
	$SQLQuery = "SELECT COUNT(*) AS TotalStrings FROM ".MYSQL_TABLE_PREFIX."octtranslator_translations WHERE TargetLanguageCode='".mysql_real_escape_string($LanguageCode)."'";
	$ResultSet = Database::$Interface->ExecuteQuery($SQLQuery);
	$TotalStrings = mysql_fetch_assoc($ResultSet);
	$TotalStrings = $TotalStrings['TotalStrings'];
	return $TotalStrings;
	}

function _GetLanguageStrings($LanguageCode, $StartFrom = 0, $RPP = 50, $ReturnDetails = false)
	{
	@ini_set('memory_limit', '256M');

	$SQLQuery = "SELECT COUNT(*) AS TotalStrings FROM ".MYSQL_TABLE_PREFIX."octtranslator_translations WHERE TargetLanguageCode='".mysql_real_escape_string($LanguageCode)."'";
	$ResultSet = Database::$Interface->ExecuteQuery($SQLQuery);
	$TotalStrings = mysql_fetch_assoc($ResultSet);
	$TotalStrings = $TotalStrings['TotalStrings'];
	
	$SQLQuery = "SELECT * FROM ".MYSQL_TABLE_PREFIX."octtranslator_translations WHERE TargetLanguageCode='".mysql_real_escape_string($LanguageCode)."' ORDER BY ID ASC LIMIT ".$StartFrom.', '.$RPP;
	$ResultSet = Database::$Interface->ExecuteQuery($SQLQuery);
	
	$LanguageStrings = array();
	
	while ($EachRow = mysql_fetch_assoc($ResultSet))
		{
		$LanguageStrings[$EachRow['ID']] = array($EachRow['OriginalValue'], $EachRow['TranslatedValue'], ($ReturnDetails == true ? $EachRow : false));
		}

	return array($LanguageStrings, $TotalStrings);
	}
// ---------------------=[ Private Functions - End ] }

} // END class octtranslator
?>
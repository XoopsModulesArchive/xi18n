<?php

define('XI_PATTERN', '/define\s*\(\s*[\'"](.*?)[\'"]\s*,\s*[^\'"]*[\'"](.*?)[\'"]\s*[^\)]*\)\s*;/is');
define('PATTERN_PHP_DEFINE', '#define\s*\([\'"](.*?)[\'"],\s*(.*?)\)\s*;#is');
define('PATTERN_PHP_STRING', '#([\'"])(.*?)(?<!\\\\)\1#s');
define('COLOR_TABLE_HEADER', '#EEEEEE');
define('COLOR_TABLE_ROW1', '#FFFFFF');
define('COLOR_TABLE_ROW2', '#CCCCCC');
define('CRLF', "<br/>\r\n");

if (! defined('XOOPS_ROOT_PATH')) exit ;
require_once XOOPS_ROOT_PATH . '/modules/xi18n/class/i18nlistfiles.php';
require_once XOOPS_ROOT_PATH . '/modules/xi18n/include/array.php';

/**
 * ListDirs()
 * 
 * @param mixed $dir
 * @param mixed $files
 * @param string $basedir
 * @return 
 **/
function ListDirs($dir, &$files, $basedir = '')
{ 
    // ListDirFiles($dir, $files, $basedir, 1, 0, 0);
    $myI18nListFiles = new I18nListFiles();
    $myI18nListFiles->ListDirs($dir, $files);
} 

/**
 * ListFiles()
 * 
 * @param mixed $dir
 * @param mixed $files
 * @param string $basedir
 * @return 
 **/
function ListFiles($dir, &$files, $basedir = '')
{ 
    // ListDirFiles($dir, $files, $basedir, 0, 1, 1);
    $myI18nListFiles = new I18nListFiles();
    $myI18nListFiles->ListFiles($dir, $files);
} 

/**
 * GetInputHiddenPost()
 * 
 * Permet de passer les paramètres d'une page à l'autre
 * On évite ainsi la gestion de session
 * 
 * @param mixed $name
 * @param mixed $arr
 * @param integer $start
 * @param string $defaut
 * @return 
 **/
function GetInputHiddenPost($name, $arr = null, $start = 1, $defaut = '')
{
    $content = '';
    if ($arr == null && $start == 1 && isset($_POST[$name]))
        $arr = $_POST[$name];
    if (is_array($arr)) {
        $i = 0;
        foreach($arr as $item) {
            $content .= GetInputHiddenPost($name . '[' . $i++ . ']', $item, 0);
        } 
    } elseif ($arr != null && $name != '') {
        $content .= "<input type='hidden' name='" . $name . "' value='" . $arr . "'/>";
    } elseif ($defaut != '') {
        $content .= "<input type='hidden' name='" . $name . "' value='" . $defaut . "'/>";
    } 
    return $content;
} 

/**
 * navbar()
 * 
 * @return 
 **/
function navbar()
{
    $step = getPost('step', 0);

    $str = "<form method='post' action='index.php' name='gotostep'>";
    $str .= GetInputHiddenPost('step');
    $str .= GetInputHiddenPost('from');
    $str .= GetInputHiddenPost('to');
    $str .= GetInputHiddenPost('modules');
    $str .= GetInputHiddenPost('files');
    $str .= GetInputHiddenPost('auto_empty', null, 1, '0');
    $str .= GetInputHiddenPost('auto_force', null, 1, '0');
    $str .= GetInputHiddenPost('auto_copy', null, 1, '0');
    $str .= GetInputHiddenPost('backup', null, 1, '0');
    $str .= "</form>";

    $str .= _MD_XI_STEPS . '&nbsp;:&nbsp;<br/>';
    $str .= '<ul>';
    $str .= '<li>' . showitem(_MD_XI_STEPS_LANGUAGES, 1, $step, getPost('from') . '->' . getPost('to')) . '</li>';
    $str .= '<li>' . showitem(_MD_XI_STEPS_MODULES, 1, $step, getPost('modules')) . '</li>';
    $str .= '<li>' . showitem(_MD_XI_STEPS_FILES, 2, $step, getPost('files')) . '</li>';
    $str .= '<li>' . showitem(_MD_XI_STEPS_TRANSLATE, 3, $step) . '</li>';
    $str .= '<li>' . showitem(_MD_XI_STEPS_FINISH, 4, $step) . '</li>';
    $str .= '</ul>';
    /*
	$str .= '<ul>';
	$str .= '<li>' . showitem(_MD_XI_STEPS_CONVERTDB, 9, $step) . '</li>';
	$str .= '</ul>';
*/
    return $str;
} 

/**
 * showitem()
 * 
 * @param mixed $text
 * @param mixed $key
 * @param mixed $step
 * @param string $value
 * @return 
 **/
function showitem($text, $key, $step, $value = '')
{
    if ($key < $step)
        $str = "<a href='#' onClick='goto_step(" . $key . ")'>" . $text . "</a>";
    else
        $str = $text;

    if (($step == $key) or ($step == 0 && $key == 1))
        $str = '<b>' . $str . '</b>';

    if ($value != '')
        $str .= "&nbsp;=&nbsp;" . $value;

    return $str;
} 

/**
 * editfile()
 * 
 * @param mixed $file
 * @return 
 **/
function editfile($file)
{
    $content = "<form method='post' action='index.php'>";
    $content = "<input type='hidden' name='file' value='" . $_POST['file'] . "'>";
    $filecontent = HttpGet($_POST['file']);
    $content = "<textarea>" . $filecontent . "</textarea>";
    $content = "</form>";
    return $content;
} 

/**
 * getPost()
 * 
 * @param mixed $name
 * @param string $defaut
 * @return 
 **/
function getPost($name, $defaut = '')
{
    if (isset($_POST[$name])) {
        if (is_array($_POST[$name])) {
            // return implode(" - ", $_POST[$name]);
            return implode_assoc_r(" ; ", " - ", $_POST[$name]);
        } else {
            return $_POST[$name];
        } 
    } else {
        return $defaut;
    } 
} 

/**
 * getLanguagedir()
 * 
 * Retourne le répertoire language du module sélectionné
 * 
 * @param mixed $module
 * @return 
 **/
function getLanguagedir($module)
{
    global $xoopsDB;
    if (!isset($module) || $module == _MD_XI_XOOPS_LANG_FILES) {
        return XOOPS_ROOT_PATH . '/language/';
    } else if ($module == _MD_XI_XOOPS_MODULES) {
        return '';
    } else if ($module == _MD_XI_XOOPS_BLOCKS) {
        return '';
    } else if ($module == _MD_XI_XOOPS_DATABASE) {
        return '';
    } else {
        return XOOPS_ROOT_PATH . '/modules/' . $module . '/language/';
    } 
} 

/**
 * getSqlSystemModule()
 * 
 * Retourne la requete necessaire pour afficher les valeurs liés a un module system
 * 
 * @param mixed $module
 * @return 
 **/
function getSqlSystemModule($module)
{
    if ($module == _MD_XI_XOOPS_MODULES) {
        return array('text' => 'name',
            'table' => 'modules',
            'id' => 'mid'
            ); 
        // $colname='name'; //mid,name
        // return 'SELECT * FROM `' . $xoopsDB->prefix('modules') . '`';
    } else if ($module == _MD_XI_XOOPS_BLOCKS) {
        return array('text' => 'title',
            'table' => 'newblocks',
            'id' => 'bid'
            ); 
        // $colname='title'; //bid,title
        // return 'SELECT * FROM `' . $xoopsDB->prefix('newblocks') . '`';
    } else if ($module == _MD_XI_XOOPS_DATABASE) {
        return array('text' => '',
            'table' => '*',
            'id' => ''
            ); 
        // $colname='title'; //bid,title
        // return 'SELECT * FROM `' . $xoopsDB->prefix('newblocks') . '`';
    } else {
        return '';
    } 
} 

/**
 * isset_post()
 * 
 * Check that all posted parameters are set
 * 
 * @param mixed $values
 * @return 
 **/
function isset_post($values)
{
    foreach ($values as $value) {
        if ($value != '' && !isset($_POST[$value]))
            return false;
    } 
    return true;
} 

/**
 * getSqlInsertValuesPost()
 * 
 * @param mixed $values
 * @return 
 **/
function getSqlInsertValuesPost($values)
{
    $sql_values = '';
    foreach ($values as $value) {
        if ($value != '') {
            if ($sql_values != '')
                $sql_values .= ', ';
            $valuepost = isset($_POST[$value])?$_POST[$value]:'';
            $sql_values .= '\'' . $valuepost . '\'';
        } else {
            $sql_values .= '\'\'';
        } 
    } 
    return $sql_values;
} 

/**
 * getSqlUpdateValuesPost()
 * 
 * @param mixed $values
 * @return 
 **/
function getSqlUpdateValuesPost($values)
{
    $sql_values = '';
    foreach ($values as $value) {
        if ($value != '') {
            if ($sql_values != '')
                $sql_values .= ', ';
            $valuepost = isset($_POST[$value])?$_POST[$value]:'';
            $sql_values .= '`' . $value . '` = \'' . $valuepost . '\'';
        } 
    } 
    return $sql_values;
} 

/**
 * getValue()
 * 
 * @param mixed $data
 * @param mixed $key
 * @param string $defaut
 * @return 
 **/
function getValue(&$data, $key, $defaut = '')
{
    return isset($data[$key])?$data[$key]:$defaut;
} 

/**
 * drawSelect()
 * 
 * Affiche une combo box avec ses valeurs
 * 
 * @param mixed $select_item
 * @param mixed $name
 * @param mixed $nameForValue
 * @param mixed $nameForText
 * @param mixed $selectedValue
 * @return 
 **/
function drawSelect($select_item, $name, $nameForValue, $nameForText, $selectedValue)
{
    $html = "<select name=" . $name . ">";
    foreach ($select_item as $select_options) {
        $html .= '<option value="' . $select_options[$nameForValue] . '"';
        if (isset($selectedValue) && $select_options[$nameForValue] == $selectedValue)
            $html .= ' selected="selected"';
        $html .= '>' . $select_options[$nameForText] . '</option>';
    } 
    $html .= '</select>';
    return $html;
} 

/**
 * drawSelectMulti()
 * 
 * Affiche plusieurs combo box avec leurs valeurs
 * 
 * @param mixed $select_items
 * @param mixed $name
 * @param mixed $nameForValue
 * @param mixed $nameForText
 * @param mixed $selectedValue
 * @param string $htmlBefore
 * @param string $htmlAfter
 * @return 
 **/
function drawSelectMulti($select_items, $name, $nameForValue, $nameForText, $selectedValue, $htmlBefore = '', $htmlAfter = '')
{
    $html = '';
    foreach ($select_items as $select_item) {
        $html .= $htmlBefore;
        $html .= drawSelect($select_item, $name, $nameForValue, $nameForText, $selectedValue);
        $html .= $htmlAfter;
    } 
    return $html;
} 

/**
 * drawInput()
 * 
 * Affiche une check box avec sa valeur
 * 
 * @param mixed $checkbox_item
 * @param mixed $name
 * @param mixed $nameForValue
 * @param mixed $nameForText
 * @param mixed $checkedValue
 * @param string $type = {checkbox|radio}
 * @return 
 **/
function drawInput($checkbox_item, $name, $nameForValue, $nameForText, $checkedValue, $type = "checkbox")
{
    $html .= '<input type="' . $type . '" name="' . $name . '" value="' . $row[$rowNameForValue] . '"';
    if (isset($checkedValue) && $row[$rowNameForValue] == $checkedValue)
        $html .= ' checked="checked"';
    if (isset($row[$rowNameForText]))
        $html .= '>' . $row[$rowNameForText] . '</input>';
    else
        $html .= '/>';
} 

/**
 * drawInputMulti()
 * 
 * Affiche une combo box avec ses valeurs
 * 
 * @param mixed $checkbox_items
 * @param mixed $name
 * @param mixed $nameForValue
 * @param mixed $nameForText
 * @param mixed $checkedValue
 * @param string $type
 * @param string $htmlBefore
 * @param string $htmlAfter
 * @return 
 **/
function drawInputMulti($checkbox_items, $name, $nameForValue, $nameForText, $checkedValue, $type = "checkbox", $htmlBefore = '', $htmlAfter = '')
{
    $html = '';
    foreach ($checkbox_items as $checkbox_item) {
        $html .= $htmlBefore;
        $html .= drawCheckbox($checkbox_item, $name, $nameForValue, $nameForText, $checkedValue, $type);
        $html .= $htmlAfter;
    } 
    return $html;
} 

/**
 * getCharsetLang()
 * 
 * @param mixed $xoopsDB
 * @param mixed $lang_title
 * @return 
 **/
function getCharsetLang(&$xoopsDB, $lang_title)
{
    return getData($xoopsDB, $lang_title, 'charset', 'ISO-8859-1');
} 

/**
 * getCodeLang()
 * 
 * @param mixed $xoopsDB
 * @param mixed $lang_title
 * @return 
 **/
function getCodeLang(&$xoopsDB, $lang_title)
{
    return getData($xoopsDB, $lang_title, 'code', 'en');
} 

function getCodeML(&$xoopsDB, $lang_title)
{
    return getData($xoopsDB, $lang_title, 'codeml', '');
} 

/**
 * getData()
 * 
 * @param mixed $xoopsDB
 * @param mixed $lang_title
 * @param mixed $parameter
 * @param string $defaut
 * @return 
 **/
function getData(&$xoopsDB, $lang_title, $parameter, $defaut = '')
{ 
    // use lang_id instead "grumble" with lang_title/dirname
    $sql = 'SELECT * FROM `' . $xoopsDB->prefix('xi18n_languages') . '` WHERE `dirname` = \'' . $lang_title . '\'';

    $res = getSqlResults($xoopsDB, $sql);
    if (isset($res[0][$parameter]))
        return $res[0][$parameter];
    else
        return $defaut;
} 

function getDatas(&$xoopsDB, $lang_title, $defaut = null)
{ 
    // use lang_id instead "grumble" with lang_title/dirname
    $sql = 'SELECT * FROM `' . $xoopsDB->prefix('xi18n_languages') . '` WHERE `dirname` = \'' . $lang_title . '\'';

    $res = getSqlResults($xoopsDB, $sql);
    if (isset($res[0]))
        return $res[0];
    else
        return $defaut;
} 

function getDatasLang(&$xoopsDB)
{ 
    // Traduction auto
	if (isset($_POST['from']) && isset($_POST['to'])) {
	    return array('from' => getDatas($xoopsDB, $_POST['from']),
        'to' => getDatas($xoopsDB, $_POST['to'])
        );
	}else{
		return null;
	}
} 

/**
 * getSqlResults()
 * 
 * @param mixed $xoopsDB
 * @param mixed $sql
 * @return 
 **/
function getSqlResults(&$xoopsDB, $sql)
{
    if (!$result = $xoopsDB->query($sql))
        return null;

    $resp = array();
    while ($row = $xoopsDB->fetchArray($result)) {
        $resp[] = $row;
    } 
    return $resp;
} 

/**
 * getSqlResult()
 * 
 * @param mixed $xoopsDB
 * @param mixed $sql
 * @return 
 **/
function getSqlResult(&$xoopsDB, $sql)
{
    $resp = null;
    if ($result = $xoopsDB->query($sql)) {
        $resp = $xoopsDB->fetchRow($result);
    } 
    return $resp;
} 

/**
 * getModuleNamePreview()
 * 
 * @param mixed $module
 * @return 
 **/
function getModuleNamePreview($module)
{
    if ($module == _MD_XI_XOOPS_LANG_FILES) {
        return "XOOPScore";
    } elseif ($module == _MD_XI_XOOPS_MODULES) {
        return "XOOPSmod";
    } elseif ($module == _MD_XI_XOOPS_BLOCKS) {
        return "XOOPSblocks";
    } else {
        return $module;
    } 
} 

/**
 * AddFileWithPreview()
 * 
 * @param mixed $file
 * @param mixed $lang
 * @param mixed $module
 * @param string $charset
 * @return 
 **/
function AddFileWithPreview($file, $lang, $module, $charset = "ISO-8859-1")
{
    return "<a href=\"javascript:openWithSelfMain('preview.php?lang=" . $lang . "&amp;charset=" . $charset . "&amp;module=" . getModuleNamePreview($module) . "&amp;file=" . urlencode($file) . "','Preview',900,700)\">" . $file . '</a>';
} 

/**
 * setStep()
 * 
 * @param mixed $value
 * @return 
 **/
function setStep($value)
{
    return '<input type="hidden" alt="step" name="step" value="' . $value . '"/>';
} 

/**
* getInputCheckbox()
* 
* @param mixed $name 
* @param mixed $text 
* @return 
*/
function getInputCheckbox($name, $text)
{
    $content = '<input type="checkbox" name="' . $name . '" value="1"';
    if (isset($_POST[$name]) && $_POST[$name] == 1)
        $content .= 'checked="checked"';
    $content .= '>' . $text . '</input>';
    return $content;
} 

/**
* 
* @access public 
* @return void 
*/
function echo2($text = '')
{
    echo $text . "<br/>\r\n";
} 

?>
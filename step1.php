<?php
/*
    * Step 1
    * 
    * Choix du module et des langues
    *
    * Input  : -
    *
    * Output : modules : Nom des modules
    */ 
// Loop on modules directory
$base = XOOPS_ROOT_PATH . '/modules';

if (is_dir($base)) {
    ListDirs($base, $modules_list);
} else {
    echo printf(_MD_XI_DIRERROR, $base);
} 
// Ajout du core
$modules_list[] = _MD_XI_XOOPS_LANG_FILES; 
// Ajout des noms des modules et des blocs
$modules_list[] = _MD_XI_XOOPS_MODULES;
$modules_list[] = _MD_XI_XOOPS_BLOCKS;

$content .= _MD_XI_ENCODING . " : " . _CHARSET . "<br/>";

$content .= setStep(2);
// Choix de la langue
$content .= _MD_XI_SESELECTLANG . '<br/>'; 
// Query on enable languages
$sql = 'SELECT * FROM `' . $xoopsDB->prefix('xi18n_languages') . '`';
$rows = getSqlResults($xoopsDB, $sql);

$lng_from = isset($_POST['from'])?$_POST['from']:'';
$lng_to = isset($_POST['to'])?$_POST['to']:'';
$content .= _MD_XI_FROM . '&nbsp;' . drawSelect($rows, 'from', 'dirname', 'lang_title', $lng_from);
$content .= '&nbsp;' . _MD_XI_TO . '&nbsp;' . drawSelect($rows, 'to', 'dirname', 'lang_title', $lng_to);
$content .= '<br/><br/>'; 
// Choix des modules
$content .= _MD_XI_SESELECTMOD . '<br/>'; 
// Sort modules name list
sort($modules_list, SORT_STRING);
$num = sizeof($modules_list);

$content .= '<table cellpadding="0" cellspacing="0">';
$content .= '<tr bgcolor="' . COLOR_TABLE_HEADER . '"><td width="100">';
$content .= '<a onClick="CheckAll(document.step_form, \'modules\');" title="' . _MD_XI_CHECKALL . '">' . _MD_XI_CHECKALL . '</a>';
$content .= '</td><td>' . _MD_XI_MODULES . '</td><td>' . _MD_XI_INSTALLED . '</td><td>' . _MD_XI_ACTIVATED . '</td></tr>';

$modules = null;
if (isset($_POST['modules'])) {
    $modules = $_POST['modules'];
} 

$im = 0;
foreach($modules_list as $module) {
    // Toggle boolean
    $bgcolor = ($im % 2)?COLOR_TABLE_ROW1:COLOR_TABLE_ROW2;
    $content .= '<tr bgcolor="' . $bgcolor . '"><td><center>';
    $content .= '<input type="checkbox" name="modules[' . $im++ . ']" value="' . $module . '"'; 
    // Select already selected items if backward
    if ($modules != null) {
        if (is_array($modules)) {
            if (!(array_search($module, $modules) === false)) {
                $content .= ' checked="checked"';
            } 
        } else {
            if ($module == $modules)
                $content .= ' checked="checked"';
        } 
    } 

    $content .= '/></td>';
    $content .= '<td>' . $module . '</center></td>'; 
    // TODO : display module state
    /*
        $bInstalled = false;
        $bActivated = false;
		$content .= '['.$bInstalled?_MD_XI_INSTALLED:_MD_XI_NOTINSTALLED.' ; '.$bActivated?_MD_XI_ACTIVATED:_MD_XI_DISACTIVATED.']';
*/
    $content .= '<td>?</td>';
    $content .= '<td>?</td>';

    $content .= '</tr>';
} 
$content .= '</table>';

$content .= '<hr/><input type="submit" value="' . _MD_XI_NEXT . '" alt="' . _MD_XI_NEXT . '">';

?>
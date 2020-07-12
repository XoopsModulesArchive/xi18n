<?php
/*
    * Step 2
    * 
    * Choix du fichier
    *
    * Input  : from     : Langue source
    *          to       : Langue cible
    *          modules  : Nom des modules
    *
    * Output : from     : Langue source
    *          to       : Langue cible
    *          modules  : Nom des modules
    *          files    : Nom des fichiers sélectionnés
    */

if (isset($_POST['modules']) && isset($_POST['from']) && isset($_POST['to'])) {
    $modules = $_POST['modules'];
    $content = setHeader(); 
    $i = 0;
    foreach($modules as $module) {
        $content .= processModule($module, $_POST['from'], $_POST['to'], $i++, $xoopsDB);
    } 
   
    $content .= '<input type="submit" value="' . _MD_XI_NEXT . '" alt="' . _MD_XI_NEXT . '">';
} else {
    // Erreur de session
    $content .= '<p>' . _MD_XI_ERRORPOST . '</p>';
} 

/**
 * setHeader()
 * 
 * @return 
 **/
function setHeader()
{
    $content = setStep(3);
    $content .= '<input type="hidden" name="from" value="' . $_POST['from'] . '"/>';
    $content .= '<input type="hidden" name="to" value="' . $_POST['to'] . '"/>';

    $content .= getInputCheckbox('auto_empty', _MD_XI_AUTOTRANSLATION_EMPTY);
    $content .= '<br/>';
    $content .= getInputCheckbox('auto_force', _MD_XI_AUTOTRANSLATION_FORCE);
    $content .= '<br/>';
    $content .= getInputCheckbox('auto_copy', _MD_XI_AUTOTRANSLATION_COPY);
    $content .= '<br/><br/>';

    $content .= _MD_XI_SESELECTFILE . '<br/>';
    $content .= '<a onClick="CheckAll(document.step_form, \'files\');" title="' . _MD_XI_CHECKALL . '">' . _MD_XI_CHECKALL . '</a>';
    $content .= '<hr/>';
    return $content;
} 

/**
 * processModule()
 * 
 * @param mixed $module
 * @param mixed $from
 * @param mixed $to
 * @param mixed $index
 * @return 
 **/
function processModule($module, $from, $to, $index, &$xoopsDB)
{ 
    //Set module id
	$content = '<input type="hidden" name="modules[' . $index . ']" value="' . $module . '"/>';
	
	// Loop on directory
    $lang_dir = getLanguagedir($module);
	$dir = $lang_dir . $from;
	$lang_files = array();
	
	$content .= '<b>'._MD_XI_MODULE . ' : ' . $module . '</b><br/>';
    $content .= _MD_XI_DIRECTORY . ' : ' . $dir . '<br/><br/>';
	
	if($lang_dir==''){
		return $content;
	}else{
		// Loop on directory
	    ListFiles($dir, $lang_files);
	}

    if (($num = sizeof($lang_files)) > 0) {
        $content .= '<table cellpadding="0" cellspacing="0">';
        $content .= '<tr bgcolor="#EEEE99"><td width="100">';
        $content .= '<a onClick="CheckAll(document.step_form, \'files[' . $index . ']\');" title="' . _MD_XI_CHECKALL . '">' . _MD_XI_CHECKALL . '</a>';
        $content .= '</td><td>' . _MD_XI_TABLE_FILES . '</td><td>' . _MD_XI_DIRECTORY . '</td><td>' . _MD_XI_TABLE_EXIST . '</td></tr>';

        $if = 0; 
        // for($i=0;$i<$num;$i++){
        foreach($lang_files as $lang_file) {
            // if(!preg_match('/(gif)|(jpg)|(tpl)|(html?)|(jpeg)|(png)|(htaccess)/i', $lang_file)){
            if (preg_match('/.*\.php\d?/i', $lang_file)) {
                $bgcolor = ($if % 2)?'#FFFFFF':'#CCCCCC';
                $the_file_from = $lang_dir . $from . '/' . $lang_file;
                $the_file_to = $lang_dir . $to . '/' . $lang_file;
                $content .= '<tr bgcolor="' . $bgcolor . '"><td>'; 
                // $content .= '<input type="radio" name="file" value="'.$lang_files[$i].'">';
                $content .= '<input type="checkbox" name="files[' . $index . '][' . $if++ . ']" value="' . $lang_file . '"';

                if (isset($_POST['files'])) {
                    $files = $_POST['files'];
                    if (is_array($files)) {
                        //TODO
	    			   if (!(array_search($lang_files[$i], $files))===FALSE) 
	        		      $content .= ' checked="checked"';
                    } else {
                        if ($lang_file == $files)
                            $content .= ' checked="checked"';
                    } 
                } 

                $content .= '/>';
                $content .= '<td>' . $lang_file . '</td>';
                $isdir = is_dir($the_file_from);
                $isexist = file_exists($the_file_to);

                $content .= '<td>' . ($isdir?_MD_XI_ISFOLDER:'-') . '</td>';
                $exist = $isdir?_MD_XI_FOLDEREXIST:_MD_XI_FILEEXIST;
                $content .= '<td>' . ($isexist?$exist:'-') . '</td>';
            } 
        } 
        $content .= '</table>';
    } else {
        // No files found
        $content .= _MD_XI_NOFILE . $module . "[" . $from . "]<br/>\r\n"; 
        // TODO: Create a default language file.
    } 
	$content .= '<hr/>';

    return $content;
} 

?>
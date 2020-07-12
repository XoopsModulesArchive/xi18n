<?php
/*
    * Step 4
    * 
    * Sauvegarde
    *
    * Input :  from     : Langue source
	*          to       : Langue cible
    *          modules  : Nom des modules
	*          files    : Nom des fichiers
	*          txt      : Traductions
	*          backup   : Sauvegarde une copie avant d'écraser
    *
	* Output : Sauvegarde+Ecriture des fichiers
    */
require_once ("include/convert.php");
require_once ("include/array.php");

$l_lang=array(); 
	
if (isset($_POST['from']) && isset($_POST['to']) && isset($_POST['modules'])) {
    //Get array filled with value //lang
	$lang=getDatasLang($xoopsDB);

    $modules = $_POST['modules'];
	$options = array('backup' => getPost('backup', '0'),
					 'overwrite_notags' => getPost('overwrite_notags', '0')
        ); 

    $im = 0;
    $itable = 0;
    foreach($modules as $module) {
        $lang_dir = getLanguagedir($module);
        if ($lang_dir == '') {
            // Module systeme : sauvegarde de la table
            if (isset($_POST['terms']))
                $terms = $_POST['terms'];
            $content .= saveTerms($xoopsDB, $module, $terms[$itable], $lang, $options);
            $itable++;
        } else {
            // Module classique : suavegarde du fichier language
            $content .= '</b>' . _MD_XI_MODULE . ' : ' . $module . '</b><br/>';
            if (isset($_POST['files']))
                $files = $_POST['files'];
            $if = 0;
            foreach($files[$im] as $file) {
                $content .= processFile($module, $file, $lang, $options);
            } 
            $im++;
        } 
        $content .= '<hr/><br/>';
    } 
} else {
    // Erreur de session
    $content .= '<p>' . _MD_XI_ERRORPOST . '</p>';
} 

/**
 * saveTerms()
 * 
 * @param mixed $xoopsDB
 * @param mixed $module
 * @param mixed $terms
 * @param mixed $lang
 * @param mixed $options
 * @return 
 **/
function saveTerms(&$xoopsDB, $module, $terms, &$lang, &$options)
{	

	$content = '</b>'._MD_XI_MODULE . ' : ' . $module . '</b><br/><br/>';
    // Sauver le tableau des valeurs dans la table
    $sysmod = getSqlSystemModule($module); 
	
	if ($sysmod['table']=='*') {
	    //Loop on every tables
		$tables=$xoopsDB->list_tables();
		//TODO
		$sysmod['id']="?";
		$sysmod['text']="?";
	}else{
    	// Get actual values
    	$table = $xoopsDB->prefix($sysmod['table']);
		$tables=array($table);
	}

	foreach($tables as $table){
	    $sql = 'SELECT * FROM `' . $table . '`';
	    $actual_terms = getSqlResults($xoopsDB, $sql);
	
	    foreach($terms as $key => $term) {
	        $actual_text = getArrayValueByKey($actual_terms, $sysmod['id'], $key, $sysmod['text']);
			if ($actual_text!=null){ 
		        // Replace actual term with with new value according to the new charset
				$term_e = change_charset($term, $charset_to, _CHARSET);
		        $new_term = updateTag($actual_text, $term_e, $lang['to']['codeml'], $options['overwrite_notags']);
				
		        $content .= "<b>".$term."</b> -> " . $newterm_e . " : ";
		        // Save
		        $sql = 'UPDATE `' . $table . '` SET ' . $sysmod['text'] . '=\'' . $newterm_e . '\' WHERE `' . $sysmod['id'] . '`=' . $key;
		        //echo $sql . "<br/>\r\n";
		        if ($result = $xoopsDB->query($sql)) {
					$content .= "<i>OK</i><br/>\r\n"; 
				}else{
					$content .= "<i>NOK!!!</i><br/>\r\n";
				}
			}
	    } 
	}
    return $content;
} 

/**
 *
 * @access public
 * @return void 
 **/
function saveIndexHtml($dir){
	if (is_writeable($dir)){
	    // Write file
		$file=$dir.'index.html';
		if (!file_exists($file)){
		    $fh = fopen($file, 'wb');
		    fwrite($fh, "<script>history.go(-1);</script>");
		    fclose($fh);
		}
	}
}


/**
* processFile()
* 
* @param mixed $module 
* @param mixed $file 
* @param mixed $from 
* @param mixed $to 
* @param mixed $backup 
* @return 
*/
function processFile($module, $file, &$lang, &$options)
{
    $content = '';
    $lang_dir = getLanguagedir($module);
    $file1 = $lang_dir . $lang['to']['dirname'] . '/' . $file;
    $file2 = XOOPS_ROOT_PATH . '/cache/' . $file;

    if (is_writeable($lang_dir)) {
        $dir1 = dirname($file1);
		//if (!file_exists($lang_dir . $to))
		//    mkdir($lang_dir . $to);
		if (!file_exists($dir1)){
            mkdir($dir1);
			saveIndexHtml($dir1);
			}
        $tgt_file = $file1;
    } else {
        $tgt_file = $file2;
    } 

    $file_from = $lang_dir . $lang['from']['dirname'] . '/' . $file;
    $source_str = file_get_contents($file_from); 
    // $translated_str=preg_replace_callback(XI_PATTERN,'lang_trans',$source_str);
	global $l_lang; 
	$l_lang=$lang; 

    $translated_str = preg_replace_callback(PATTERN_PHP_DEFINE, 'lang_trans', $source_str); 
    // Backup file
    if ($options['backup'] && file_exists($tgt_file)) {
        copy($tgt_file, $tgt_file . ".bak");
    } 
    // Write file
    $fh = fopen($tgt_file, 'wb');
    fwrite($fh, $translated_str);
    fclose($fh);

    $content .= _MD_XI_FILE_DEPLOY;
    $content .= AddFileWithPreview($file, $lang['to']['dirname'], $module);
    $content .= _MD_XI_FILE_DEPLOY_INTO . $tgt_file . "<br/>\r\n"; 

	//$content .= "<textarea cols='120' rows='20'>".change_charset($translated_str, $l_lang['to']['charset'], _CHARSET)."</textarea>";
    // $content .= '<textarea name="translation" rows="50" cols="80" style="border:1px solid #999999;font-size:10pt">'.$translated_str.'</textarea>';
    return $content;
} 

/**
* lang_trans()
* 
* @param mixed $matches 
* @return 
*/
function lang_trans($matches)
{
    global $l_lang; 
    // Encode HTML
	$value =isset($_POST[$matches[1]])?$_POST[$matches[1]]:'';
	//clean quote
	$value0=stripslashes($value);
	//escape double quote
	//$value1= preg_replace('#(?<!\)"#','\"',$value0);
	//TODO : find better regex 
	$value1= preg_replace('#"#','\"',$value0);
	
    $value_e = change_charset($value1, $l_lang['to']['charset'], _CHARSET);
//	$value_e0 = change_charset($value, $l_lang['to']['charset'], $l_lang['from']['charset']);
	//clean quote
//	$value_e = preg_replace("#\\\\'#","'",$value_e0);

/*
echo "Encoding SAVE ["._CHARSET."->".$l_lang['to']['charset']."]=".$value." -> ".$value0." -> ".$value1." -> ".$value_e.CRLF;
*/ 
    return 'define("' . $matches[1] . '", "' . $value_e . '");';
} 

?>
<?php
/*
    * Step 3
    * 
    * Traduction des textes
    *
    * Input :  from     : Langue source
    *          to       : Langue cible
    *          modules  : Nom des modules
    *          files    : Nom des fichiers
    *          auto_empty : Active la traduction automatique (optionnel)
    *          auto_force : Force la traduction automatique (optionnel)
    *
    * Output : from     : Langue source
    *          to       : Langue cible
    *          modules  : Nom des modules
    *          files    : Nom des fichiers
    *          txt      : Traductions
    */

require_once ("include/convert.php");
require_once ("include/array.php");
// PATTERN_PHP_STRING : Backreference \1 pour determiner le guillemet fermant et assertion arrière négative (negative lookbehind)
// pour eviter les slash+quotes
/*
1 - Determiner le define (pattern PATTERN_PHP_DEFINE)
2 - Dans la partie valeur [2], identifier toutes les chaines de caracteres (preg_match_all) 
    même les compositions comme  'je'.$suis." un test".$espace.'de '.$valeur
    quelque soit les quotes utilisées (pattern PATTERN_PHP_STRING)
3 - Stocker dans un tableau
*/
require_once("class/i18ntranslate.php");
// if (isset($_POST['files']) && isset($_POST['modules']) && isset($_POST['from']) && isset($_POST['to'])) {
if (isset($_POST['modules']) && isset($_POST['from']) && isset($_POST['to'])) {
	//Get array filled with value //lang
	$lang=getDatasLang($xoopsDB);
    $content = setStep(4);
    $content .= "<input type='hidden' name='from' value='" . $lang['from']['dirname'] . "'/>";
    $content .= "<input type='hidden' name='to' value='" . $lang['to']['dirname'] . "'/>"; 
    
    $myTranslation = new Translation($lang['from']['code'], $lang['to']['code']); 
    // States
    $content .= _MD_XI_ENCODING . " : " . _CHARSET . "<br/>";
    $options = array(
	    'auto_empty' => getPost('auto_empty', '0'),
        'auto_force' => getPost('auto_force', '0'),
        'auto_copy'  => getPost('auto_copy', '0')
        ); 
    // $backup = getPost('backup', '0');
    // Backup
    $content .= getInputCheckbox('backup', _MD_XI_BACKUP);
    $content .= '<br/>'; 
    // Choix du mode de ML
    $content .= getInputCheckbox('mode', _MD_XI_TRANSLATION_MODE);
    $content .= '<br/>'; 
    // Overwrite value if no tags
    $content .= getInputCheckbox('overwrite_notags', _MD_XI_OVW_NOTAG);
    $content .= '<br/>';

    $content .= _MD_XI_AUTOTRANSLATION_EMPTY . " : " . ($options['auto_empty']?_MD_XI_ACTIVATED:_MD_XI_DISACTIVATED) . "<br/>";
    $content .= _MD_XI_AUTOTRANSLATION_FORCE . " : " . ($options['auto_force']?_MD_XI_ACTIVATED:_MD_XI_DISACTIVATED) . "<br/>";
    if ($options['auto_empty'] || $options['auto_force'])
        $content .= _MD_XI_AUTOTRANSLATION_ENGINE . " : " . $myTranslation->getEngineName() . "<br/>";
    $content .= _MD_XI_AUTOTRANSLATION_COPY . " : " . ($options['auto_copy']?_MD_XI_ACTIVATED:_MD_XI_DISACTIVATED) . "<br/>"; 
    // $content .= _MD_XI_BACKUP . " : " . ($backup?_MD_XI_ACTIVATED:_MD_XI_DISACTIVATED) . "<br/>";
    $content .= "<br/><hr/>";

    if (isset($_POST['files']))
        $files = $_POST['files'];

    $modules = $_POST['modules'];

    $content .= '<input type="submit" value="' . _MD_XI_NEXT . '" alt="' . _MD_XI_NEXT . '"><br/>'; 
    // $im = 0;
    foreach($modules as $im => $module) {
        $content .= '</b>' . _MD_XI_MODULE . ' : ' . $module . '</b><br/>';
        $content .= "<input type='hidden' name='modules[" . $im . "]' value='" . $module . "'/>";

        $lang_dir = getLanguagedir($module);

        if ($lang_dir == '')
            $files[$im] = array('');

        $if = 0;
        if (!isset($files[$im]) || sizeof($files[$im]) == 0) {
            $content .= _MD_XI_NOSELFILE . "<br/>\r\n<hr/>";
        } else {
            foreach($files[$im] as $file) {
                if ($file != '') {
                    $content .= _MD_XI_FILE_SOURCE . ' : ';
                    $content .= AddFileWithPreview($file, $from, $module, $lang['from']['charset']);
                    $content .= '<br/>';

                    $content .= _MD_XI_FILE_TARGET . ' : ';
                    $content .= AddFileWithPreview($file, $to, $module, $lang['to']['charset']);
                    $content .= '<br/>';

                    $content .= "<input type='hidden' name='files[" . $im . "][" . $if++ . "]' value='" . $file . "'/>";
                } 
                $content .= processFile($module, $im, $file,
                    $xoopsDB, $myTranslation,
                    $lang, $options);
            } 
            // $im++;
            $content .= '<br/>';
        } 
    } 
    $content .= '<input type="submit" value="' . _MD_XI_NEXT . '" alt="' . _MD_XI_NEXT . '">';
} else {
    // Erreur de session
    $content = '<p>' . _MD_XI_ERRORPOST . '</p>';
} 

/**
 * processFile()
 * 
 * @param mixed $module
 * @param mixed $im
 * @param mixed $file
 * @param mixed $xoopsDB
 * @param mixed $myTranslation
 * @param mixed $lang
 * @param mixed $options
 * @return 
 **/
function processFile($module, $im, $file, &$xoopsDB, &$myTranslation, &$lang, &$options)
{
    $content = '';
    $lang_dir = getLanguagedir($module);
    $file_from = $lang_dir . $lang['from']['dirname'] . '/' . $file;
    $file_to = $lang_dir . $lang['to']['dirname'] . '/' . $file;
	$lang['to']['charset1'] = $lang['to']['charset'];
	$lang['from']['charset1'] = $lang['from']['charset'];

    if ($lang_dir != '' && is_dir($file_to)) {
        // Erreur
        $content .= 'Fichier source est un répertoire :' . $file_from . '<br/>';
    } else if ($lang_dir != '' && is_dir($file_from)) {
        // Erreur
        $content .= 'Fichier destination est un répertoire :' . $file_to . '<br/>';
    } else {
        if ($lang_dir == '') {
            // Si module systeme alors recherche dans la DB
            $sysmod = getSqlSystemModule($module);
            $sql = 'SELECT * FROM `' . $xoopsDB->prefix($sysmod['table']) . '`';
            $rows = getSqlResults($xoopsDB, $sql);
            $lang_src = ExtractTags($rows, $lang['from']['codeml'], $sysmod['text'], $im, $sysmod['id']);

			if ($options['auto_copy']) {
                $lang_tgt = $lang_src; 
                $lang['to']['charset'] = $lang['from']['charset']; //Le charset devient donc celui du from
            } else {
                $lang_tgt = ExtractTags($rows, $lang['to']['codeml'], $sysmod['text'], $im, $sysmod['id']);
            } 
        } else {
            // extraire du fichier source les chaines de caracteres par clé
            if (is_file($file_from)) {
                ExecuteRegex($file_from, $lang_src);
            } else {
                $content .= _MD_XI_FILENOTEXIST . $file_from . "<br/>";
                return $content;
            } 

            if ($options['auto_copy']) {
                // Recopier les valeurs source sur la cible
                $lang_tgt = $lang_src;
                $lang['to']['charset'] = $lang['from']['charset']; //Le charset devient donc celui du from
            } else {
                if (file_exists($file_to)) {
                    // Le fichier cible existe déjà ; extraire les chaines de caracteres par clé
                    ExecuteRegex($file_to, $lang_tgt); 
                    // da($lang_tgt, 'lang_tgt');
                } else {
                    // $file_to doesn't exist
                    $lang_auto = array(); 
                    // Copy source into target
                    $lang_tgt = $lang_src;
                    arrayfill($lang_tgt, '');
                } 
            } 
        } 
        // Try to complete empty fields with auto-translation
        if (!$options['auto_empty']) {
            // $content .= '<p>'._MD_XI_AUTO_NOTUSED.'</p>';
        } else if ($myTranslation->isTranslationEnabled()) {
            // Traduction automatique
            // +$lang_auto ??
            DoTranslation($myTranslation, $lang_src, $lang_tgt, $options);
        } else {
            $content .= '<p>' . _MD_XI_AUTO_UNAVAILABLE . '</p>';
        } 

        $content .= displayTable($lang_src, $lang_tgt, $lang, $options);
        $content .= '<hr/>';
    } 
    return $content;
} 

/**
* DoTranslation()
* 
* @param mixed $myTranslation 
* @param mixed $lang_src 
* @param mixed $lang_tgt 
* @param mixed $options ['auto_force']
* @return 
*/
function DoTranslation(&$myTranslation, &$lang_src, &$lang_tgt, &$options)
{ 
    // Prepare the items to translate (empty items in target)
    $txt_to_translate = array();
    foreach ($lang_src as $key => $src_text) {
        if (!isset($lang_tgt[$key])) {
            // Non existing item ; create it based on src
            $txt_to_translate[$key] = $src_text;
        } else {
            if (is_array($lang_src[$key])) {
                // clone the struct from the tgt and complete with leaving src
                foreach($lang_src[$key] as $src_key => $src_value) {
                    if (!$options['auto_force'] && isset($lang_tgt[$key][$src_key]) && $lang_tgt[$key][$src_key] != '') {
                        // Keep from original target data
                        $tr_value = $lang_tgt[$key][$src_key];
                    } else {
                        // Copy from src data
                        $tr_value = $src_value;
                    } 
                    $txt_to_translate[$key][] = $tr_value;
                } 
            } elseif ($lang_tgt[$key] == '') {
                $txt_to_translate[$key] = $src_text;
            } 
        } 
    } 
    // da($txt_to_translate, 'txt_to_translate');
    // Go Translate
    // $text=implode('|', $txt_to_translate);
    $text = implode_assoc_r('', '|', $txt_to_translate); 
    // $text= UnescapeText($text);
    // $content .= _MD_XI_AUTOTRANSLATION.$myTranslation->getEngineName()."<br/>";
    // Encoding for input
    $charset_result_from = $myTranslation->getInputEncoding();
    $text_e = change_charset($text, $charset_result_from, $lang['from']['charset']);
    /*
echo "Encoding INPUT [".$lang['from']['charset']."->".$charset_result_from."]\r\n<br/>";
echo $text."->".$text_e."\r\n<br/>";
*/
    $txt_translated = $myTranslation->translate($text_e);

    /*
echo "Before:  ".$text_e."\r\n<br/>";
echo "After:  ".$txt_translated."\r\n<br/>";
*/ 
    // Encoding for output
    $charset_result_to = $myTranslation->getOutputEncoding();
    $txt_translated_e = change_charset($txt_translated, $lang['to']['charset'], $charset_result_to);
    /*
echo "Encoding OUTPUT [".$charset_result_to."->".$lang['to']['charset']."]\r\n<br/>";
echo $txt_translated."->".$txt_translated_e."\r\n<br/>";
*/ 
    // Get the array
    $translations = explode('|', $txt_translated_e);
    $count = sizeof($translations); 
    // da($translations, 'translations');
    // Re-associate with keys
    // fill datas from $translations into $txt_to_translate (if empty item of $lang_tgt)
    deflatArray($txt_to_translate, $translations, $lang_tgt, $options['auto_force']); 
    // da($txt_to_translate, 'txt_to_translate2');
    // Fill exisiting data on a side to the other side using key based on the structure
    arrayMerge($lang_tgt, $txt_to_translate); 
    // da($lang_tgt, 'lang_tgt');
    // da($lang_src, 'lang_src');
} 

/**
* displayTable()
* 
* @param mixed $lang_src 
* @param mixed $lang_tgt 
* @param mixed $from 
* @param mixed $to 
* @param mixed $lang ['from']['code']
* @param mixed $lang ['to']['code']
* @param mixed $lang ['from']['charset']
* @param mixed $lang ['to']['charset']
* @param mixed $options ['auto_force']
* @return 
*/
function displayTable(&$lang_src, &$lang_tgt, &$lang, &$options)
{
    // $content .= "<table><tr><td>Key</td><td>".$_POST['from']."</td><td>".$_POST['to']."</td></tr>";
    $content = "<table><tr bgcolor='" . COLOR_TABLE_HEADER . "'>";
    $content .= "<td>&nbsp;</td>"; //Clé
    $content .= "<td width='40%'><b>" . $lang['from']['dirname'] . "[" . $lang['from']['code'] . " / " . $lang['from']['codeml'] . " - " . $lang['from']['charset1'] . "]</b></td>"; //Source
    $content .= "<td width='60%'><b>" . $lang['to']['dirname'] . "[" . $lang['to']['code'] . " / " . $lang['to']['codeml'] . " - " . $lang['to']['charset1'] . "]</b></td>"; //Target
    $content .= "<td>&nbsp;</td>"; //Trad Auto 
    $content .= "</tr>";
    $index = 1;
    foreach($lang_src as $key_src => $value_src) {
        $bgcolor = ($index % 2)?COLOR_TABLE_ROW1:COLOR_TABLE_ROW2;
        $index++; 
        // Create a array in all case
        if (is_array($lang_tgt[$key_src]))
            $values = $lang_tgt[$key_src];
        else
            $values = array($key_src => $lang_tgt[$key_src]); 
        // Draw lines for a specified key
        foreach($values as $key => $value) {
            if (is_array($value_src))
                $txt_src = isset($value_src[$key])?$value_src[$key]:"??";
            else
                $txt_src = $value_src; 
            // Text source
			$txt_src=stripslashes($txt_src);
            $txt_src_e = change_charset($txt_src, _CHARSET, $lang['from']['charset']);
            $source = nl2br(htmlspecialchars($txt_src_e)); 
			

			$value_strip=stripslashes($value);
            // Use current encoding _CHARSET for right display
            $target = change_charset($value_strip, _CHARSET, $lang['to']['charset']);
/*
echo "Encoding DISPLAY [" . $lang['to']['charset'] . "->" . _CHARSET . "]=" . $value . " -> " . $target . CRLF;
*/
            if ($key_src == $key)
                $input_name = $key_src;
            else
                $input_name = $key_src . '#' . $key; 
            // Ligne
            $content .= '<tr bgcolor="' . $bgcolor . '">'; 
            // Clé
            $content .= '<td><a href="#" alt="' . $input_name . '" title="' . $input_name . '">-</a></td>'; 
            // Source
            // !!! Warning on htmlentities with UTF8 !!!
            $content .= '<td>' . $source . '</td><td>';

            if (substr_count($target, chr(13) . chr(10)) || strlen($target) > 80) {
                $rows = (strlen($target) / 80) + 2;
                $content .= '<textarea name="' . $input_name . '" rows="' . $rows . '" cols="80" style="border:1px solid #999999;font-size:10pt">' . $target . '</textarea>';
            } else {
                $content .= '<input size="90" type="text" name="' . $input_name . '" value="' . $target . '" style="border:1px solid #999999">';
            } 
            $content .= "</td><td>";
            if ($options['auto_force'] or (isset($lang_auto[$key]) && $lang_auto[$key]))
                $content .= '<a href="#" alt="' . _MD_XI_AUTOTRANSLATED . '" title="' . _MD_XI_AUTOTRANSLATED . '">*</a>';
            $content .= "</td></tr>\r\n";
        } 
    } 
    $content .= '</table>';
    return $content;
} 

/**
* ExecuteRegex()
* 
* @param mixed $file 
* @param mixed $arr 
* @return 
*/
function ExecuteRegex($file, &$arr)
{
    $str1 = file_get_contents($file); 
    // Lister les define
    preg_match_all(PATTERN_PHP_DEFINE, $str1, $defines);
    $i = 0;
    foreach ($defines[2] as $constants) {
        $key = $defines[1][$i++];
        preg_match_all(PATTERN_PHP_STRING, $constants, $strings);
        foreach ($strings[2] as $string) {
            $arr[$key][] = $string;
        } 
    } 
} 

/**
* ExtractTags()
* 
* @param mixed $items 
* @param mixed $lang 
* @param mixed $colname 
* @param mixed $mid 
* @return 
*/
function ExtractTags($items, $lang, $colname, $mid, $id_name)
{
    $result = array();
    $pattern = "#\[" . $lang . "\](.*?)\[/" . $lang . "\]#s";
    foreach ($items as $key => $item) {
        // $keyr= 'terms['.$mid.']['.$key.']';
        $keyr = 'terms[' . $mid . '][' . $item[$id_name] . ']';
        preg_match_all($pattern, $item[$colname], $strings);
        /*Revoir les cas suivants : 
		* Pas de balises
		* Plusieurs fois la meme balise
		* */
        if (isset($strings[1][0])) {
            $result[$keyr] = $strings[1][0];
        } else {
            // si pas de balise
            $pattern2 = "#\[(\w+)].*?\[/\\1\]#s";
            $replacement2 = '';
            $result[$keyr] = preg_replace($pattern2, $replacement2, $item[$colname]);
        } 
    } 
    return $result;
} 

?>
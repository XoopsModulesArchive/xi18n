<?php 
define('PATTERN_MLTAGS', "#\[(\w+)].*?\[/\\1\]#s");
define('CRLF', "<br/>\r\n");

$l_lang =array();

// from http://jking.dark-phantasy.com/log/res/func.html_ent.phps
/* This is a function that aims to improve
   upon PHP's htmlentities() function by
   providing more control and by not converting
   ampersands that are already part of valid
   HTML entities.  This function should
   therefore always produce "safe" text without
   the shortcomings of htmlspecialchars().  I
   do not claim that it's fast or terribly good
   code, though.
   
   The second parameter (the first being a
   UTF-8 string to process) is a string that
   specifies what should be converted:
   
   e: Convert literal ampersands to entities
   b: Convert angle brackets to entities
   a: convert apostrophes ( ' ) to entities
   q: Convert double-quotes ( " ) to entities
   
   The parameter defaults to "ebaq", which
   converts everything.  Note, also, that the
   list of named entities used in this funtion
   is that of XHTML 1.0, which contains an
   extra entity, &apos;.  Converting this for
   HTML4 is left up to the user.
*/
function html_ent($text, $param = "ebaq")
{ 
    // the UTF-8 byte order mark is used to temporarily replace ampersands of valid entities
    // until invalid entities are converted.  This should be safe, as using the BOM anywhere
    // but at the beginning of a file is illegal
    if (strpos($param, "e") !== false) { // strip initial UTF-8 byte order marks
        $text = str_replace(chr(0xEF) . chr(0xBB) . chr(0xBF), "", $text); 
        // match all valid named entities
        $text = preg_replace("!&(nbsp|iexcl|cent|pound|curren|yen|brvbar|sect|uml|copy|ordf|laquo|not|shy|reg|macr|deg|plusmn|sup2|sup3|acute|micro|para|middot|cedil|sup1|ordm|raquo|frac14|frac12|frac34|iquest|Agrave|Aacute|Acirc|Atilde|Auml|Aring|AElig|Ccedil|Egrave|Eacute|Ecirc|Euml|Igrave|Iacute|Icirc|Iuml|ETH|Ntilde|Ograve|Oacute|Ocirc|Otilde|Ouml|times|Oslash|Ugrave|Uacute|Ucirc|Uuml|Yacute|THORN|szlig|agrave|aacute|acirc|atilde|auml|aring|aelig|ccedil|egrave|eacute|ecirc|euml|igrave|iacute|icirc|iuml|eth|ntilde|ograve|oacute|ocirc|otilde|ouml|divide|oslash|ugrave|uacute|ucirc|uuml|yacute|thorn|yuml|fnof|Alpha|Beta|Gamma|Delta|Epsilon|Zeta|Eta|Theta|Iota|Kappa|Lambda|Mu|Nu|Xi|Omicron|Pi|Rho|Sigma|Tau|Upsilon|Phi|Chi|Psi|Omega|alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|lambda|mu|nu|xi|omicron|pi|rho|sigmaf|sigma|tau|upsilon|phi|chi|psi|omega|thetasym|upsih|piv|bull|hellip|prime|Prime|oline|frasl|weierp|image|real|trade|alefsym|larr|uarr|rarr|darr|harr|crarr|lArr|uArr|rArr|dArr|hArr|forall|part|exist|empty|nabla|isin|notin|ni|prod|sum|minus|lowast|radic|prop|infin|ang|and|or|cap|cup|int|there4|sim|cong|asymp|ne|equiv|le|ge|sub|sup|nsub|sube|supe|oplus|otimes|perp|sdot|lceil|rceil|lfloor|rfloor|lang|rang|loz|spades|clubs|hearts|diams|quot|amp|lt|gt|OElig|oelig|Scaron|scaron|Yuml|circ|tilde|ensp|emsp|thinsp|zwnj|zwj|lrm|rlm|ndash|mdash|lsquo|rsquo|sbquo|ldquo|rdquo|bdquo|dagger|Dagger|permil|lsaquo|rsaquo|euro|apos);!i", chr(0xEF) . chr(0xBB) . chr(0xBF) . "$1;", $text); 
        // match all decimal entities less than five digits
        $text = preg_replace("!&#([0-9]{1,4});!", chr(0xEF) . chr(0xBB) . chr(0xBF) . "#$1;", $text); 
        // match all hexadecimal entities less than four digits
        $text = preg_replace("!&#[xX]([A-Fa-f0-9]{1,3});!", chr(0xEF) . chr(0xBB) . chr(0xBF) . "#x" . strtolower("$1") . ";", $text); 
        // match all five-digit decimal entiities and store for processing
        preg_match_all("!&#([\d]{5});!", $text, $matches);
        for ($a = 0; $a < sizeof($matches[1]); $a++) {
            if ($matches[1][$a] < 65534) 
                // check to make sure the reference is within the allowed range of code points and preserve the entity if it is
                $text = str_replace("&#{$matches[1][$a]}", chr(0xEF) . chr(0xBB) . chr(0xBF) . "#{$matches[1][$a]}", $text);
        } 
        // match all four-digit hexadecimal entities and store for processing
        preg_match_all("!&#[xX]([A-Fa-f0-9]{4});!", $text, $matches);
        for ($a = 0; $a < sizeof($matches[1]); $a++) { // normalise to lowercase to conform with XML rules
            $num = strtolower($matches[1][$a]);
            if (intval($num, 16) < 0xFFFE) 
                // check to make sure the reference is within the allowed range of code points and preserve the entity if it is
                $text = preg_replace("!&#x$num!i", chr(0xEF) . chr(0xBB) . chr(0xBF) . "#x$num", $text);
        } 
        // convert all remaining ampersands to entities
        $text = str_replace("&", "&amp;", $text); 
        // convert the temporary byte order marks back into literal ampersands
        $text = str_replace(chr(0xEF) . chr(0xBB) . chr(0xBF), "&", $text);
    } 
    if (strpos($param, "b") !== false) {
        $text = str_replace(array("<", ">"), array("&lt;", "&gt;"), $text);
    } 
    if (strpos($param, "a") !== false) {
        $text = str_replace("'", "&#39;", $text);
    } 
    if (strpos($param, "q") !== false) {
        $text = str_replace('"', '&quot;', $text);
    } 
    return $text;
} 
// http://php-tools.org/manuel-php/fonctions/function.html-entity-decode.php
// Pour les utilisateurs ayant des versions antérieures à PHP 4.3.0 :
function unhtmlentities($string)
{
    $trans_tbl = get_html_translation_table (HTML_ENTITIES);
    $trans_tbl = array_flip ($trans_tbl);
    return strtr ($string, $trans_tbl);
} 

function hexhtml_utf($str)
{
    return preg_replace('/&#(\\d+);/e', 'code2utf($1)', utf8_encode($str));
} 
function code2utf($num)
{
    if ($num < 128) return chr($num);
    if ($num < 2048) return chr(($num >> 6) + 192) . chr(($num &63) + 128);
    if ($num < 65536) return chr(($num >> 12) + 224) . chr((($num >> 6) &63) + 128) . chr(($num &63) + 128);
    if ($num < 2097152) return chr(($num >> 18) + 240) . chr((($num >> 12)&63) + 128) . chr((($num >> 6) &63) + 128) . chr(($num &63) + 128);
    return '';
} 
// http://php3.de/manual/fr/function.html-entity-decode.php
function decode_entities($text)
{
    $text = html_entity_decode($text, ENT_QUOTES, "ISO-8859-1"); #NOTE: UTF-8 does not work!
    $text = preg_replace('/&#(\d+);/me', "chr(\\1)", $text); #decimal notation
    $text = preg_replace('/&#x([a-f0-9]+);/mei', "chr(0x\\1)", $text); #hex notation
    return $text;
} 

function convert_text($text, $charset)
{ 
    // echo "original[".mb_detect_encoding($text)."]=".$text."<br/>\r\n";
    $text_encoded = unhtmlentities($text); 
    // echo "unhtmlentities[".mb_detect_encoding($text_encoded)."]=".$text_encoded."<br/>\r\n";
    $text_encoded = change_charset($text_encoded, $charset); 
    // echo "result[".mb_detect_encoding($text_encoded).'->'.$charset."]=".$text_encoded."<br/>\r\n<br/>\r\n";
    return $text_encoded; 
    // PHP5:
    // return html_entity_decode($txt_src,ENT_QUOTES,_CHARSET);
} 

/**
 * change_charset()
 * 
 * Permet d'encoder dans le charset de la page
 * 
 * In progress
 * 
 * @param mixed $text
 * @param string $charset_output
 * @param string $charset_input
 * @return 
 **/
function change_charset($text, $charset_output = 'UTF-8', $charset_input = 'ISO-8859-1')
{ 
    // Values can be : UTF-8 ; ASCII
    //$detected_encoding = mb_detect_encoding($text); 
    // if ($charset_output == $detected_encoding) {
    if ($charset_output == $charset_input) {
        // Encoding is already good
        return $text;
    } elseif ($charset_output == 'UTF-8' && $charset_input == 'ISO-8859-1') {
        // ISO-8859-1 -> UTF8
        return utf8_encode($text);
    } elseif ($charset_output == 'ISO-8859-1' && $charset_input == 'UTF-8') {
        // UTF8 -> ISO-8859-1
        return utf8_decode($text);
    } else {
        // * -> UTF8
		if (function_exists("mb_convert_encoding")) {
		    return mb_convert_encoding($text, $charset_output, $charset_input);
		}else{
			return $text;
		}
        
    } 
} 

/**
 * test()
 * 
 * @return 
 **/
function test()
{
    $main_test_string = "référendum sur la Co&agrave;nstitution européenne";
    echo $main_test_string . "<br>";

    if (function_exists('mb_detect_encoding'))
        $string_test = mb_detect_encoding($main_test_string, 'UTF-8,ISO-8859-1');
    else
        $string_test = 'mb_detect_encoding not existing!!';
    echo "Encoding used: $string_test<br> "; // Properly displays ISO-8859-1 
    // First try converting with iconv
    $iconv_test = iconv("ISO-8859-1", "UTF-8", $main_test_string);
    echo "Iconv test: $iconv_test<br> "; // Displays nothing. No data whatsoever 
    // Now try converting with mb_convert_encoding
    if (function_exists('mb_convert_encoding'))
        $mb_test = mb_convert_encoding($main_test_string, "UTF-8", "ISO-8859-1");
    else
        $string_test = 'mb_convert_encoding not existing!!';
    if (function_exists('mb_detect_encoding'))
        $string_test2 = mb_detect_encoding($mb_test, 'UTF-8, ISO-8859-1');
    else
        $string_test = 'mb_detect_encoding not existing!!';
    echo "Encoding used: $string_test2<br> "; // Indicates string is now UTF-8 encoded (which is wrong)
    echo "MB Test convert value: $mb_test<br> "; // Displays: rÃ©fÃ©rendum sur la Constitution europÃ©enne; doesn't look like UTF-8 to me 
    // Finally try utf8_encode
    if (function_exists('utf8_encode'))
        $utf8_encode_test = utf8_encode($main_test_string);
    else
        $string_test = 'utf8_encode not existing!!';
    if (function_exists('mb_detect_encoding'))
        $string_test3 = mb_detect_encoding($textfieldabstract, 'UTF-8,ISO-8859-1');
    else
        $string_test = 'mb_detect_encoding not existing!!';
    echo "Encoding used: $string_test3<br> "; // Indicates string is now UTF-8 encoded (which is wrong)
    echo "Abstract post conversion: $utf8_encode_test<br> "; // Same as before, displays: rÃ©fÃ©rendum sur la Constitution europÃ©enne 
} 

/**
 * UnescapeText()
 * 
 * @param mixed $text
 * @return 
 **/
function UnescapeText($text)
{
    $newtext = stripslashes($text);
    $newtext = html_entity_decode($newtext);
    return $newtext;
} 

/**
 * EscapeText()
 * 
 * @param mixed $text
 * @return 
 **/
function EscapeText($text)
{
    $newtext = quotemeta($text);
    $newtext = htmlspecialchars_decode($newtext);
    return $newtext;
} 

/**
* updateTag()
* 
* @param mixed $source 
* @param mixed $new 
* @param mixed $codeml 
* @return 
*/
function updateTag($text, $new, $codeml, $overwrite_ifnotags = false)
{ 
    // Trouver la balise de la langue
    $pattern = "#\[" . $codeml . "\](.*?)\[/" . $codeml . "\]#s";
    if (preg_match_all($pattern, $text, $strings) > 0) {
        // si ok, on remplace
        $replacement = "#\[" . $codeml . "\]" . $new . "\[/" . $codeml . "\]#s";
        $val = preg_replace($pattern, $replacement, $text);
    } else {
        // si nok,
        if (preg_match(PATTERN_MLTAGS, $source) > 0 || !$overwrite_ifnotags) {
            // si presence de balises, on ajoute en fin
            $val = $text . '[' . $codeml . ']' . $new . '[/' . $codeml . ']';
        } else {
            // sinon on remplace tout
            // $val = $new;
            // sinon on ajoute et on met en commentaire [xx] le texte actuel
            $val = '[xx]' . $text . '[/xx][' . $codeml . ']' . $new . '[/' . $codeml . ']';
        } 
    } 
    return $val;
} 

/**
* change_charset_tags()
* 
* @param mixed $text 
* @param mixed $charset_out 
* @param mixed $charset_in 
* @param mixed $lang 
* @return 
*/
function change_charset_tags($text, &$lang, $convert_ifnotags=FALSE)
{
	
	if (preg_match(PATTERN_MLTAGS, $text) > 0) {
        // si presence de balises, on ajoute en fin
        $pattern_from = "#\[" . $lang['from']['code'] . "\](.*?)\[/" . $lang['from']['code'] . "\]#s";
		$pattern_to = "#\[" . $lang['to']['code'] . "\](.*?)\[/" . $lang['to']['code'] . "\]#s";
		
        if (preg_match_all($pattern_to, $text, $strings) > 0) {
			// si balise de la langue existe, ne rien faire
			$newvalue = $text;
		}else if (preg_match_all($pattern_from, $text, $strings) > 0) {
            // si balise de la langue source, on convertit
            $new = change_charset($strings[1][0], $lang['to']['charset'], $lang['from']['charset']);
			//On ajoute la langue cible
            $replacement = "#\[" . $lang['to']['code'] . "\](" . $new . ")\[/" . $lang['to']['code'] . "\]#s";
            //$newvalue = preg_replace($pattern_from, $replacement, $text);
//TODO : multiple tags of the same language (cf footer/article...)
global $l_lang;
 $l_lang = $lang;
 
			$newvalue =preg_replace_callback($pattern_from, 'fn_replace_charset',$text);
			return $newvalue;
        } else {
            // Pas de balises de la langue, ne rien faire
            return $text;
        } 
    } else {
        if ($convert_ifnotags) {
            // On remplace tout
			$newvalue = change_charset($text, $lang['to']['charset'], $lang['from']['charset']);
            return $newvalue;
        }else {
            // ne rien faire
            return $text;
        } 
        
    } 

    return $newvalue;
} 

/**
 * fn_replace_charset()
 * 
 * @param mixed $matches
 * @return 
 **/
function fn_replace_charset($matches){
	global $l_lang;
	$new = change_charset($matches[1], $l_lang['to']['charset'], $l_lang['from']['charset']);
	$replacement = "[" . $l_lang['to']['codeml'] . "]" . $new . "[/" . $l_lang['to']['codeml'] . "]";
	return $matches[0].$replacement;
}

function cleanSqlValue($text){
	//Simple quote
	return preg_replace("#(?<!\\\\)'#","\'",$text);
}

?> 
<?php
//error_reporting(0);
//error_reporting(E_ALL);

include ("class/i18ntranslate.php");

/**/
//Full Test 
show_sample();
/**/

/*
//Test on a local file
include ("../include/array.php");
test_file();
*/

/**
 * Test debugging using a local file
 *
 * @author  LudoO  <ludoo@mail.com>
 */
function test_file(){   
	   $myTranslation = new Translation('en', 'el', 'babelfish');
	   echo "<p>";
	   if($myTranslation->isTranslationEnabled()){
	        echo $myTranslation->translate("hello");
	   }else{
	   		  echo "Impossible : changer les langues";
	   }
       echo "</p></div>\r\n";
}

/**
 * Sample of function
 *
 * @author  LudoO  <ludoo@mail.com>
 */
function show_sample(){
    $text="";
    
    echo "<html>";
    echo "<head>";
    echo "<title>Traduction automatique</title>";
    //Encodage de la page
	$encoding=(isset($_REQUEST["encoding"]))?$_REQUEST["encoding"]:"UTF-8";
    echo "<meta http-equiv='content-type' content='text/html; charset=".$encoding."'/>";
	echo "</title>";
	echo "<body>\r\n";
    if (isset($_REQUEST["text"])) {
       //Demande de traduction
	   $text=$_REQUEST["text"];
	   //google moteur par défaut
	   $engine=(isset($_REQUEST["engine"]))?$_REQUEST["engine"]:"google";
	   //Langues
	   $input_lang=(isset($_REQUEST["input"]))?$_REQUEST["input"]:"fr";
	   $output_lang=(isset($_REQUEST["output"]))?$_REQUEST["output"]:"en";
	   
	   $myTranslation = new Translation($input_lang, $output_lang, $engine);
	   echo "<div><p>Traduction avec ";
	   echo "<a href='".$myTranslation->getEngineURL()."'>".$engine."</a>";
	   echo " de ".$input_lang." vers ".$output_lang."</p>\r\n";
	   echo "<p>";
	   if($myTranslation->isTranslationEnabled()){
	        echo $text."=";
	        echo $myTranslation->translate($text);
	   }else{
	   		  echo "Impossible : changer les langues";
	   }
       echo "</p></div>\r\n";
    }
    
    //Formulaire de traduction
	echo "<br/><br/><div>";
    echo "<form action='".$_SERVER["PHP_SELF"]."' method='post'>\r\n";
    echo "<input type='text' name='text' value='".$text."'/>\r\n";
    $langs=array(
            'en' => 'Anglais',
            'fr' => 'Français',
            'es' => 'Espagnol',
            'de' => 'Allemand',
            'nw' => 'Norvégien',
            'it' => 'Italien',
            'pt' => 'Portugais',
            'nl' => 'Hollandais',
            'el' => 'Grec',
            'ru' => 'Russe',
            'ko' => 'Coréen',
            'ja' => 'Japonais',
            'zh' => 'Chinois simplifiée',
            'zt' => 'Chinois traditionnel',
            );
    $engines=array(
            'google' => 'Google',
            'babelfish' => 'Babelfish',
            'freetranslation' => 'Free Translation',
            );
	printSelect("input", $langs, $input_lang);
	printSelect("output", $langs, $output_lang);
	printSelect("engine", $engines, $engine);

    echo "<input type='submit' name='translate' value='traduire'/>";
    echo "</form></div>";
	echo "</body></html>";
}

/**
 * Translate text via online engine like google or babelfish
 *
 * @author  LudoO  <ludoo@mail.com>
 */
 function printSelect($name, $langs, $selected){
    echo "<select name='".$name."'>";
        foreach($langs as $key => $lang) {
        	echo "<option value='".$key."'";
				if($key==$selected)
				 echo " selected='selected'";
				echo ">".utf8_encode($lang)."</option>\r\n";	   
			}
    echo "</select>\r\n";
}
?>
<?php
/*
 * Regles pour altavista babelfish
 *
 */
	$homepage="http://www.appliedlanguage.com/free_translation.shtml";
	$url = "http://www.appliedlanguage.com/free_translation.shtml";
	$outputEncoding = "UTF-8";
	$inputEncoding  = "UTF-8";
	$maxSize  = 10000; //Limit 150 Words

	//TextArea
	$textarea = "transtext";
	$param[$textarea]='';
	//Language select
	$select = "lpair";
	$param[$select]=''	;
	
	$pairs=array();
	 $pairs['zh']['en']="zh_en";
	 $pairs['zt']['en']="zt_en";
	 $pairs['en']['zh']="en_zh";
	 $pairs['en']['zt']="en_zt";
	 $pairs['en']['nl']="en_nl";
	 $pairs['en']['fr']="en_fr";
	 $pairs['en']['de']="en_de";
	 $pairs['en']['it']="en_it";
	 $pairs['en']['ja']="en_ja";
	 $pairs['en']['ko']="en_ko";
	 $pairs['en']['pt']="en_pt";
	 $pairs['en']['ru']="en_ru";
	 $pairs['en']['es']="en_es";
	 $pairs['nl']['en']="nl_en";
	 $pairs['nl']['fr']="nl_fr";
	 $pairs['fr']['en']="fr_en";
	 $pairs['fr']['de']="fr_de";
	 $pairs['fr']['it']="fr_it";
	 $pairs['fr']['pt']="fr_pt";
	 $pairs['fr']['nl']="fr_nl";
	 $pairs['fr']['es']="fr_es";
	 $pairs['de']['fr']="de_fr";
	 $pairs['it']['en']="it_en";
	 $pairs['it']['fr']="it_fr";
	 $pairs['ja']['en']="ja_en";
	 $pairs['ko']['en']="ko_en";
	 $pairs['pt']['en']="pt_en";
	 $pairs['pt']['fr']="pt_fr";
	 $pairs['ru']['en']="ru_en";
	 $pairs['es']['en']="es_en";
	 $pairs['es']['fr']="es_fr";

	$delimiter_pair="_";
	$regex['start']='<textarea name="ResultArea" id="ResultArea" rows="3" wrap="virtual" cols="30">';
	$regex['end']="</textarea>";
?>
<?php
/*
 * Regles pour freetranslation
 *
 */
	$homepage="http://www.freetranslation.com";
	$url = "http://ets.freetranslation.com";
	$outputEncoding = "UTF-8";
	$inputEncoding  = "UTF-8";
	$maxSize  = 50000; //Limit 750 words
	$param['sequence']='core';
	//$param['mode']='html';
	$param['mode']='text';
	//$param['template']='results_en-us.htm';

	//TextArea
	$textarea = "srctext";
	$param[$textarea]='';
	//Language select
	$select = "language";
	$param[$select]='';

	$pairs=array();
	$pairs['en']['es']="English/Spanish";
	$pairs['en']['fr']="English/French";
	$pairs['en']['de']="English/German";
	$pairs['en']['it']="English/Italian";
	$pairs['en']['nl']="English/Dutch";
	$pairs['en']['pt']="English/Portuguese";
	$pairs['en']['ru']="English/Russian";
	$pairs['en']['nw']="English/Norwegian";
	$pairs['en']['zh']="English/SimplifiedChinese";
	$pairs['en']['zt']="English/TraditionalChinese";
	$pairs['es']['en']="Spanish/English";
	$pairs['fr']['en']="French/English";
	$pairs['de']['en']="German/English";
	$pairs['it']['en']="Italian/English";
	$pairs['nl']['en']="Dutch/English";
	$pairs['pt']['en']="Portuguese/English";
	$pairs['ru']['en']="Russian/English";
	
	$delimiter_pair="/";
	/*
	$regex['start']=$txt2regex('<textarea name="dsttext" cols="40" rows="6" style="width:355px; height:120px; padding:1px 3px" wrap="virtual" tabindex="1">');
	$regex['end']=$txt2regex("</textarea>");
	*/
	$regex['start']="";
	$regex['end']="";
?>
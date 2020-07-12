<?php
// $Id: translate.php,v 0.1.0 20/04/05 14:29 LudoO Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  ------------------------------------------------------------------------ //
// Author: LudoO                                                             //
// URL: http://ludoo.nuxit.net                                               //
// Class: Class to translate via online engine like google or babelfish      //
// ------------------------------------------------------------------------- //

class Translation {

    /**#@+
     * @var array
     */
	var $url="";
	var $hidden= array();
	var $regex= array();
	var $longpair = array();
	var $select="";
	var $textarea="";
	var $engine="";
	var $engine_list=array('google', 'babelfish', 'freetranslation');
	var $input="fr";
	var $output="en";	
	var $outputEncoding="UTF-8";
	var $inputEncoding="UTF-8";
    /**#@-*/
   
    /*
    * Construteur
    *
    */
    /*
	function Translation(){
      $this->init();
    }
    */
    
    /*
    * Construteur avec paramètres
    *
    * @param string $input  : Langue du texte source
    * @param string $output : Langue de la cible
    * @param string $engine : Moteur de traduction
    */
    function Translation($input, $output, $engine='google'){
		$this->init();
		$this->setInput($input);
		$this->setOutput($output);
		$this->setEngine($engine);
		$this->selectBestEngine();
    }
    
    /*
    * Permet de déterminer un moteur dont le couple langues source/cible sont possibles
    *
    * @return boolean
    *
    */
    function selectBestEngine(){
    	//Current is already good
		if ($this->isTranslationEnabled()){
		   return true;
    	}

    	//Do not test current engine
    	$curr_engine=$this->engine;
    	foreach($this->engine_list as $engine_test){
    		if ($engine_test!=$curr_engine){
    			$this->setEngine($engine_test);
            	if ($this->isTranslationEnabled()){        	   
				   return true;
            	}
			}
    	}
    	return false;
    }
    
    
    /*
    * Permet d'envoyer des données par la méthode http POST
    *
    * @param string $Host : Hote du script destinataire
    * @param string $URI : URI du script qui recevra les données
    * @param string $Referer : page d'où sont émises les données
    * @param array $Post : tableau de varaibles à envoyer
    *
    * @return array
    *
    */
    function httpPost($URI, $Post)
    {
           
		$urlp = $this->break_url($this->url);
        $Host = $urlp['host'];
        $Referer=$URI;
 
		$Body = '';
        foreach($Post as $key => $value)
            {
                $Body.= urlencode($key).'='.urlencode(stripslashes($value)).'&';
            }
        $ContentLength = strlen($Body);

        // Generate the Request header
        $Request = "POST $URI HTTP/1.1\r\n";
        $Request.= "Host: $Host\r\n";
        $Request.= 'User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1) ';
        $Request.= "Gecko/20021204\r\n";
        /*$Request.= 'Accept: text/xml,application/xml,application/xhtml+xml,';
        $Request.= 'text/html;q=0.9,text/plain;q=0.8,video/x-mng,image/png,';
        *///$Request.= "image/jpeg,image/gif;q=0.2,text/css,*/*;q=0.1\r\n";
        /*$Request.= "Accept-Language: en-us, en;q=0.50\r\n";
        $Request.= "Accept-Encoding: gzip, deflate, compress;q=0.9\r\n";
        $Request.= "Accept-Charset: ISO-8859-1, utf-8;q=0.66, *;q=0.66\r\n";
        $Request.= "Keep-Alive: 300\r\n";
        $Request.= "Connection: keep-alive\r\n";
        */
		$Request.= "Referer: $Referer\r\n";
        //$Request.= "Cache-Control: max-age=0\r\n";
        $Request.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $Request.= "Content-Length: $ContentLength\r\n\r\n";
        $Request.= "$Body\r\n";

        // Open the connection to the host
        $socket = fsockopen($Host, 80, $errno, $errstr);

        if (!$socket)
            {
				$Result['errno'] = $errno;
				$Result['errstr'] = $errstr;
				return $Result;
            }
        else
            {
                $idx = 0;
                fputs($socket, $Request);

                while (!feof($socket))   {
                    $Result[$idx++] = fgets($socket, 1024) or exit("erreur :".$idx."=".$Result[$idx-1]);
                    echo $idx."=".htmlentities($Result[$idx-1])."<br/>\r\n";
                }

                fclose ($socket);
                return $Result;
            }
    }

    /****
     * Titre : Scinde une URL
     * Auteur : Damien Seguy
     * Email : damien.seguy@nexen.net
     * Url : www.nexen.net
     * Description : Scinde une URL en host/path/fichier.

    ****/
    function break_url($url){
    $retour = array();
            $url = eregi_replace("^http://", "", $url);
            $retour['host'] = substr($url, 0, strpos($url, "/"));
            $retour['file']  = substr($url,  strrpos($url, "/")+1);
            $retour['path'] = substr($url,  strpos($url, "/")+1,  strrpos($url, "/") - 1 -strpos($url, "\
    /"));
            return $retour;
    }

     function HTTP_Post($url , $vars){
         $urlp = $this->break_url($url);

         // $vars est un tableau associatif de la forme :
         // $vars['nom_de_la_variable'] = 'valeur_de_la_variable';
          $args = array();
          while (list($cle, $val) = each($vars)) {
          array_push($args, "$cle=".urlencode($val));
          }
          $arg = join("&", $args);

         // Entete du POST
          $POST = "POST $url\n";
          $POST .= "Content-Type: application/x-www-form-urlencoded\n";
          $POST .= "Content-Length: ".strlen($arg)."\n\n";
          $POST .= "$arg\n";

         // Ouvre la chaussette
          $socket = fsockopen($urlp['host'], 80);
          if (!$socket) {
          return FALSE;
          }

         // envoi du POST
          fputs($socket, $POST);
          $retour = array();
          while (!feof($socket)) {
          array_push($retour, fgets($socket, 102400));
          }
          fclose($socket);
          return $retour;
        }

    function HttpGet($url, $param=null)
    {
           //$binary=false;
		   $params="";
           if ($param!=null){
               foreach( $param as $key => $para )
                   $params.= (($params!='')?"&":"").$key."=".urlencode($para);
           }
           
           $uri=$url.(($params!='')?("?".$params):"");
	
           //$file = fopen ($uri, $binary?"rb":"r") or exit("Failed to open URL:".$uri);
           $file = fopen ($uri, "r") or exit("Failed to open URL:".$uri);
           if (!$file) {
    		exit;
           }
           
		   $content='';
           while (!feof ($file)) {
        		/*if ($binary){
        			$content.=fread ($file, 4096);
        		}else{
        		*/
					$content.=fgets ($file, 4096);
        		//}
           }
           
           fclose($file);
           return $content;
    }
    
	function txt2regex($txt){
		$txt2=str_replace(" ", "\s", $txt);
		//$txt2=str_replace("/", "\/", $txt2);
		return $txt2;
	}

    function init(){
    	//From English
		$this->longpair['english']="en";
		$this->longpair['french']="fr";
		$this->longpair['german']="de";
		
		//From others country
		$this->longpair['anglais']="en";
		$this->longpair['francais']="fr";
		$this->longpair['deutsch']="de";
	}
	
	function setEngine($id)
	{
		$this->engine = $id;
		/*
		$hidden = new array[];
		$regex= new array[];
		*/
		$hidden=array();
		$regex=array();

		if (ereg($this->engine,'google')) {
		    include ("engine/google.php");

			/*
			$this->url = "http://translate.google.com/translate_t";
			$this->outputEncoding = "UTF-8";
	        $this->inputEncoding  = "UTF-8";
	        $this->maxSize  = 5000;      //Limit
			$this->param['hl']='fr';    // Langue de traduction (cible)
			$this->param['ie']=$this->inputEncoding; //Input encoding (UTF-8 par defaut)
			$this->param['oe']=$this->outputEncoding; //Output encoding (UTF-8 par defaut)
			//TextArea
			$this->textarea = "text";
			$this->param[$this->textarea]='';
			//Language select
			$this->select = "langpair";
			$this->param[$this->select]='';
			//$this->pairs="en|de;en|es;en|fr;en|it;en|pt;en|ja;en|ko;en|zh-CN;de|en;de|fr;es|en;fr|en;fr|de;it|en;pt|en;ja|en;ko|en;zh-CN|en";
			$this->pairs=array("","");
			$this->pairs['en']['de']="en|de";
			$this->pairs['en']['es']="en|es";
			$this->pairs['en']['fr']="en|fr";
			$this->pairs['en']['it']="en|it";
			$this->pairs['en']['pt']="en|pt";
			$this->pairs['en']['ja']="en|ja";
			$this->pairs['en']['ko']="en|ko";
			$this->pairs['en']['zh']="en|zh-CN";
			$this->pairs['de']['en']="de|en";
			$this->pairs['de']['fr']="de|fr";
			$this->pairs['es']['en']="es|en";
			$this->pairs['fr']['en']="fr|en";
			$this->pairs['fr']['en']="fr|de";
			$this->pairs['it']['en']="it|en";
			$this->pairs['pt']['en']="pt|en";
			$this->pairs['ja']['en']="ja|en";
			$this->pairs['ko']['en']="ko|en";
			$this->pairs['zh']['en']="zh-CN|en";
			
			$this->delimiter_pair="|";
			$this->regex['start']=$this->txt2regex("<textarea name=q rows=5 cols=45 wrap=PHYSICAL>");
			$this->regex['end']=$this->txt2regex("</textarea>");
			*/
		}elseif(ereg($this->engine,'babelfish')) {
			include ("engine/babelfish.php");
			
			/*
			$this->url = "http://babelfish.altavista.com/tr";
			$this->outputEncoding = "UTF-8";
	        $this->inputEncoding  = "UTF-8";
	        $this->maxSize  = 10000; //Limit 150 Words
			//$this->param['doit']='done';
			//$this->param['intl']='1';
			//$this->param['tt']='urltext';
			//TextArea
			$this->textarea = "trtext";
			$this->param[$this->textarea]='';
			//Language select
			$this->select = "lp";
			$this->param[$this->select]=''	;
			//$this->pairs="zh_en;zt_en;en_zh;en_zt;en_nl;en_fr;en_de;en_el;en_it;en_ja;en_ko;en_pt;en_ru;en_es;nl_en;nl_fr;fr_en;fr_de;fr_el;fr_it;fr_pt;fr_nl;fr_es;de_en;de_fr;el_en;el_fr;it_en;it_fr;ja_en;ko_en;pt_en;pt_fr;ru_en;es_en;es_fr";
			$this->pairs=array("","");
			$this->pairs['zh']['en']="zh_en";
			$this->pairs['zt']['en']="zt_en";
			$this->pairs['en']['zh']="en_zh";
			$this->pairs['en']['zt']="en_zt";
			$this->pairs['en']['nl']="en_nl";
			$this->pairs['en']['fr']="en_fr";
			$this->pairs['en']['de']="en_de";
			$this->pairs['en']['el']="en_el";
			$this->pairs['en']['it']="en_it";
			$this->pairs['en']['ja']="en_ja";
			$this->pairs['en']['ko']="en_ko";
			$this->pairs['en']['pt']="en_pt";
			$this->pairs['en']['ru']="en_ru";
			$this->pairs['en']['es']="en_es";
			$this->pairs['nl']['en']="nl_en";
			$this->pairs['nl']['fr']="nl_fr";
			$this->pairs['fr']['en']="fr_en";
			$this->pairs['fr']['de']="fr_de";
			$this->pairs['fr']['el']="fr_el";
			$this->pairs['fr']['it']="fr_it";
			$this->pairs['fr']['pt']="fr_pt";
			$this->pairs['fr']['nl']="fr_nl";
			$this->pairs['fr']['es']="fr_es";
			$this->pairs['de']['en']="de_en";
			$this->pairs['de']['fr']="de_fr";
			$this->pairs['el']['en']="el_en";
			$this->pairs['el']['fr']="el_fr";
			$this->pairs['it']['en']="it_en";
			$this->pairs['it']['fr']="it_fr";
			$this->pairs['ja']['en']="ja_en";
			$this->pairs['ko']['en']="ko_en";
			$this->pairs['pt']['en']="pt_en";
			$this->pairs['pt']['fr']="pt_fr";
			$this->pairs['ru']['en']="ru_en";
			$this->pairs['es']['en']="es_en";
			$this->pairs['es']['fr']="es_fr";

			$this->delimiter_pair="_";
			$this->regex['start']=$this->txt2regex("<td bgcolor=white class=s><div style=padding:10px;>");
			$this->regex['end']=$this->txt2regex("</div></td>");
			*/

		}elseif(ereg($this->engine,'freetranslation')) {
            include ("engine/freetranslation.php");
            
			/*
			$this->url = "http://ets.freetranslation.com";
			$this->outputEncoding = "UTF-8";
	        $this->inputEncoding  = "UTF-8";
	        $this->maxSize  = 50000; //Limit 750 words
			$this->param['sequence']='core';
			//$this->param['mode']='html';
			$this->param['mode']='text';
			//$this->param['template']='results_en-us.htm';

			//TextArea
			$this->textarea = "srctext";
			$this->param[$this->textarea]='';
			//Language select
			$this->select = "language";
			$this->param[$this->select]='';

			//$this->pairs="English/Spanish;English/French;English/German;English/Italian;English/Dutch;English/Portuguese;English/Russian;English/Norwegian;English/SimplifiedChinese;English/TraditionalChinese;Spanish/English;French/English;German/English;Italian/English;Dutch/English;Portuguese/English;Russian/English";
			$this->pairs=array("","");
			$this->pairs['en']['es']="English/Spanish";
			$this->pairs['en']['fr']="English/French";
			$this->pairs['en']['de']="English/German";
			$this->pairs['en']['it']="English/Italian";
			$this->pairs['en']['nl']="English/Dutch";
			$this->pairs['en']['pt']="English/Portuguese";
			$this->pairs['en']['ru']="English/Russian";
			$this->pairs['en']['nw']="English/Norwegian";
			$this->pairs['en']['zh']="English/SimplifiedChinese";
			$this->pairs['en']['zt']="English/TraditionalChinese";
			$this->pairs['es']['en']="Spanish/English";
			$this->pairs['fr']['en']="French/English";
			$this->pairs['de']['en']="German/English";
			$this->pairs['it']['en']="Italian/English";
			$this->pairs['nl']['en']="Dutch/English";
			$this->pairs['pt']['en']="Portuguese/English";
			$this->pairs['ru']['en']="Russian/English";
			
			$this->delimiter_pair="/";
			/*
			$this->regex['start']=$this->txt2regex('<textarea name="dsttext" cols="40" rows="6" style="width:355px; height:120px; padding:1px 3px" wrap="virtual" tabindex="1">');
			$this->regex['end']=$this->txt2regex("</textarea>");

			$this->regex['start']="";
			$this->regex['end']="";
			*/
		}
		
		//$this->homepage = $homepage;
		$this->url = $url;
		$this->outputEncoding = $outputEncoding;
	    $this->inputEncoding  = $inputEncoding;
	    $this->maxSize  = $maxSize;
		$this->param=$param;
		$this->textarea = $textarea;
		$this->select = $select;
		$this->pairs=$pairs;
		$this->delimiter_pair=$delimiter_pair;
		$this->regex['start']=$this->txt2regex($regex['start']);
		$this->regex['end']=$this->txt2regex($regex['end']);
			
	}
	
    /**
     * Retourne le nom du moteur de traduction
     *
     */
	function getEngineName(){
	   return $this->engine;
	}
	
    /**
     * Retourne l'URL du moteur de traduction
     *
     */
	function getEngineURL(){
	   return $this->url;
	}
	
    /**
     * Retourne la paire de langues à traduire si elle existe
     *
     */
	function getCurrentPair(){
		$pair="";
		/*if (array_key_exists($this->input,$this->pairs)){
		  if (array_key_exists($this->output,$this->pairs[$this->input])){
		    $pair = $this->pairs[$this->input][$this->output] or exit("");	  
		  }
		}*/	
		if (isset($this->pairs[$this->input][$this->output]))
		   $pair = $this->pairs[$this->input][$this->output];
		/*
		$pair = $this->pairs[$this->input][$this->output] or exit("");
		if (isset($pair)){
		   //Effectuer la correspondance des langues
		   return $pair;
		}else{
		   return "";
		};
		*/
		
		return $pair;
	}
	
    /**
     * Détermine si les langues choisis sont possibles à traduire
     * avec le moteur sélectionné
     *
     */
	function isTranslationEnabled(){
		$pair = $this->getCurrentPair();
		return ($pair!="");
	}
	
    /**
     * Définit la langue de la source
     *
     * @lang  : Langue du texte saisi
     */
	function setInput($lang){
	   if (isset($this->longpair[$lang])){
	   	  $this->input=$this->longpair[$lang];
	   }else{
	     $this->input=$lang;
	   }
	}
	
    /**
     * Définit la langue de la traduction
     *
     * @lang  : Langue de la traduction
     */
	function setOutput($lang){
	   if (isset($this->longpair[$lang])){
	   	  $this->output=$this->longpair[$lang];
	   }else{
	     $this->output=$lang;
	   }
	}
	
    /**
     * Retourne l'encodage de la langue de la source
     *
     * @encoding  : Encodage de la langue du texte à saisir
     */
	function getInputEncoding(){
	   return $this->inputEncoding;  
	}

    /**
     * Retourne l'encodage de la langue de la traduction
     *
     * @encoding  : Encodage de la langue de la traduction
     */
	function getOutputEncoding(){
	   return $this->outputEncoding;
	}
	
    /**
     * Affiche un ligne avec un retour chariot HTML + ASCII
     *
     */
	function echoline($text, $enter=1){
			 echo $text;
			 for($i=0;$i<$enter;$i++){
			    echo "<br/>\r\n";            
			 }
	}

    /**
     * Retourne le texte traduit
     *
     * @input  : Texte à traduire
     */
	function translate($input)
	{
			$t1 = (double)microtime();
			$text="";
			$result="";
			$content="";
			$this->param[$this->select]=$this->getCurrentPair();
			$this->param[$this->textarea]=$input;
			$contents=array();
			//$contents = $this->HTTP_Post($this->url, $this->param);
			//$contents = $this->httpPost($this->url, $this->param);
//$data= $this->HttpGet("gr.htm");
			$data= $this->HttpGet($this->url, $this->param);
/*	*/
//$this->echoline("data(htmlentities)=[".htmlentities($data)."]");
$this->echoline("data=[".$data."]");	
/* */
			/*
			$data="";
			foreach($contents as $content)
               $data.=$content;
			*/
			
			$match="";
			if (($this->regex['start']=='') && ($this->regex['end']=='')){
			   $match=$data;
			}else{
    			$regex="#(".$this->regex['start']."(.*?)".$this->regex['end'].")#s";
				if ("/".preg_match($regex, $data, $matches)) {		
    				if ($matches) 
        				$match=$matches[2];
    			}
			}
/*
$this->echoline("Regex=[".htmlentities($regex)."]",2);
$this->echoline("URL=[".$this->url.'?'.implode_assoc_r('=', '&',  $this->param, true, true)."]",2);
$this->echoline("input=[".$input."]",2);
$this->echoline("match=[".$match."]",2);
$decode = $this->getUniword($match);
$this->echoline("decode=[".$decode."]",2);
*/		
			return $match;
			

            //temps de generation du script
            /*
			$t2 = (double)microtime();
            ($t2>$t1)?$t=substr($t2-$t1,0,4):$t=substr($t1-$t2,0,4);
            */
			//echo "<center><font style='color:red;'>Temps de génération de la page: ".$t."s</font></center>";
	}

    /**
     * Retourne le temps d'execution d'un script
     *
     * this function was taken from example code on php.net for the microtime function
     * return the time in seconds
     */
   function getmicrotime(){
       list($usec, $sec) = explode(" ",microtime());
       return ((float)$usec + (float)$sec);
   }

function getUniword($text){
  $content='';
  $count=strlen($text);
  for($i=0;$i<$count;$i++){
     $car = substr($text, $i,1);
	 $code = ord($car);
     $codehex = dechex($code);
     $content.= '<'.$i.':'.$car.':'.$code.':'.$codehex.':'.chr($code).'> ';
  }
  return $content;
}

}

?>
<?php
	/*
    * 
    * 
    * Apercu d'un fichier
    *
    * Input :  lang    : Langue
    *          module  : Nom du module
    *          file    : Nom du fichier
    */

$charset="ISO-8859-1";

echo "<html><meta http-equiv='Content-Type' content='text/html; charset=".$charset."' /><head></head><body>";


define('XOOPS_ROOT_PATH', '/home2/l/ludoo/www/xoops');

if( isset($_GET['lang']) && isset($_GET['module']) && isset($_GET['file']) ){
    $lang   = $_GET['lang'];
    $module = $_GET['module'];
    $file   = $_GET['file'];
    
    if(!defined( 'XOOPS_ROOT_PATH')){
		echo 'Root introuvable!!<br/>';
	}else{
	
    	if ($module == "XOOPScore") {
    		$file_path= XOOPS_ROOT_PATH.'/language/'.$lang.'/'.$file;
    	}elseif ($module == "XOOPSmod") {
    		//TODO
    	}elseif ($module == "XOOPSblocks") {
    		//TODO
    	}else{
    		$file_path= XOOPS_ROOT_PATH.'/modules/'.$module.'/language/'.$lang.'/'.$file;
    	}
    	
        //Ouvrir le fichier 
        if (file_exists($file_path)){
           $content=file_get_contents($file_path);
            echo 'Chemin  : '.$file_path.'<br/>';
    		echo 'Fichier : '.$file.'<br/>';
            echo 'Module  : '.$module.'<br/>';
            echo 'Langue  : '.$lang.'<br/>';
            echo '<textarea name="file" rows="30" cols="80" style="border:1px solid #999999;font-size:10pt">'.$content.'</textarea>';
        }else{
           echo 'Fichier introuvable: '.$file_path.'<br/>';
    	}  
	}

}else{
  echo '<p>Error!!</p>';
}

echo '</body></html>';
?>

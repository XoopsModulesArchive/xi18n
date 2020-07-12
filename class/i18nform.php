<?php
  
/**
 * 
 *
 * @version $Id$
 * @copyright 2005 
 */
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';

$titre_form =_FO_VXP_CLIENT;
$form	= new GeneratorThemeForm($titre_form, 'form', 'index.php');

//Hidden field
$f_id_hidden  = new XoopsFormHidden("id", $client_id);
$form->addElement($f_id_hidden);

//Action button
$action_buttons	= new XoopsFormElementTray("");
 $submit_action	= new XoopsFormButton('', 'action', _AC_GEN_VALIDER, 'submit');
 $action_buttons->addElement($submit_action);
 $form->addElement($action_buttons);
  
//Zone de texte
$f_prenom = new XoopsFormText(_FO_PRENOM, 'prenom',50,150,$prenom);
$form->addElement($f_prenom,true); // obligatoire  

//display the form
$form->display();
?>
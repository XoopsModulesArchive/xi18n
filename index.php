<?php
require('../../mainfile.php');
include 'include/function.php';

$uname = !empty($xoopsUser) ? $xoopsUser->getVar('uname','E') : _MD_XI_ANONYMOUS;

// We must always set our main template before including the header
$xoopsOption['template_main'] = 'wizard.html';

// Include the page header
require(XOOPS_ROOT_PATH.'/header.php');

$content="";

//Determine step level
$step = getPost('step',1);
if ($step>=1 && $step<=4)
  include 'step'.$step.'.php';
  
$xoopsTpl->assign('i18n_menu', navbar());
$xoopsTpl->assign('i18n_step', $step);
$xoopsTpl->assign('content', $content);


// Include the page footer
require(XOOPS_ROOT_PATH.'/footer.php');

?>
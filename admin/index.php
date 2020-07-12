<?php
include '../../../include/cp_header.php';
if (file_exists("../language/" . $xoopsConfig['language'] . "/admin.php")) {
    include "../language/" . $xoopsConfig['language'] . "/admin.php";
} else {
    include "../language/english/admin.php";
} 

xoops_cp_header();

include 'function.php';
include '../include/function.php';

define('LANG_THIS_URL', XOOPS_URL . '/modules/xi18n/admin/index.php');
$act = isset($_GET['act'])?$_GET['act']:"";
$values = array('', 'lang_title', 'dirname', 'charset', 'code', 'codeml');

switch ($act) {
    case 'add':
        if (isset($_POST['lang'])) {
            if (isset_post($values)) {
                // if(isset($_POST['lang_title']) && isset($_POST['dirname']) && isset($_POST['charset'])){
                // $sql = 'INSERT INTO `'.$xoopsDB->prefix('xi18n_languages').'` VALUES (\'\', \''.$_POST['lang_title'].'\', \''.$_POST['dirname'].'\', \''.$_POST['charset'].'\')';
                $sql_values = getSqlInsertValuesPost($values);
                $sql = 'INSERT INTO `' . $xoopsDB->prefix('xi18n_languages') . '` VALUES (' . $sql_values . ')';
                if ($result = $xoopsDB->query($sql)) {
                    redirect_header(LANG_THIS_URL, 1, _AM_XI_UPDATEOK);
                } else {
                    echo _AM_XI_ERROR . "<br/>" . $sql . "<br/>";
                } 
            } else {
                redirect_header(LANG_THIS_URL, 1, _AM_XI_ERROR);
            } 
        } else {
            the_form($act);
        } 
        break;
    case 'edit':
        if (isset($_GET['lang_id'])) {
            $lang_id = isset($_GET['lang_id'])?$_GET['lang_id']:"";
            if (isset($_POST['lang'])) {
                // if(isset($_POST['lang_title']) && isset($_POST['dirname']) && isset($_POST['charset'])){
                if (isset_post($values)) {
                    /*
		  $sql = 'UPDATE `'.$xoopsDB->prefix('xi18n_languages').'` SET
                 `lang_title` = \''.$_POST['lang_title'].'\',
                 `dirname` = \''.$_POST['dirname'].'\',
                 `charset` = \''.$_POST['charset'].'\,'
                 `code` = \''.$_POST['code'].'\'
				 `codeml` = \''.$_POST['codeml'].'\'
                 WHERE `lang_id` = \''.$_GET['lang_id'].'\'';
          */
                    $sql_values = getSqlUpdateValuesPost($values);
                    $sql = 'UPDATE `' . $xoopsDB->prefix('xi18n_languages') . '` SET ' . $sql_values . 'WHERE `lang_id` = \'' . $_GET['lang_id'] . '\'';
                    if ($xoopsDB->query($sql)) {
                        redirect_header(LANG_THIS_URL, 1, _AM_XI_UPDATEOK);
                    } else {
                        redirect_header(LANG_THIS_URL, 3, _AM_XI_ERROR);
                    } 
                } else {
                    redirect_header(LANG_THIS_URL, 1, _AM_XI_NOLANG);
                } 
            } else {
                $sql = 'SELECT * FROM `' . $xoopsDB->prefix('xi18n_languages') . '` WHERE `lang_id` = ' . $lang_id;
                if (!$result = $xoopsDB->query($sql)) {
                    redirect_header(XOOPS_URL . '/', 1, _AM_XI_ERROR);
                    exit();
                } 
                $data = $xoopsDB->fetchArray($result);
                the_form($act, $lang_id, $data);
            } 
        } else {
            redirect_header(LANG_THIS_URL, 1, _AM_XI_NOLANG);
        } 
        break;
    case 'del':
        if (isset($_GET['lang_id'])) {
            $sql = 'DELETE FROM `' . $xoopsDB->prefix('xi18n_languages') . '` WHERE `lang_id` = ' . $_GET['lang_id'];
            if (mysql_query($sql)) {
                redirect_header(LANG_THIS_URL, 1, _AM_XI_UPDATEOK);
            } 
        } else {
            redirect_header(LANG_THIS_URL, 1, _AM_XI_NOLANG);
        } 
        break;
    default:
        $sql1 = 'SELECT `lang_id` FROM `' . $xoopsDB->prefix('xi18n_languages') . '`';
        if (!$result1 = $xoopsDB->query($sql1)) {
            redirect_header(XOOPS_URL . '/', 1, _AM_XI_ERROR);
            exit();
        } 
        $total = $xoopsDB->getRowsNum($result1);
        if (!isset($_GET["page"])) $page = 1;
        else $page = $_GET["page"];
        $per = 20;
        $list = 10;
        $start = ($page-1) * $per;
        $pages = ceil($total / $per);

        if ((floor($pages / $list) >= 1) && ($pages > $list)) {
            if ($page % $list > 0)
                $page_loop = ((floor($page / $list)) * $list) + 1;
            else
                $page_loop = ((floor(($page-1) / $list)) * $list) + 1;

            if ($pages > ($list + $page_loop-1))
                $page_limit = $list + $page_loop-1;
            else
                $page_limit = $pages;
        } else {
            $page_loop = 1;
            $page_limit = $pages;
        } 

        if ($page == $pages && $total % $per != 0)
            $per = $total % $per;

        $sql = 'SELECT * FROM `' . $xoopsDB->prefix('xi18n_languages') . '` LIMIT ' . $start . ', ' . $per;
        if (!$result = $xoopsDB->query($sql)) {
            redirect_header(XOOPS_URL . '/', 1, _AM_XI_ERROR);
            exit();
        } 

        if (($num = $xoopsDB->getRowsNum($result)) > 0) {
            echo '<table align="center" width="400">';
            echo '<tr><td colspan="5" align="right"><a href="' . LANG_THIS_URL . '?act=add">' . _AM_XI_ADD . '</a></td></tr>';
            echo '<tr>';
            echo '<td width="300">' . _AM_XI_LANGTITLE . '</td>';
            echo '<td width="300">' . _AM_XI_FOLDER . '</td>';
            echo '<td width="200">' . _AM_XI_CHARSET . '</td>';
            echo '<td width="200">' . _AM_XI_CODE . '</td>';
            echo '<td width="200">' . _AM_XI_CODE_ML . '</td>';
            echo '<td width="200">' . _AM_XI_ADMIN . '</td>';
            echo '</tr>';
            for($i = 0;$i < $num;$i++) {
                $data = $xoopsDB->fetchArray($result);
                $data_lang_id = getValue($data, 'lang_id');
                echo '<tr>';
                echo '<td>' . getValue($data, 'lang_title') . '</td>';
                echo '<td>' . getValue($data, 'dirname') . '</td>';
                echo '<td>' . getValue($data, 'charset') . '</td>';
                echo '<td>' . getValue($data, 'code') . '</td>';
                echo '<td>' . getValue($data, 'codeml') . '</td>';
                echo '<td>';
                echo '<a href="' . LANG_THIS_URL . '?act=edit&lang_id=' . $data_lang_id . '">' . _AM_XI_EDIT . '</a>&nbsp;&nbsp;';
                echo '<a href="' . LANG_THIS_URL . '?act=del&lang_id=' . $data_lang_id . '">' . _AM_XI_DEL . '</a>';
                echo '</td>';
                echo '</tr>';
            } 
            echo '</table>';
            if ($pages > 1) {
                echo '<table align="center" width="400"><tr><td align="center"><hr>';
                if ($pages > $list && $page > $list) {
                    $p = floor(($page_loop - 2) / $list) * $list + 1;
                    echo '<a href="' . LANG_THIS_URL . '?page=' . $p . '">' . _AM_XI_L10 . '</a>&nbsp;';
                } 
                if ($page > 1) {
                    $p = $page - 1;
                    echo '&nbsp;<a href="' . LANG_THIS_URL . '?page=' . $p . '">' . _AM_XI_L1 . '</a>&nbsp;';
                } 
                for($t = $page_loop;$t <= $page_limit;$t++) {
                    if ($page == $t) {
                        echo '&nbsp;<b>' . $t . '</b>&nbsp;';
                    } else {
                        echo '&nbsp;<a href="' . LANG_THIS_URL . '?page=' . $t . '">' . $t . '</a>&nbsp;';
                    } 
                } 
                if ($pages > $page) {
                    $p = $page + 1;
                    echo '&nbsp;<a href="' . LANG_THIS_URL . '?page=' . $p . '">' . _AM_XI_N1 . '</a>&nbsp;';
                } 
                if ($pages > $list && $pages >= ($page_loop + $list)) {
                    $p = $page_limit + 1;
                    echo '&nbsp;<a href="' . LANG_THIS_URL . '?page=' . $p . '">' . _AM_XI_N10 . '</a>';
                } 
                echo '</td></tr></table>';
            } 
        } 
} 

xoops_cp_footer();

?>

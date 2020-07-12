<?php
/**
* 
* @version $Id$
* @copyright 2005
*/

require('../../mainfile.php');
require_once ("include/convert.php");
// include 'include/function.php';
// $uname = !empty($xoopsUser) ? $xoopsUser->getVar('uname','E') : _MD_XI_ANONYMOUS;
// We must always set our main template before including the header
// $xoopsOption['template_main'] = 'XI_greet.html';
// Include the page header
require(XOOPS_ROOT_PATH . '/header.php');

$xoopsOption['template_main'] = 'convertdb.html';

global $xoopsDB;
$content = convertDB($xoopsDB, "fr", "fru");
$xoopsTpl->assign('content', $content);

/*
$xoopsTpl->assign('i18n_menu', navbar());
$xoopsTpl->assign('i18n_step', $step);
$xoopsTpl->assign('content', $content);
*/
// Include the page footer
require(XOOPS_ROOT_PATH . '/footer.php');

function convertDB(&$xoopsDB, $code_from, $code_to)
{
	$charset_from = _CHARSET;
	$charset_to = 'UTF-8';

    $exception_tables = array(1 => $xoopsDB->prefix('session')
        );

    $content = ''; 
    $tables_list = mysql_list_tables(XOOPS_DB_NAME);
    //$tables_list = $xoopsDB->list_tables();
	while ($tables = $xoopsDB->fetchRow($tables_list)) {
    //while ($tables = mysql_fetch_row($tables_list)) {
        $deftable = '';
        $table = $tables[0];

        $key_exception = array_search($table, $exception_tables);
        if ($key_exception > 0)
            continue;

        $query_table = "SELECT * FROM " . $table;
        //if ($result = mysql_query($query_table)) {
		if ($result = $xoopsDB->query($query_table)) {
            $primarykey = '';
            //$cols = mysql_num_fields($result);
			$cols = $xoopsDB->getFieldsNum($result);
            $coldatas = array();
            $pkeyExists = 0;
            $len=0;
            //$deftable = $table . '=' . "<br/>\r\n";
            for($i = 0;$i < $cols;$i++) {
				/*
				$flags = mysql_field_flags($result, $i);
                $type = mysql_field_type($result, $i);
                $len = mysql_field_len($result, $i);
                $name = mysql_field_name ($result, $i);
				* */
				$flags = mysql_field_flags($result, $i);
                $type = $xoopsDB->getFieldType($result, $i);
//                $len = $xoopsDB->getFieldLen($result, $i);
                $name = $xoopsDB->getFieldName ($result, $i);

                $pos = strpos($flags, 'primary_key');
                if ($pos === false)
                    $pkey = false;
                else
                    $pkey = true;
                if ($pkeyExists == 0 && $pkey) {
                    $pkeyExists = 1;
                } 
                $isText = ($type == 'string' || $type == 'blob');

                $coldatas[] = array('name' => $name,
                    'type' => $type,
                    'len' => $len,
                    'flags' => $flags,
                    'primary_key' => $pkey,
                    'istext' => $isText
                    );
                //$deftable .= '-' . $name . '[' . $type . ',' . $len . ',' . $flags . ']' . "<br/>\r\n";;
            } 

            if ($pkeyExists == 1) {
                // Loop on data
                //while ($line = mysql_fetch_array($result)) {
				while ($line = $xoopsDB->fetchBoth($result)) {
                    $query_datas = '';
                    $query_keys = '';
                    foreach ($coldatas as $coldata) {
                        $colname = $coldata['name'];
                        if (isset($line[$colname]) && $line[$colname] != '') {
                            // Data
                            if ($coldata['istext']) {
								$newvalue = change_charset_tags($line[$colname], $charset_from, $charset_to, $code_from, $code_to);
								/*
								* verifier l'encodage des quotes...
								* 
								* le quote ` peut résoudre le pb sans appeler cleanSqlValue ?
								* */
								$newvalue = cleanSqlValue($newvalue);
                                if ($newvalue != $line[$colname]) {
	                                if ($query_datas != '')
	                                    $query_datas .= ', ';
									//$query_datas .= '`' . $colname . '` = `' . $newvalue . '`';
									$query_datas .= '`' . $colname . '` = \'' . $newvalue . '\'';
                                } 
                            } 
                            // Key
                            if ($coldata['primary_key']) {
                                if ($query_keys != '')
                                    $query_keys .= ' AND ';
                                $query_keys .= '`' . $colname . '` = \'' . $line[$colname] .'\'';
								//$query_keys .=  $colname . ' = ' . $line[$colname];
                            } 
                        } 
                    } 

					// Save
                    if ($query_datas != '' && $query_keys != ''){
                        $query = 'UPDATE `' . $table . '` SET ' . $query_datas . ' WHERE ' . $query_keys;
						//$query = 'UPDATE ' . $table . ' SET ' . $query_datas . ' WHERE ' . $query_keys;						
						if ($xoopsDB->queryF($query)) {
							$content .= "OK= ".htmlspecialchars($query).CRLF; 
						}else{
							$content .= "NOK err[".$xoopsDB->errno()."]=".$xoopsDB->error().CRLF.htmlspecialchars($query).CRLF.CRLF;
						}
					}
                } 
            } 
			//mysql_free_result($result);
			$xoopsDB->freeRecordSet($result);
        } 
    } 
	$xoopsDB->close();
    return $content;
} 


								
?>
<?php
/**
* Method to recursivly implode a multi-dimensional array
* Orginal: Chris Ross - 17-Aug-2004
* Modified: Walter Torres - 09-14-2004
* reModified: LudoO - 05-12-2005
*/
// http://lu2.php.net/manual/en/function.implode.php
function implode_assoc_r($inner_glue = "=", $outer_glue = "\n", $array = null, $keepOuterKey = false, $useKey = false, $removeNull = false)
{
    $output = array();

    foreach($array as $key => $item)
    if (is_array ($item)) {
        if ($keepOuterKey)
            $output[] = $key; 
        // This is value is an array, go and do it again!
        $output[] = implode_assoc_r ($inner_glue, $outer_glue, $item, $keepOuterKey, $useKey);
    } else
    if (!$removeNull or ($removeNull && $item != null)) {
        if ($useKey)
            $output[] = $key . $inner_glue . $item;
        else
            $output[] = $item;
    } 

    return implode($outer_glue, $output);
} 
// http://lu2.php.net/manual/en/function.implode.php
function explode_assoc_r2($inner_glue = "=", $outer_glue = "\n", $recusion_level = 0, $string = null)
{
    $output = array();
    $array = explode($outer_glue . $recusion_level . $outer_glue, $string);

    foreach ($array as $value) {
        $row = explode($inner_glue . $recusion_level . $inner_glue, $value);
        $output[$row[0]] = $row[1];
        $level = $recusion_level + 1;
        if (strpos($output[$row[0]], $inner_glue . $level . $inner_glue))
            $output[$row[0]] = explode_with_keys_a($inner_glue, $outer_glue, $level, $output[$row[0]]);
    } 
    return $output;
} 
// Re-associate with keys
// fill datas from $flat_datas into $structure (if empty item of $control or force)
function deflatArray(&$structure, $flat_datas, $control, $force = false)
{ 
    // Check integrity
    if (sizeof($flat_datas) != sizeof2($structure))
        echo "Integrity error in deflatArray<br/>\r\n";

    $i = 0;
    foreach($structure as $key => $node) {
        if (is_array($node)) {
            foreach($node as $key_item => $item) {
                if ($force or (isset($control[$key][$key_item]) && $control[$key][$key_item] == ''))
                    $structure[$key][$key_item] = $flat_datas[$i];
                $i++;
            } 
        } else {
            if ($force or (isset($control[$key]) && $control[$key] == ''))
                $structure[$key] = $flat_datas[$i];
            $i++;
        } 
    } 
} 
// Fill exisiting data on a side to the other side using key based on the structure
function arrayMerge(&$base, $datas)
{
    foreach($datas as $key => $node) {
        if (is_array($node)) {
            foreach($node as $key_node => $item) {
                if (isset($base[$key])) {
                    // Target must be too array
                    $base[$key][$key_node] = $item;
                } else {
                    echo "arrayMerge error = cette clé n'existe pas :" . $key;
                } 
            } 
        } else {
            $base[$key] = $node;
        } 
    } 
} 

function arrayfill(&$array, $value = '')
{
    foreach($array as $key => $node) {
        if (is_array($node)) {
            arrayfill($array[$key], $value);
        } else {
            $array[$key] = $value;
        } 
    } 
} 

function sizeof2($array)
{
    $size = 0;
    foreach($array as $value) {
        if (is_array($value)) {
            $size += sizeof2($value)-1;
        } 
    } 
    return $size + sizeof($array);
} 

function getArrayItemByKey(&$arr, $key_name, $key_value){
	foreach($arr as $key=>$item){
		if ($item[$key_name]==$key_value) {
		    return $item;
		}
	}
	return null;
}

function getArrayValueByKey(&$arr, $key_name, $key_value, $name){
	$item = getArrayItemByKey($arr, $key_name, $key_value);
	if (isset($item[$name])) 
	    return $item[$name];
	else
		return null;
}

function da($array, $name, $value = '')
{
    echo "\/-\/-\/-\/-\/-\/-----" . $name . "-----\/-\/-\/-\/-\/-\/<br/>\r\n";
    if ($value != '')
        echo $value . "<br/>\r\n";
    print_r($array);
    echo "/\-/\-/\-/\-/\-/\-----" . $name . "-----/\-/\-/\-/\-/\-/\<br/>\r\n";
} 

?>
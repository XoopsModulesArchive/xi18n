<?
function the_form($act, $lang_id="", $data=null){
$url='index.php?act='.$act;
if ($lang_id!='')
   $url.= '&lang_id='.$lang_id;
   
?>
<form method="post" action="<?=$url;?>">
<input type="hidden" name="lang" value="1">
<table align="center" cellpadding="1" cellspacing="1" width="400" border="1">
 <tr>
  <td width="100"><?=_AM_XI_LANGTITLE;?></td>
  <td width="300">
   <input type="text" name="lang_title" value="<?=$data['lang_title'];?>" size="40">
  </td>
 </tr>
 <tr>
  <td width="100"><?=_AM_XI_FOLDER;?></td>
  <td width="300">
   <input type="text" name="dirname" value="<?=$data['dirname'];?>" size="40">
  </td>
 </tr>
 <tr>
  <td width="100"><?=_AM_XI_CHARSET;?></td>
  <td width="300">
   <input type="text" name="charset" value="<?=$data['charset'];?>" size="40">
  </td>
 </tr>
 <tr>
  <td width="100"><?=_AM_XI_CODE;?></td>
  <td width="300">
   <input type="text" name="code" value="<?=$data['code'];?>" size="40">
  </td>
 </tr>
 <tr>
  <td width="100"><?=_AM_XI_CODE_ML;?></td>
  <td width="300">
   <input type="text" name="codeml" value="<?=$data['codeml'];?>" size="40">
  </td>
 </tr>
 <tr>
  <td colspan="4" align="center">
   <input type="submit">
   <input type="reset">
  </td>
 </tr>
</table>
</form>
<?
}
?>
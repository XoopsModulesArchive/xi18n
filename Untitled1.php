<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="content-language" content="fr" />
</head>
<body>

<?php
require_once ("include/convert.php");

define ("_TEST", "Pour votre \"article\" <b>%s</b> sur <b>%s</b>, ins&eacute;rer le bête 'mot' <font color=red>[pagebreak]</font> (avec les crochets) dans l'article. merci de cliquer <a href=%s>ici</a>");
define ("_CHARSET","UTF-8");
define ("_CHARSET_ISO","ISO-8859-1");


//PHP5 : OK
//PHP4 : "Warning: cannot yet handle MBCS in html_entity_decode()"
$t0=html_entity_decode(_TEST,ENT_QUOTES,_CHARSET);
//Normal way : Decode HTML entities &agrave; &cute;...
$t1=html_entity_decode(_TEST);

//Work around PHP4 : Decode HTML entities &agrave; &cute;...
$t20=html_ent(_TEST, "e");
$t21=html_ent(_TEST, "eb");
$t22=html_ent(_TEST, "eba");
$t23=html_ent(_TEST, "ebaq");

$t3=utf8_encode(unhtmlentities(_TEST));

/*echo "<p>Phrase originale : "._TEST."</p>\r\n";
echo "<p>html_entity_decode("._CHARSET.") : ".$t0."</p>\r\n";
echo "<p>html_entity_decode : ".$t1."</p>\r\n";
echo "<p>hexhtml_utf e: ".$t20."</p>\r\n";
echo "<p>hexhtml_utf eb: ".$t21."</p>\r\n";
echo "<p>hexhtml_utf eba: ".$t22."</p>\r\n";
echo "<p>hexhtml_utf ebaq: ".$t23."</p>\r\n";
*/

echo "<p>utf8_encode(unhtmlentities : ".$t3."</p>\r\n";




?>
</body>
</html>


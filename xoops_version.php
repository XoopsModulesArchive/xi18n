<?php
$modversion['name'] = _MI_XI_NAME;
$modversion['version'] = 0.10;
$modversion['description'] = _MI_XI_DESC;
$modversion['credits'] = '';
$modversion['author'] = '&lt;LudoO&gt; <br /> http://ludoo.nuxit.net';
$modversion['license'] = 'GPL see LICENSE';
$modversion['official'] = 0;
$modversion['image'] = 'xilogo.png';
$modversion['dirname'] = 'xi18n';

// Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Db
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";

// Table
$modversion['tables'][0] = "xi18n_languages";

// Menu
$modversion['hasMain'] = 1;
$modversion['sub'][1]['name'] = _MI_XI_SMNAME1;
$modversion['sub'][1]['url'] = "index.php";
$modversion['sub'][2]['name'] = _MI_XI_SMNAME2;
$modversion['sub'][2]['url'] = "convertdb.php";

// Templates
$modversion['templates'][1]['file'] = 'wizard.html';
$modversion['templates'][1]['description'] = 'wizard Form';
$modversion['templates'][2]['file'] = 'convertdb.html';
$modversion['templates'][2]['description'] = 'convertdb';
?>
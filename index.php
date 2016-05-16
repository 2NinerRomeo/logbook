<?php //Main Application access point
if ( !isset($_REQUEST['-sort']) and @$_REQUEST['-table'] == 'flights' ){
   $_REQUEST['-sort'] = $_GET['-sort'] = 'id desc';
}
if ( !isset($_REQUEST['-sort']) and @$_REQUEST['-table'] == 'airplanes' ){
   $_REQUEST['-sort'] = $_GET['-sort'] = 'id desc';
}
require_once "/var/www/html/xataface/public-api.php";
df_init(__FILE__, "/xataface")->display();
?>

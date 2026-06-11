<?php
if(isset($_POST['tableString']) && $_POST['tableString'] != null){

    $tbl ='<table>';
    $tbl .= utf8_decode($_POST['tableString']);
    $tbl .= '</table>';

}

header ("Expires: Mon, 29 Out 2015 15:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/x-msexcel"); 
header ("Content-Disposition: attachment; filename=anexos.xls" );
header ("Content-Description: PHP Generated Data" );
echo $tbl;

?>
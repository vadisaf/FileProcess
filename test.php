<?php
#$fp = fopen('./ref', 'w');
#fwrite($fp, 'DONE');
#fclose($fp);
#echo gettype($_SERVER["HTTP_REFERER"]);
#echo "script works!!!";

/*$s = "string";
if (strpos($s, '0') || strpos($s, '1'))
	echo "YES";*/

#echo file_get_contents('/var/www/html/files/ref1');

$str = "asfasAS_Main_Pag.yyds";
echo strpos($str, 'Main_Page');


?>

<?php
$length = 64;
$characters = '0123456789abcdef';
$charactersLength = strlen($characters);
$code = '';
for ($i = 0; $i < $length; $i++)
    $code .= $characters[rand(0, $charactersLength - 1)];

//$characters = '0123456789qwertyuiopasdfghjklzxcvbnm_-QWERTYUIOPASDGHJKLZXCVBNM';
//$charactersLength = strlen($characters);
//$name = '';
//for ($i = 0; $i < 10; $i++)
//    $name .= $characters[rand(0, $charactersLength - 1)];
file_put_contents('code.php', "<?php \nreturn '".$code."';");


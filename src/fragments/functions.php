<?php 

function generateCustomUid() {
    $letter = chr(random_int(97, 122)); // a-z
    $number = str_pad(strval(random_int(0, 99999)), 5, '0', STR_PAD_LEFT);

    return "ui{$letter}{$number}";
}

?>
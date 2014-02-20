<?php

/*
 *  methods to encrypt and decrypt using Base64
 * 
 */

function encrypt($string) {
    $key="addsj4w5893$34$&fd";
    $result = '';
    for($i=0; $i<strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key))-1, 1);
        $char = chr(ord($char)+ord($keychar));
        $result.=$char;
    }
  return base64_encode($result);
}

function decrypt($string) {
    $key="addsj4w5893$34$&fd";
    $result = '';
    $string = base64_decode($string);
    for($i=0; $i<strlen($string); $i++) {
        $char = substr($string, $i, 1);
        $keychar = substr($key, ($i % strlen($key))-1, 1);
        $char = chr(ord($char)-ord($keychar));
        $result.=$char;
    }

  return $result;
}
?>
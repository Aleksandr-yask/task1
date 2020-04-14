<?php
/**
 * Библиотека скриптов для проекта
 */
namespace MVC;
use MVC\Model;

class Library extends Model
{

    public function get($name) {
        if (!$_SESSION[$name]) {

        }
    }

    public function loadData($token) {

    }

    public function tokenChecker() {
        $data = $this->db->row('select * from pools where id = "1"');
        return $data;
    }

    // Кодировка данных
    public function decodeData($ciphertext, $enKey) {
        $c = base64_decode($ciphertext);
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = substr($c, 0, $ivlen);
        $hmac = substr($c, $ivlen, $sha2len=32);
        $ciphertext_raw = substr($c, $ivlen+$sha2len);
        $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $enKey, $options=OPENSSL_RAW_DATA, $iv);
        $calcmac = hash_hmac('sha256', $ciphertext_raw, $enKey, $as_binary=true);

        // выдается варн, на работу не влияет, работает - не лезь
        if (@hash_equals($hmac, $calcmac)) {
            return $plaintext;
        }
        return false;
    }

    // Декодировка данных
    public function encodeData($plaintext, $enKey) {
        $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $enKey, $options=OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertext_raw, $enKey, $as_binary=true);
        return base64_encode( $iv.$hmac.$ciphertext_raw );
    }

    // получение ключа
    public function getCode() {
        if (file_exists('code/code.php')) {
            $c = include 'code/code.php';
            if (!empty($c)) return $c;
            else return false;
        } else
            return false;

    }



}
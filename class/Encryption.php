<?php

class Encryption {

    private static $CYPHER = 'AES256';
    private static $IV = Config::PW_IV;   

    static function encrypt($sValue, $sSecretKey, $fixed_output = false) {
        $ivlen = openssl_cipher_iv_length(self::$CYPHER);
        if ($fixed_output) {
            $iv = self::$IV;
        } else {
            $iv = openssl_random_pseudo_bytes($ivlen);
        }
        $ciphertext_raw = openssl_encrypt($sValue, self::$CYPHER, $sSecretKey, 0, $iv);
        return str_replace('%', '=', urlencode(base64_encode($iv . $ciphertext_raw)));
    }

    static function decrypt($sValue, $sSecretKey) {
        $c = base64_decode(urldecode(str_replace('=', '%', $sValue)));
        $ivlen = openssl_cipher_iv_length(self::$CYPHER);
        if (strlen($c) < $ivlen) { 
            return false;
        }
        $iv2 = substr($c, 0, $ivlen);
        $ciphertext_raw2 = substr($c, $ivlen);
        return openssl_decrypt($ciphertext_raw2, self::$CYPHER, $sSecretKey, 0, $iv2);
    }

    static function encrypt_file($file, $password) {
        $contents = file_get_contents($file);
        if ($contents) {
            $contents = openssl_encrypt($contents, self::$CYPHER, $password, 0, self::$IV);
            if ($contents) {
                return $contents;
            }
        }
        return false;
    }

    static function decrypt_file($file_encrypted, $password) {
        $contents = file_get_contents($file_encrypted);
        if ($contents) {
            $contents = openssl_decrypt($contents, self::$CYPHER, $password, 0, self::$IV);
            if ($contents) {
                return $contents;
            }
        }
        return false;
    }

}

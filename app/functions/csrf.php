<?php
namespace functions;

/**
 * Class CSRF
 * @package functions
 */
class CSRF
{

    /**
     * @return string
     */
    public static function generate()
    {
        return $_SESSION['csrf'] = base64_encode(openssl_random_pseudo_bytes(32));
    }

    /**
     * @param $csrf
     *
     * @return bool
     */
    public static function check($csrf)
    {
        if (isset($_SESSION['csrf']) && $csrf === $_SESSION['csrf']) {
            unset($_SESSION['csrf']);

            return true;
        }

        return false;
    }

}
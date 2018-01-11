<?php
/**
 * Cross-Site Request Forgery (CSRF) class
 *
 * @author  Habib Hadi <me@habibhadi.com>
 */

namespace Hadi;

class Csrf {

    /**
     * Session name
     * 
     * @var string
     */
    public $tokenName = 'csrf_token';

    /**
     * Session expire at name
     * 
     * @var string
     */
    public $tokenExpireTimeName = 'csrf_token_expire_at';

    /**
     * Token expire time in min
     * 
     * @var integer
     */
    public $tokenExpireTime = 10;

    /**
     * Token GET or POST name
     * 
     * @var string
     */
    public $tokenFieldName = '_token';

    /**
     * CSRF header key name
     * 
     * @var string
     */
    public $tokenHeaderKeyName = 'X-CSRF-TOKEN';

    /**
     * Token
     * 
     * @var string
     */
    protected $token;

    /**
     * Token expire at 
     * 
     * @var string UNIX timestamp 
     */
    protected $tokenExpireAt;


    /**
     * Generate csrf token
     * 
     * @return $this
     */
    public function generateToken() 
    {
        $this->setToken()
            ->setTokenExpiration();

        return $this;
    }

    /**
     * Validate token by supplied token value
     * 
     * @param  string $token
     * @return boolean
     */
    public function validateToken($token = null)
    {
        $this->token = $token;

        if(!$this->isTokenMatched()) {
            return false;
        }

        if($this->isTokenExpired()) {
            return false;
        }

        if(!$this->isSameReferer()) {
            return false;
        }

        return true;
    }

    /**
     * Check if csrf request is valid
     * 
     * @return boolean
     */
    public function validRequest()
    {
        $token = $this->findTokenInRequest();

        if(!$token) {
            return false;
        }

        return $this->validateToken($token);
    }

    /**
     * Get csrf token value
     *
     * @return string
     */
    public function getToken()
    {
        if(!isset($_SESSION[$this->tokenName])) {
            $this->generateToken();
        }

        return $_SESSION[$this->tokenName];
    }

    /**
     * Generate and get token
     * 
     * @return string
     */
    public function token()
    {   
        return $this->generateToken()->token;
    }

    /**
     * Regenerate CSRF token
     * 
     * @return $this
     */
    public function reset()
    {
        return $this->generateToken();
    }

    /**
     * Delete current CSRF token
     * 
     * @return $this
     */
    public function deleteToken()
    {
        if(isset($_SESSION[$this->tokenName])) {
            unset($_SESSION[$this->tokenName]);
        }

        if(isset($_SESSION[$this->tokenExpireTimeName])) {
            unset($_SESSION[$this->tokenExpireTimeName]);
        }

        return $this;
    }

    /**
     * Set token expiration time
     * 
     * @param integer
     * @return $this
     */
    public function setTokenExpireTime($min)
    {
        $this->tokenExpireTime = $min;

        return $this;
    }


    /**
     * Set csrf token to session
     *
     * @return $this
     */
    protected function setToken()
    {
        if (function_exists('mcrypt_create_iv')) {
            $this->token = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
        } else {
            $this->token = bin2hex(openssl_random_pseudo_bytes(32));
        }

        $_SESSION[$this->tokenName] = $this->token;

        return $this;
    }

    /**
     * Set csrf token expiration time
     *
     * @return $this
     */
    protected function setTokenExpiration()
    {
        $this->tokenExpireAt = time() + ($this->tokenExpireTime * 60);

        $_SESSION[$this->tokenExpireTimeName] = $this->tokenExpireAt;

        return $this;
    }

    /**
     * Is token matched with stored token
     * 
     * @return boolean
     */
    protected function isTokenMatched()
    {
        if(!isset($_SESSION[$this->tokenName])) {
            return false;
        }

        return $this->token == $_SESSION[$this->tokenName];
    }

    /**
     * Check if token is expired or not
     * 
     * @return boolean
     */
    protected function isTokenExpired()
    {
        if(!isset($_SESSION[$this->tokenExpireTimeName])) {
            return false;
        }

        return $_SESSION[$this->tokenExpireTimeName] < time();
    }

    /**
     * Check if there is any token in POST, GET or Header request
     * 
     * @return string
     */
    protected function findTokenInRequest()
    {
        $token = null;

        if(isset($_POST[$this->tokenFieldName])) {
            $token = $this->sanitizeInput($_POST[$this->tokenFieldName]);
        }
        else if(isset($_GET[$this->tokenFieldName])) {
            $token = $this->sanitizeInput($_GET[$this->tokenFieldName]);
        }
        else {
            $header = getallheaders();

            if(isset($header[$this->tokenHeaderKeyName])) {
                $token = $this->sanitizeInput($header[$this->tokenHeaderKeyName]);
            }
        }

        return $token;
    }

    /**
     * Sanitize input string
     * 
     * @param $var string
     * @return string
     */
    protected function sanitizeInput($var)
    {
        return filter_var($var, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
    }

    /**
     * Check if request from same domain
     * 
     * @return boolean
     */
    protected function isSameReferer()
    {
        if ((isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']))) {
            if (strtolower(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)) != strtolower($_SERVER['HTTP_HOST'])) {
                return false;
            }
        }

        return true;
    }

}

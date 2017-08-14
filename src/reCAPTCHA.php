<?php

namespace Phelium\Component;

/**
 * reCAPTCHA v2 class
 * 
 * @author ShevAbam
 * @link https://github.com/shevabam/recaptcha
 * @license GNU GPL 2.0
 */
class reCAPTCHA
{
    /**
     * ReCAPTCHA URL verifying
     * 
     * @var string
     */
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Public key
     * 
     * @var string
     */
    private $siteKey;

    /**
     * Private key
     * 
     * @var string
     */
    private $secretKey;

    /**
     * Remote IP address
     * 
     * @var string
     */
    protected $remoteIp = null;

    /**
     * Supported themes
     * 
     * @var array
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected static $themes = array('light', 'dark');

    /**
     * Captcha theme. Default : light
     * 
     * @var string
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected $theme = null;

    /**
     * Supported types
     * 
     * @var array
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected static $types = array('image', 'audio');

    /**
     * Captcha type. Default : image
     * 
     * @var string
     * @see https://developers.google.com/recaptcha/docs/display#config
     */
    protected $type = null;

    /**
     * Captcha language. Default : auto-detect
     * 
     * @var string
     * @see https://developers.google.com/recaptcha/docs/language
     */
    protected $language = null;

    /**
     * Captcha size. Default : normal
     * 
     * @var string
     * @see https://developers.google.com/recaptcha/docs/display#render_param
     */
    protected $size = null;



    /**
     * Initialize site and secret keys
     * 
     * @param string $siteKey Site key from ReCaptcha dashboard
     * @param string $secretKey Secret key from ReCaptcha dashboard
     * @return void
     */
    public function __construct($siteKey = null, $secretKey = null)
    {
        $this->setSiteKey($siteKey);
        $this->setSecretKey($secretKey);
    }

    /**
     * Set site key
     * 
     * @param string $key
     * @return object
     */
    public function setSiteKey($key)
    {
        $this->siteKey = $key;

        return $this;
    }

    /**
     * Set secret key
     * 
     * @param string $key
     * @return object
     */
    public function setSecretKey($key)
    {
        $this->secretKey = $key;

        return $this;
    }

    /**
     * Set remote IP address
     * 
     * @param string $ip
     * @return object
     */
    public function setRemoteIp($ip = null)
    {
        if (!is_null($ip))
            $this->remoteIp = $ip;
        else
            $this->remoteIp = $_SERVER['REMOTE_ADDR'];

        return $this;
    }

    /**
     * Set theme
     *
     * @param string $theme (see https://developers.google.com/recaptcha/docs/display#config)
     * @return object
     */
    public function setTheme($theme = 'light')
    {
        if (in_array($theme, self::$themes))
            $this->theme = $theme;
        else
            throw new \Exception('Theme "'.$theme.'"" is not supported. Available themes : '.join(', ', self::$themes));

        return $this;
    }

    /**
     * Set type
     *
     * @param  string $type (see https://developers.google.com/recaptcha/docs/display#config)
     * @return object
     */
    public function setType($type = 'image')
    {
        if (in_array($type, self::$types))
            $this->type = $type;

        return $this;
    }

    /**
     * Set language
     *
     * @param  string $language (see https://developers.google.com/recaptcha/docs/language)
     * @return object
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Set size
     *
     * @param  string $size (see https://developers.google.com/recaptcha/docs/display#render_param)
     * @return object
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Generate the JS code of the captcha
     * 
     * @return string
     */
    public function getScript()
    {
        $data = array();
        if (!is_null($this->language))
            $data = array('hl' => $this->language);

        return '<script src="https://www.google.com/recaptcha/api.js?'.http_build_query($data).'"></script>';
    }

    /**
     * Generate the HTML code block for the captcha
     * 
     * @return string
     */
    public function getHtml()
    {
        if (!empty($this->siteKey))
        {
            $data = 'data-sitekey="'.$this->siteKey.'"';

            if (!is_null($this->theme))
                $data .= ' data-theme="'.$this->theme.'"';

            if (!is_null($this->type))
                $data .= ' data-type="'.$this->type.'"';

            if (!is_null($this->size))
                $data .= ' data-size="'.$this->size.'"';

            return '<div class="g-recaptcha" '.$data.'></div>';
        }
    }

    /**
     * Checks the code given by the captcha
     * 
     * @param string $response Response code after submitting form (usually $_POST['g-recaptcha-response'])
     * @return bool
     */
    public function isValid($response)
    {
        if (is_null($this->secretKey))
            throw new \Exception('You must set your secret key');
           
        if (empty($response))
            return false;

        $params = array(
            'secret'    => $this->secretKey,
            'response'  => $response,
            'remoteip'  => $this->remoteIp,
        );

        $url = self::VERIFY_URL.'?'.http_build_query($params);
        
        if (function_exists('curl_version'))
        {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
        }
        else
        {
            $response = file_get_contents($url);
        }
    
        if (empty($response) || is_null($response))
        {
            return false;
        }
    
        $json = json_decode($response);

        return $json->success;
    }
}
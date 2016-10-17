<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 17.10.2016
 * Time: 13:47
 */

namespace mpf\widgets\form\fields;


use mpf\web\helpers\Html;
use mpf\WebApp;
use mpf\widgets\form\Field;

class Recaptcha extends Field
{

    public $siteKey, $secretKey;

    /**
     * @return bool
     */
    public static function checkCaptcha()
    {
        if (defined('DEBUG_SERVER') && DEBUG_SERVER)
            return true;
        $f = new Recaptcha();
        $key = $f->secretKey;
        $r = json_decode(self::getURLContent('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $key,
            'response' => $_POST['g-recaptcha-response'],
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ]));
        return $r->success;
    }

    /**
     *
     * Returns url content using curl extension.
     * @param type $url
     * @return string
     */
    protected static function getURLContent($url, $post)
    {
        if (defined('DEBUG_SERVER') && DEBUG_SERVER)
            return "- captcha hidden on debug server -";
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($crl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($crl, CURLOPT_POST, count($post));
        curl_setopt($crl, CURLOPT_POSTFIELDS, http_build_query($post));
        $message = curl_exec($crl);
        curl_close($crl);
        return $message;
    }

    public function getInput()
    {
        return Html::get()->scriptFile('https://www.google.com/recaptcha/api.js')
        . '<div class="g-recaptcha" data-sitekey="' . $this->siteKey . '"></div>';

        $options = $this->htmlOptions;
        $options['class'] = (isset($options['class']) ? $options['class'] . ' ' : '') . $this->inputClass;
        return FormHelper::get()->input($this->getName(), $this->inputType, $this->getValue(), $options);
    }
}
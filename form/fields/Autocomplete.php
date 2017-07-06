<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 27.07.2015
 * Time: 11:31
 */

namespace mpf\widgets\form\fields;


use mpf\web\helpers\Form;
use mpf\web\helpers\Html;
use mpf\WebApp;
use mpf\widgets\form\Field;

class Autocomplete extends Field{

    /**
     * If set to true then it will search for options by using ajax; If not it will use the $optionsJSON param to get all options
     * @var bool
     */
    public $ajaxUpdate = false;
    /**
     * URL to be used by the ajax request to get the options; Result must be a json, same structure as optionsJSON;
     * It will receive a post "text" option as a parameter for search plus any additional parameters specified;
     * @var string
     */
    public $ajaxURL = "";

    /**
     * An optional list of parameters to send to ajax search;
     * @var array
     */
    public $ajaxExtraParams = [];

    /**
     * A JSON encoded string that contains a list with all possible options( used if ajaxUpdate is false )
     * @var string[]
     */
    public $options = [];

    /**
     * Minimum number of letters required to send an ajax request and search for results. This option is ignored if
     * ajaxUpdate is set to false.
     * @var int
     */
    public $minLettersToSearch = 3;

    /**
     * If set to true then it will allow for new values to be inserted; If not only the existing options are allowed;
     * @var bool
     */
    public $allowNewValues = false;

    /**
     * Get the input;
     * @return string
     */
    public function getInput() {
        $options = $this->htmlOptions;
        $options['class'] = (isset($options['class']) ? $options['class'] . ' ' : '') . 'input autocomplete';
        $options['autc_ajax'] = ($this->ajaxUpdate?'1':'0');
        $options['autc_url'] = $this->ajaxURL;
        $options['autc_for'] = str_replace(['[', ']'], '__', $this->getName());
        $options['autc_minletters'] = $this->minLettersToSearch;
        $options['autc_insert'] = ($this->allowNewValues?'1':'0');
        $this->ajaxExtraParams[WebApp::get()->request()->getCsrfKey()] = WebApp::get()->request()->getCsrfValue();
        $options['autc_extraparams'] = json_encode($this->ajaxExtraParams);
        $opts = [];
        foreach ($this->options as $word){
            $opts[] = "<li>$word</li>";
        }
        $opts = implode("", $opts);
        return Form::get()->hiddenInput($this->getName(), $this->getValue(), ['id' => str_replace(['[', ']'], '__', $this->getName())])
            . Form::get()->input("", "text", $this->getValue(), $options)
            . Html::get()->tag("div", "<ul>$opts</ul>", ['id' => 'options-for-' . $options['autc_for'], 'class' => 'form-autocomplete-list'])
            . Html::get()->scriptFile($this->form->getAssetsURL()  . 'autocomplete.js');
    }

}
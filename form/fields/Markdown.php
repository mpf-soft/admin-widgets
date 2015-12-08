<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 08.12.2015
 * Time: 11:47
 */

namespace mpf\widgets\form\fields;


use mpf\web\helpers\Form;
use mpf\web\helpers\Html;
use mpf\WebApp;
use mpf\widgets\form\Field;

class Markdown extends Field {

    public $hintText = "This input uses Markdown syntax. <a href='https://daringfireball.net/projects/markdown/syntax' target='_blank'>Click For Details</a>";

    /**
     * A POST with the text will be sent in an AJAX request and the returned content will be displayed.
     * @var
     */
    public $previewURL;

    /**
     * Get HTML Code for Input
     * @return string
     */
    public function getInput() {
        $this->htmlOptions['class'] = (isset($this->htmlOptions['class']) ? $this->htmlOptions['class'] . ' ' : '') . 'input markdown-input';
        $this->htmlOptions['ajax-url'] = $this->previewURL ?: WebApp::get()->request()->getCurrentURL();
        $this->htmlOptions['csrf-key'] = WebApp::get()->request()->getCsrfKey();
        $this->htmlOptions['csrf-value'] = WebApp::get()->request()->getCsrfValue();
        return Form::get()->textarea($this->getName(), $this->getValue(), $this->htmlOptions);
    }

    /**
     * @return string
     */
    public function getHint() {
        return Html::get()->tag("span", $this->hintText, ["class" => "markdown-hint"]);
    }

    /**
     * @return string
     */
    public function getPreview() {
        return Html::get()->tag("div", "", ["class" => "markdown-preview"]);
    }

    /**
     * @param string $original
     * @return string
     */
    public static function processText($original) {
        return \Michelf\Markdown::defaultTransform($original);
    }

    /**
     * Overwrite field getContent to add hints
     * @return string
     */
    public function getContent() {
        return parent::getContent() . $this->getHint() . $this->getPreview();
    }
}
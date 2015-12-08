<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 08.12.2015
 * Time: 11:47
 */

namespace mpf\widgets\form\fields;


use mpf\widgets\form\Field;

class Markdown extends Field{

    public $hintText = "This input uses Markdown syntax. <a href='https://github.com/michelf/php-markdown' target='_blank'>Click For Details</a>";

    /**
     * Get HTML Code for Input
     * @return string
     */
    public function getInput() {
        $this->htmlOptions['class'] = (isset($this->htmlOptions['class'])?$this->htmlOptions['class'].' ':'') . 'input';
        return Form::get()->textarea($this->getName(), $this->getValue(), $this->htmlOptions);
    }

    public function getHint(){

    }

    public function getPreview(){

    }

    /**
     * Overwrite field getContent to add hints
     * @return string
     */
    public function getContent() {
        return parent::getContent() . $this->getHint() . $this->getPreview();
    }
}
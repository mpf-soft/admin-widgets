<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 23.03.2015
 * Time: 15:12
 */

namespace mpf\widgets\form\fields;


use mpf\web\helpers\Form;
use mpf\widgets\form\Field;

class File extends Field{

    public function getInput() {
        $options = $this->htmlOptions;
        $options['class'] = (isset($options['class']) ? $options['class'] . ' ' : '') . $this->inputClass;
        return Form::get()->input($this->name, 'file', $this->getValue(), $options);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 24.10.2014
 * Time: 16:10
 */

namespace mpf\widgets\viewtable\columns;


use mpf\base\TranslatableObject;
use mpf\web\helpers\Html;

class Basic extends TranslatableObject {

    public $name;

    public $label;

    public $value;

    public $htmlOptions = [];

    public $headerHtmlOptions = [];

    public $rowHtmlOptions = [];

    /**
     * Can be 'input' or 'raw'. If type is raw then it will not apply any escape to it. If not it will use html_encode from Html helper.
     * @var string
     */
    public $type = 'input';

    /**
     * @var \mpf\widgets\viewtable\Table
     */
    public $table;

    public function display(){
        return Html::get()->tag('tr', $this->getRowContent(), $this->rowHtmlOptions);
    }

    protected function getRowContent(){
        return Html::get()->tag('th', $this->getLabel(), $this->headerHtmlOptions) . Html::get()->tag('td', $this->getValue(), $this->htmlOptions);
    }

    protected function getLabel(){
        return $this->translate($this->label?:$this->table->getLabel($this->name));
    }

    protected function getValue(){
        return $this->value?:$this->table->model->{$this->name};
    }

} 
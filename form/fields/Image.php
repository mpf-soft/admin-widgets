<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 23.03.2015
 * Time: 15:12
 */

namespace mpf\widgets\form\fields;


use mpf\web\helpers\Html;

class Image extends File{

    public $imageHtmlOptions = [];

    public $title;

    public $urlPrefix;

    public function getInput() {
        if ($this->getValue()) {
            $preview = Html::get()->image($this->urlPrefix .$this->getValue(), $this->title ? $this->title : $this->getLabel(), $this->imageHtmlOptions);
        } else {
            $preview = "";
        }
        return $preview .  parent::getInput();
    }
}
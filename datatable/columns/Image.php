<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 09.11.2015
 * Time: 11:23
 */

namespace mpf\widgets\datatable\columns;


use mpf\web\helpers\Html;
use mpf\widgets\datatable\Table;

class Image extends Basic {
    /**
     * Image URL. Can be a string or a callback with $row as a param.
     * @var string|callback
     */
    public $src;

    /**
     * Image Title. Can be a string or a callback with $row as a param.
     * @var string|callback
     */
    public $title;

    /**
     * If this param is set then the image will be included in a hyperlink that will send the user to this URL.
     * Can be a string or a callback with $row as a param.
     * @var string|callback
     */
    public $href;

    /**
     * HTML Options for the image.
     * @var array
     */
    public $imageHtmlOptions = [];

    /**
     * HTML Options for the link in case one is created( href param is specified )
     * @var array
     */
    public $linkHtmlOptions = [];


    /**
     * Returns column value for the specific row
     * @param string [string] $row
     * @param Table $table
     * @return string
     */
    public function getValue($row, Table $table) {
        $image = $this->getHTMLImage($row, $table);
        if (!$this->href) {
            return $image;
        }
        $href = "";
        if (is_string($this->href)) {
            eval("\$href = {$this->href}");
        } elseif (is_callable($this->href)) {
            $href = call_user_func($this->href, $row, $table);
        }
        return ("" != ($r = Html::get()->link($href, $image, $this->linkHtmlOptions))) ? $r : $image;
    }

    /**
     * @param $row
     * @param Table $table
     * @return string
     */
    protected function getHTMLImage($row, Table $table) {
        $url = $title = "";
        if (is_string($this->src)) {
            eval("\$url = {$this->src}");
        } elseif (is_callable($this->src)) {
            $url = call_user_func($this->src, $row, $table);
        }
        if (is_string($this->title)) {
            eval("\$title = {$this->title}");
        } elseif (is_callable($this->title)) {
            $title = call_user_func($this->title, $row, $table);
        }
        return Html::get()->image($url, $title, $this->imageHtmlOptions);
    }

}
<?php

/*
 * @author Mirel Nicu Mitache <mirel.mitache@gmail.com>
 * @package MPF Framework
 * @link    http://www.mpfframework.com
 * @category core package
 * @version 1.0
 * @since MPF Framework Version 1.0
 * @copyright Copyright &copy; 2011 Mirel Mitache 
 * @license  http://www.mpfframework.com/licence
 * 
 * This file is part of MPF Framework.
 *
 * MPF Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MPF Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MPF Framework.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace mpf\widgets\datatable\columns\actions;

use mpf\base\TranslatableObject;
use mpf\web\AssetsPublisher;
use mpf\web\helpers\Html;
use mpf\widgets\datatable\Table;

/**
 * This is the basic action for "Actions" column. There are some more defined
 * that will extend this one.
 *
 * @author mirel
 */
class Basic extends TranslatableObject {

    /**
     * A template for how URL will be generated
     * Example : "data/edit/{$row->id}"
     * @var string
     */
    public $url = '#';

    /**
     * False or list of values to be sent in post request
     * @var bool|array
     */
    public $post = false;

    /**
     * Full URL to icon if one is to be used
     * @var string
     */
    public $icon;

    /**
     * PHP code to eval that returns true or false in case this button must be
     * hidden for some values. It can also have a boolean value that it won't be
     * executed by eval but used as it is.
     * @var string/boolean
     */
    public $visible = true;

    /**
     * Leave it to null for no confirmation or a string in case a confirmation
     * message must be used. If string it's set then it will use javascript "confirm"
     * to confirm before submitting the action.
     * @var string
     */
    public $confirmation;

    /**
     * Instead of an URL a JS method name can be set. It will get 3 params:
     *  - row ID
     *  - action Name
     *  - current DOM element (this)
     * @var string
     */
    public $jsAction;

    /**
     * Name of the current action.
     * @var string
     */
    public $name;

    /**
     * List of HTML Optiosn for current action;
     * @var string[string]
     */
    public $htmlOptions = array();

    /**
     * Label to be used for current action.
     * @var string
     */
    public $label = "";

    /**
     * HTML Title to be used when hovering over the button. PHP Eval code is
     * required for this param.
     * @var string
     */
    public $title = "";

    /**
     * From assets/icons folders. There are 4 different sizes: 16 / 24 / 32 / 48.
     * @var int
     */
    public $iconSize = 16;

    public function getTopString(Table $table){
        $options = $this->htmlOptions;
        $options['title'] = $this->title;
        if ($this->confirmation && (!$this->post)) {
            if ($this->jsAction) {
                $options['onclick'] = "if (confirm('{$this->confirmation}')) {$this->jsAction}(0, '{$this->name}', this);";
            } else {
                $options['onclick'] = "return confirm('{$this->confirmation}');";
            }
        } elseif ($this->jsAction) {
            $options['onclick'] = "return {$this->jsAction}(0, '{$this->name}', this);";
        }
        $url = $this->url;
        if (false !== $this->post && is_array($this->post)) {
            $options['class'] = isset($options['class']) ? $options['class'] . ' mdata-table-post-link' : 'mdata-table-post-link';
            $options['post-data'] = json_encode($this->post);
            if ($this->confirmation){
                $options['post-confirmation'] = $this->confirmation;
            }
        }
        $icon = $this->getIcon($options['title'], $table);
        return Html::get()->link($url, $icon . $this->label, $options);
    }

    /**
     * Final result, a HTML Element for current action;
     * @param Model $row
     * @param \mpf\widgets\datatable\Table $table
     * @return string
     */
    public function getString($row, Table $table) {
        if (!$this->isVisible($row)) {
            return "";
        }
        $options = $this->htmlOptions;
        if ($this->title != "") {
            eval("\$options['title'] = {$this->title};");
        } elseif (!isset($options['title'])) {
            $options['title'] = '';
        }
        if ($this->confirmation && (!$this->post)) {
            if ($this->jsAction) {
                $options['onclick'] = "if (confirm('{$this->confirmation}')) {$this->jsAction}({$row->{$this->dataProvider->getPkKey()}}, '{$this->name}', this);";
            } else {
                $options['onclick'] = "return confirm('{$this->confirmation}');";
            }
        } elseif ($this->jsAction) {
            $options['onclick'] = "return {$this->jsAction}({$row->id}, '{$this->name}', this);";
        }

        if ('#' != $this->url) {
            eval("\$url = {$this->url};");
        } else {
            $url = '#';
        }
        if (false !== $this->post && is_array($this->post)) {
            $options['class'] = isset($options['class']) ? $options['class'] . ' mdata-table-post-link' : 'mdata-table-post-link';
            $parsed = array();
            foreach ($this->post as $name=>$value){
                eval('$value = '. $value.';');
                if ('{{modelKey}}' == $name){
                    $name = $table->dataProvider->filtersKey;
                }
                $parsed[$name] = $value;
            }
            $options['post-data'] = json_encode($parsed);
            if ($this->confirmation){
                $options['post-confirmation'] = $this->confirmation;
            }
        }
        $icon = $this->getIcon($options['title'], $table);
        return Html::get()->link($url, $icon . $this->label, $options);
    }

    /**
     * Get HTML img tag for icon or an empty string if there is no icon
     * @param string $title
     * @param \mpf\widgets\datatable\Table $table
     * @return string
     */
    public function getIcon($title, Table $table) {
        if ($this->icon) {
            $icon = str_replace(array('%DATATABLE_ASSETS%', '%SIZE%'), array($table->getAssetsURL(), $this->iconSize . 'x' . $this->iconSize), $this->icon);
            if ('%MPF_ASSETS%' == substr($icon, 0, 12)) {
                $icon = AssetsPublisher::get()->mpfAssetFile(substr($icon, 12));
            }
            return Html::get()->image($icon, $title);
        }
        return '';
    }

    /**
     * Checks if action is visible or not
     * @return boolean
     */
    public function isVisible($row) {
        if (false === $this->visible) // check if it's visible
            return false;
        elseif (is_string($this->visible)) {
            $visible = true;
            eval("\$visible = {$this->visible};");
            if (false == $visible)
                return false;
        }
        return true;
    }

}

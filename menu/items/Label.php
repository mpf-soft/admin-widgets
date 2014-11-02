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

namespace mpf\widgets\menu\items;

/**
 * Description of Label
 *
 * @author mirel
 */
class Label extends \mpf\base\TranslatableObject {

    /**
     * Label to be used for link. It will be automatically translated
     * @var string
     */
    public $label;

    /**
     * URL to icon to be set for item. Optional.
     * @var string
     */
    public $icon;

    /**
     * List of  subitems for current item.
     * @var Link[]
     */
    public $items = array();

    /**
     * Set it to visible or hidden;
     * @var boolean
     */
    public $visible = true;

    /**
     * List of HTML options for current item.
     * @var string[string]
     */
    public $htmlOptions = array();

    /**
     * Returns current item as HTML Code
     * @return string
     */
    public function display() {
        if (!$this->isVisible())
            return "";

        $content = \mpf\web\helpers\Html::get()->tag('span', $this->getIcon() . $this->translate($this->label));
        $submenu = "";
        if (count($this->items)) {
            $anySelected = false;
            $anyVisible = false;
            foreach ($this->items as $item) {
                /* @var $item Label */
                $submenu .= $item->display();
                $anySelected = $anySelected || $item->isSelected();
                $anyVisible = $anyVisible || $item->isVisible();
            }
            if ($anyVisible)
                $submenu = \mpf\web\helpers\Html::get()->tag('ul', $submenu);
            else
                $submenu = "";
            if ($anySelected) {
                if (isset($this->htmlOptions['class'])) {
                    $this->htmlOptions['class'] .= ' selected';
                } else {
                    $this->htmlOptions['class'] = 'selected';
                }
            }
        }
        return \mpf\web\helpers\Html::get()->tag('li', $content . $submenu, $this->htmlOptions);
    }

    public function getIcon() {
        if (!$this->icon)
            return "";
        return \mpf\web\helpers\Html::get()->image($this->icon, $this->translate($this->label));
    }

    /**
     * Labels can't be selected as they are not pages;
     * @return boolean
     */
    public function isSelected() {
        return false;
    }

    public function isVisible() {
        return $this->visible;
    }

}

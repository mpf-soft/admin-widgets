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
use mpf\base\TranslatableObject;

/**
 * Description of Link
 *
 * @author mirel
 */
class Link extends TranslatableObject {

    /**
     * Link that will be added to item. It can be a string or an array with
     * the following:
     *  array( controller,  [action,]  [params,] [module])
     * 
     * @var string|array
     */
    public $url = '#';

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
    public $items = [];

    /**
     * Set it to visible or hidden;
     * @var boolean
     */
    public $visible = true;

    /**
     * List of HTML options for current item.
     * @var string[string]
     */
    public $htmlOptions = [];

    /**
     * Html Options for link element
     * @var array
     */
    public $linkHtmlOptions = [];

    /**
     * Returns current item as HTML Code
     * @return string
     */
    public function display() {
        if (!$this->isVisible()) { //return nothing if it's not visible
            return "";
        }
        $content = \mpf\web\helpers\Html::get()->link($this->getURL(), $this->getIcon() . $this->translate($this->label), $this->linkHtmlOptions);
        $submenu = "";
        $anySelected = false;
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

        if ($anySelected || $this->isSelected()) {
            $this->htmlOptions['class'] = isset($this->htmlOptions['class']) ? $this->htmlOptions['class'] . ' selected' : 'selected';
        }

        return \mpf\web\helpers\Html::get()->tag('li', $content . $submenu, $this->htmlOptions);
    }

    /**
     * Get HTML image for selected icon
     * @return string
     */
    public function getIcon() {
        if (!$this->icon) {
            return "";
        }
        return \mpf\web\helpers\Html::get()->image($this->icon, $this->translate($this->label));
    }

    /**
     * Get string url from array|string input
     * @return string
     */
    public function getURL() {
        if (is_string($this->url)) {
            return $this->url;
        }
        return \mpf\WebApp::get()->request()->createURL(isset($this->url[0]) ? $this->url[0] : 'home', isset($this->url[1]) ? $this->url[1] : null, isset($this->url[2]) ? $this->url[2] : array(), isset($this->url[3]) ? $this->url[3] : ((isset($this->url[2]) && is_string($this->url[2]))?$this->url[2]:null));
    }

    /**
     * Check if this is the current page.
     * @return boolean
     */
    public function isSelected() {
        if (is_string($this->url) && $this->url == \mpf\WebApp::get()->request()->getCurrentURL()) {
            return true;
        } elseif (is_array($this->url)) {
            $controller = isset($this->url[0]) ? $this->url[0] : 'home';
            if ($controller != \mpf\WebApp::get()->request()->getController()) {
                return false;
            }

            if ($this->getModule() != \mpf\WebApp::get()->request()->getModule()) {
                return false; // different module;
            }
            if (\mpf\WebApp::get()->getController()) {
                $action = isset($this->url[1]) ? $this->url[1] : \mpf\WebApp::get()->getController()->defaultAction;
                return ($action == \mpf\WebApp::get()->getController()->getActiveAction());
            }
        }
        return false;
    }

    /**
     * Get name of the link module;
     * @return string
     */
    protected function getModule() {
        $module = \mpf\WebApp::get()->request()->getModule();
        if (isset($this->url[2]) && !is_array($this->url[2])) { // is module
            $module = $this->url[2];
        } elseif (isset($this->url[3]) && !is_array($this->url[3])) { // this is module then
            $module = $this->url[3];
        }
        return $module;
    }

    /**
     * Check if current item is visible or not.
     * @return boolean
     */
    public function isVisible() {
        if ((!is_array($this->url)) || (!$this->visible)) { // if it's not array, or not visible then just return visible value, no need to check anything
            return $this->visible;
        }
        $controller = isset($this->url[0]) ? $this->url[0] : 'home';
        $action = isset($this->url[1]) ? $this->url[1] : null;
        if (false !== strpos($controller, '/')){
            list ($module, $controller) = explode('/', $controller, 2);
            if ('..' == $module){
                $module = '/';
            }
        } else {
            $module = (isset($this->url[2]) && is_string($this->url[2]))?$this->url[2]:(isset($this->url[3])?$this->url[3]:null);
        }
        if (\mpf\WebApp::get()->accessMap && (!\mpf\WebApp::get()->accessMap->canAccess($controller, $action, $module))) {
            return false;
        }
        return true;
    }

}

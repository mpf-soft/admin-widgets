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

namespace mpf\widgets\menu;
use mpf\web\helpers\Html;

/**
 * Description of Menu
 *
 * @author mirel
 */
class Menu extends \mpf\base\Widget {
    
    /**
     * Folder name where all plugin assests are found. Is made an attribute
     * so that it can be changed by any class that extends this plugin, or even
     * by config.
     * @var string
     */
    protected $assetsFolder = 'assets';
    
    /**
     * URL to assets folder
     * @var string
     */
    protected $assetsURL;

    /**
     * Theme name for menu;
     * @var string
     */
    public $theme = 'default-dropdown';

    /**
     * 
     * @var string[string]
     */
    public $htmlOptions = array();

    /**
     * Menu links.
     * @var string
     */
    public $items = array();

    /**
     * List of instatiated items;
     * @var \mpf\widgets\menu\items\Link[]
     */
    protected $instantiatedItems = array();

    /**
     * Update this if you want to use a different dropdown icon
     * @var string
     */
    public $dropDownIcon;

    public function init($config = array()) {
        $this->instantiatedItems = $this->loadItems($this->items);
        return parent::init($config);
    }

    protected function loadItems($items) {
        $result = array();
        foreach ($items as $item) {
            $class = isset($item['class']) ? $item['class'] : 'Link';
            if (false === strpos($class, '\\')) {
                $class = '\\mpf\widgets\\menu\\items\\' . $class;
            }
            unset($item['class']);
            $obj = new $class($item);
            /* @var $obj \mpf\widgets\menu\items\Link */
            $obj->items = $this->loadItems(isset($item['items']) ? $item['items'] : array());
            $result[] = $obj;
        }
        return $result;
    }

    /**
     * 
     */
    public function display() {
        $this->assetsURL = \mpf\web\AssetsPublisher::get()->publishFolder(__DIR__ . DIRECTORY_SEPARATOR . $this->assetsFolder);
        echo \mpf\web\helpers\Html::get()->cssFile($this->assetsURL . 'style.css');
        $content = '';
        foreach ($this->instantiatedItems as $item) {
            /* @var $item \mpf\widgets\menu\items\Link */
            $content .= $item->display();
        }
        if (!isset($this->htmlOptions['class'])) {
            $this->htmlOptions['class'] = 'm-menu m-menu-' . $this->theme;
        } else {
            $this->htmlOptions['class'] = $this->htmlOptions['class'] . ' m-menu m-menu-' . $this->theme;
        }
        $menu = \mpf\web\helpers\Html::get()->tag('div', \mpf\web\helpers\Html::get()->tag('ul', $content, array('class' => 'm-menu-main-menu')), $this->htmlOptions);
        $menu .= Html::get()->script("
    $('li.m-menu-dropdown>a, li.m-menu-dropdown>span').click(function(e){
        if ($(this.parentNode).hasClass('dropdownvisible')){
            $(this.parentNode).removeClass('dropdownvisible');
        } else {
            $('li', this.parentNode.parentNode).removeClass('dropdownvisible');
            $(this.parentNode).addClass('dropdownvisible');
        }
        e.preventDefault();
        return false;
    });
    $(document).click(function(){
        $('li').removeClass('dropdownvisible');
    });
        ");
        echo $menu;
    }

}

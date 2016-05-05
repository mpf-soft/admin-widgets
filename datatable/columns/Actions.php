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

namespace mpf\widgets\datatable\columns;
use mpf\widgets\datatable\Table;

/**
 * Description of Actions
 *
 * @author mirel
 */
class Actions extends Basic {

    /**
     * List of buttons. 
     * @var string[]
     */
    public $buttons = array();
    /**
     * List of actions for header column
     * @var string[]
     */
    public $topButtons = array();
    protected $instancedButtons = array();
    protected $instancedTopButtons = array();
    
    /**
     * Can't order by actions;
     * @var boolean
     */
    public $order = false;

    public function init($config) {
        foreach ($this->buttons as $name => $details) {
            $class = isset($details['class']) ? $details['class'] : 'Basic';
            if (false === strpos($class, '\\'))
                $class = '\\mpf\widgets\\datatable\\columns\\actions\\' . $class;
            $details['name'] = $name;
            unset($details['class']);
            $this->instancedButtons[$name] = new $class($details);
        }
        foreach ($this->topButtons as $name => $details){
            $class = isset($details['class']) ? $details['class'] : 'Basic';
            if (false === strpos($class, '\\'))
                $class = '\\mpf\widgets\\datatable\\columns\\actions\\' . $class;
            $details['name'] = $name;
            unset($details['class']);
            $this->instancedTopButtons[$name] = new $class($details);
        }
        return parent::init($config);
    }

    /**
     * Get header label for actions column
     * @return string
     */
    function getLabel() {
        if (!$this->label) {
            return ""; // will return "" instead of column name
        }
        return $this->label; // except when it's defined otherwise (for example to add a link there or something)
    }

    /**
     * There is no filter for Actions column
     * @return string
     */
    function getFilter() {
        return "&nbsp;";
    }

    function getValue($row, $table) {
        $result = '';
        foreach ($this->instancedButtons as $button){
            /* @var $button \mpf\widgets\datatable\columns\actions\Basic */
            $result .= ' ' . $button->getString($row, $table);
        }
        return $result?$result:'&nbsp;';
    }

    public function getHeaderCode(Table $table){
        $result = '';
        foreach ($this->instancedTopButtons as $button){
            /* @var $button \mpf\widgets\datatable\columns\actions\Basic */
            $result .= ' ' . $button->getTopString($table);
        }
        return $result?$result:'&nbsp;';
    }
}

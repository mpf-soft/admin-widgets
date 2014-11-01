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

namespace mWidgets\datatable\columns\actions;

/**
 * Description of Delete
 *
 * @author mirel
 */
class Delete extends Basic{
    //put your code here
    public $title = '"Delete"';

    public $icon = '%MPF_ASSETS%images/oxygen/%SIZE%/actions/edit-delete.png';

    public $post = array('{{modelKey}}' => '$row->id');
    
    public $url = "\\mpf\\WebApp::get()->request()->createURL(\\mpf\\WebApp::get()->request()->getController(), 'delete')";
    
    public function init($config = array()) {
        if (!$this->confirmation){
            $this->confirmation = $this->translate('Are you sure you want to delete this?');
        }
        parent::init($config);
    }
}

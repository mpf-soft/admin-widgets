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

namespace mpf\widgets\form\fields;

use mpf\web\helpers\Form;
use mpf\web\helpers\Html;
use mpf\WebApp;

/**
 * Description of BirthDate
 *
 * @author Mirel Mitache
 */
class BirthDate extends \mpf\widgets\form\Field {
    public $minAge = 5;

    public $maxAge = 140;

    public $defaulAge = 18;

    //put your code here
    function getInput() {
        $years = [];
        for ($i = date('Y') - $this->maxAge; $i <= date('Y') - $this->minAge; $i++) {
            $years[$i] = $i;
        }
        $value = $this->getValue();
        WebApp::get()->debug($value);
        $year = substr($value, 0, 4);
        $monthV = substr($value, 5, 2);
        $day = substr($value, 8, 2);
        $this->htmlOptions['class'] = isset($this->htmlOptions['class']) ? $this->htmlOptions['class'] . ' ' : '';
        $this->htmlOptions['class'] .= 'birthday';

        $htmlOptions = $this->htmlOptions;
        $htmlOptions['class'] .= ' bday-year';
        $r = Form::get()->select('', $years, $year, $htmlOptions);

        $mths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $months = [];
        foreach ($mths as $k => $month) {
            $months[$k < 10 ? "0" . ($k + 1) : $k+1] = $this->translate($month);
        }
        $htmlOptions = $this->htmlOptions;
        $htmlOptions['class'] .= ' bday-month';
        $r .= Form::get()->select('', $months, $monthV, $htmlOptions);
        $days = [];
        for ($i = 1; $i <= 31; $i++) {
            $days[$i < 10 ? "0" . $i : $i] = $i < 10 ? "0" . $i : $i;
        }
        $htmlOptions = $this->htmlOptions;
        $htmlOptions['class'] .= ' bday-day';
        $r .= Form::get()->select('', $days, $day, $htmlOptions);
        return $r . Form::get()->hiddenInput($this->getName(), $this->getValue(), array('class' => 'birthday_value'));
    }

    public function getValue() {
        $parent = parent::getValue();
        return $parent ? $parent : date('Y-m-d', strtotime('-' . $this->defaulAge . ' years'));
    }
}

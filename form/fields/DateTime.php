<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 13.10.2016
 * Time: 10:00
 */

namespace mpf\widgets\form\fields;


use mpf\web\helpers\Form;
use mpf\widgets\form\Field;

class DateTime extends Field
{

    /**
     * Date format.
     * @var string
     */
    public $format = 'Y-m-d';

    /**
     * Display hour select
     * @var bool
     */
    public $hour = true;

    /**
     * Display minute select
     * @var bool
     */
    public $minute = true;


    /**
     * Display second select;
     * @var bool
     */
    public $second = false;

    //put your code here
    function getInput()
    {

        if ($this->getValue()) {
            $value = is_numeric($this->getValue()) ? $this->getValue() : strtotime($this->getValue());
        } else {
            $value = time();
        }

        $this->inputClass .= ' datetimeinput';

        $format = str_replace(['Y', 'm', 'd'], ['yy', 'mm', 'dd'], $this->format);
        $this->htmlOptions['class'] = (isset($this->htmlOptions['class']) ? $this->htmlOptions['class'] . ' ' : '') . $this->inputClass . ' datetime-date';
        $r = Form::get()->date($this->getName() . '[date]', date($this->format, $value), $format, $this->htmlOptions);

        $hours = range(0, 23);
        $seconds = $minutes = range(0, 59);
        if ($this->hour)
            $r .= Form::get()->select($this->getName() . '[hour]', $hours, date('H', $value), ['class' => $this->inputClass . ' datetime-hour']);
        else
            $r .= Form::get()->hiddenInput($this->getName() . '[hour]', (int)date('H', $value), ['class' => 'datetime-hour']);

        if ($this->minute)
            $r .= Form::get()->select($this->getName() . '[minute]', $minutes, date('i', $value), ['class' => $this->inputClass . ' datetime-minute']);
        else
            $r .= Form::get()->hiddenInput($this->getName() . '[minute]', (int)date('i', $value), ['class' => 'datetime-minute']);

        if ($this->second)
            $r .= Form::get()->select($this->getName() . '[second]', $seconds, date('s', $value), ['class' => $this->inputClass . ' datetime-second']);
        else
            $r .= Form::get()->hiddenInput($this->getName() . '[second]', (int)date('s', $value), ['class' => 'datetime-second']);

        return $r . Form::get()->hiddenInput($this->getName(), date($this->format . ' H:i:s', $value), ['class' => 'datetime-value']);
    }
}
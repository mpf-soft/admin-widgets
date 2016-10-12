<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 10.10.2016
 * Time: 16:09
 */

namespace mpf\widgets\form\fields;

use mpf\web\helpers\Html;
use mpf\widgets\form\Field;

class ModelRelationField extends Field
{
    protected static $jsPublished = false;

    public $contentTag = 'fieldset';
    public $legend = true;
    public $legendLabel;
    public $multiple = true;
    public $fields = [];
    public $modelClass;
    public $newKeysSuffix = '_';
    public $addButtonLabel = '+';
    public $remButtonLabel = '-';

    public $buttonsContainerTag = 'div';
    public $buttonsContainerClass = 'model-relations-buttons';

    public $allRelationsContainerClass = 'model-relations-container';

    /**
     * {action} will be replaced by "add" or "rem" for each action
     * @var string
     */
    public $buttonsClass = "{action}-relation-button relations-button";


    /**
     * @return string
     */
    protected function jsFunctions($baseName)
    {
        if (self::$jsPublished)
            return "";
        self::$jsPublished = true;
        $htmlCode = str_replace("\n", " ", str_replace('"', '\"', $this->getSingleInput([], '_REPL_ME_KEY_', $baseName)));
        $f = <<<GEN
var FormModelRelationField_CurrentKeyCount = 1;        
function FormModelRelationField_AddField(fieldset){
    var keySuffix = '{$this->newKeysSuffix}';
    var code = "$htmlCode".replace('_REPL_ME_KEY_', keySuffix + FormModelRelationField_CurrentKeyCount);
    FormModelRelationField_CurrentKeyCount = FormModelRelationField_CurrentKeyCount + 1;
    $(fieldset.parentNode).append(code);
    return false;
}

function FormModelRelationField_RemField(fieldset){
    $(fieldset).remove();
    return false;
}

GEN;
        return Html::get()->script($f);
    }

    /**
     * @param $value
     * @param $key
     * @param $baseName
     * @return string
     */
    protected function getSingleInput($value, $key, $baseName)
    {
        $c = $this->modelClass;
        $labels = $c::getLabels();
        if ($this->multiple)
            $baseName .= "[$key]";
        $fields = [];
        foreach ($this->fields as $field) {
            $name = is_string($field) ? $field : $field['name'];
            $label = $this->translate(isset($labels[$name]) ? $labels[$name] : ucwords(str_replace('_', ' ', $name)));
            $name = $baseName . "[" . $name . "]";
            if (is_string($field)) {
                $field = new Text(['name' => $name, 'label' => $label, 'value' => isset($value[$name]) ? $value[$name] : '']);
                $fields[] = $field->display($this->form);
            } elseif (is_array($field)) {
                $class = isset($field['type']) ? ucfirst($field['type']) : 'Text';
                $class = (false === strpos($class, '\\')) ? '\\mpf\\widgets\\form\\fields\\' . $class : $class;
                unset($field['type']);
                if (!isset($field['label']))
                    $field['label'] = $label;
                $field['value'] = isset($value[$name]) ? $value[$name] : (isset($field['defaultValue']) ? $field['defaultValue'] : '');
                $field['name'] = $name;
                $field = new $class($field);
                /* @var $field \mpf\widgets\form\Field */
                $fields[] = $field->display($this->form);
            }
        }

        $r = ($this->legend ? Html::get()->tag('legend', str_replace('_KEY_', $key, $this->legendLabel ?: $this->getLabel())) : '') . implode("\n", $fields);
        $r .= $this->getButtons();

        return Html::get()->tag($this->contentTag, $r);
    }

    public function getButtons()
    {
        $r = Html::get()->link('#', $this->addButtonLabel, ['class' => str_replace('{action}', 'add', $this->buttonsClass), 'onclick' => "return FormModelRelationField_AddField(this.parentNode.parentNode)"]);
        $r .= Html::get()->link('#', $this->remButtonLabel, ['class' => str_replace('{action}', 'rem', $this->buttonsClass), 'onclick' => "return FormModelRelationField_RemField(this.parentNode.parentNode)"]);
        return Html::get()->tag($this->buttonsContainerTag, $r, ['class' => $this->buttonsContainerClass]);
    }

    /**
     * Get HTML Code for Input
     * @return string
     */
    function getInput()
    {
        $value = $this->getValue();
        $r = '';

        $baseName = $this->getName();
        if ($this->form->model) {
            $shortClass = get_class($this->form->model);
            $shortClass = explode('\\', $shortClass);
            $shortClass = $shortClass[count($shortClass) - 1];
            $baseName = substr($this->getName(), strlen($shortClass) + 1);
            $baseName = implode('', explode(']', $baseName, 2));

        }

        if ($value) {
            foreach ($value as $k => $val) {
                $r .= $this->getSingleInput($val, $k, $baseName);
            }
        } else {
            $r .= $this->getSingleInput([], $this->newKeysSuffix . '0', $baseName);
        }

        return Html::get()->tag('div', $r, ['class' => $this->allRelationsContainerClass]) . $this->jsFunctions($baseName);
    }

    public function getValue()
    {
        return parent::getValue();
    }
}
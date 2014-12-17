<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 17.12.2014
 * Time: 12:14
 */

namespace mpf\widgets\form\fields;


use mpf\web\helpers\Form;
use mpf\web\helpers\Html;
use mpf\widgets\form\Field;

class SeoKeywords extends Field {

    protected static $published = false;

    public function getInput() {
        $r = Form::get()->hiddenInput($this->getName(), $this->getValue(), ['class'=>'keywords-hidden-input']);
        $r .= Html::get()->tag('div', '', ['class' => 'keywords-list-input']);
        $this->htmlOptions['class'] = (isset($this->htmlOptions['class']) ? $this->htmlOptions['class'] . ' ' : '') . $this->inputClass . ' keywords-visible-input';
        $r .= Form::get()->input('', 'text', '', $this->htmlOptions);
        if (!self::$published) {
            $r .= $this->getScripts() . $this->getStyles();
            self::$published = true;
        }
        return $r;
    }


    /**
     * @return string
     */
    public function getScripts() {
        $script = <<<SCRIPT
\$(document).ready(function() {
    $('.keywords-visible-input').each(function(){
        // 1. get old values
        var _parent = this.parentNode;
        var old = $('.keywords-hidden-input', this.parentNode).val().split(', ');
        $.each(old, function(index, value){
            addKeyWordToList(value, _parent);
        });
        // 2. make it ready for new words
        $(this).keypress(function(event){
            if (event.keyCode == 13){
                var word = $(this).val();
                var old = $('.keywords-hidden-input', this.parentNode).val().split(', ');
                var alreadyFound = false;
                for (var i =0; i< old.length; i++){
                    if (old[i] == word) {
                        alreadyFound = true;
                    }
                }
                if (!alreadyFound){
                   old[old.length] = word;
                   $('.keywords-hidden-input', this.parentNode).val(old.join(', '));
                   addKeyWordToList(word, this.parentNode);
                }

                $(this).val('');
                return false;
            }
        });
    });
});

function addKeyWordToList(word, parent){
    $('<a>').addClass('keyword-word-box').html('<span>' + word + '</span><a class="keyword-remove" onclick="return removeKeyWordFromList(this.parentNode);">x</a>')
            .appendTo($('.keywords-list-input', parent));
}

function removeKeyWordFromList(element){
    var word = $('span', element).text();
    $(element).remove();
    var old = $('.keywords-hidden-input', element.parentNode.parentNode).val().split(', ');
    var new = [];
    for (var i = 0;i < old.length; i++){
        if (old[i] != word) {
            new[new.length] = old[i];
        }
    }
    $('.keywords-hidden-input', element.parentNode.parentNode).val(new.join(', '));
}
SCRIPT;

        return Html::get()->script($script);
    }

    /**
     * @return string
     */
    public function getStyles() {
        $style = <<<STYLE

STYLE;

        return Html::get()->css($style);
    }
}
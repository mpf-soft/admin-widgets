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

    public $separator = ', ';

    public $deleteLabel = 'x';

    /**
     * @var string
     * Value can be set to string or array. If is array then a list of keywords will be returned; Also separator value won't be used.
     */
    public $valueAs = 'string';

    public function getInput() {
        if (!in_array($this->valueAs, ['string', 'array'])){
            trigger_error("Invalid option for `valueAs`! Can only be string or array!");
            return "";
        }
        if ('string' == $this->valueAs) {
            $r = Form::get()->hiddenInput($this->getName(), $this->getValue(), ['class' => 'keywords-hidden-input']);
        } else {
            $r = Form::get()->select($this->getName(), $this->getValue(), $this->getValue(), ['class' => 'keywords-hidden-input', 'multiple' => 'multiple']);
        }
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
        var old = keyWordsGetOld(_parent);
        $.each(old, function(index, value){
            if (value) {
                addKeyWordToList(value, _parent);
            }
        });
        // 2. make it ready for new words
        $(this).keypress(function(event){
            if (event.keyCode == 13){
                var word = $(this).val();
                var old = keyWordsGetOld(_parent);
                var alreadyFound = false;
                for (var i =0; i< old.length; i++){
                    if (old[i] == word) {
                        alreadyFound = true;
                    }
                }
                if (!alreadyFound){
                   old[old.length] = word;
                   keyWordsSetNew(old, _parent);
                   addKeyWordToList(word, this.parentNode);
                }

                $(this).val('');
                return false;
            }
        });
    });
});

function addKeyWordToList(word, parent){
    $('<a>').addClass('keyword-word-box').html('<span>' + word + '</span><a class="keyword-remove" onclick="return removeKeyWordFromList(this.parentNode);">{$this->deleteLabel}</a>')
            .appendTo($('.keywords-list-input', parent));
}

function removeKeyWordFromList(element){
    var word = $('span', element).text();
    $(element).remove();
    var oldWords = keyWordsGetOld(element.parentNode.parentNode);
    var newWords = [];
    for (var i = 0;i < oldWords.length; i++){
        if (oldWords[i] != word) {
            newWords[newWords.length] = old[i];
        }
    }
    keyWordsSetNew(newWords, element.parentNode.parentNode);
}

SCRIPT;

        if ('string' == $this->valueAs) {
            $extra = <<<EXTRA
function keyWordsGetOld(parent){
   return $('.keywords-hidden-input', parent).val().split('{$this->separator}');
}
function keyWordsSetNew(words, parent){
    $('.keywords-hidden-input', parent).val(words.join('{$this->separator}'));
}

EXTRA;
        } else {
            $extra = <<<EXTRA
function keyWordsGetOld(parent){
   return $('.keywords-hidden-input', parent).val();
}
function keyWordsSetNew(words, parent){
   $('.keywords-hidden-input', parent).val(words);
}
EXTRA;

        }



        return Html::get()->script($script . $extra);
    }

    /**
     * @return string
     */
    public function getStyles() {
        $style = <<<STYLE
.keywords-hidden-input{
    display:none;
}

.keywords-list-input{
  padding:10px;
  display:Block;
  float:left;
  clear:both;
}
.keywords-list-input .keyword-word-box{
  display:block;
  float:left;
  padding:3px;
  margin:2px;
  line-height:16px;
  text-decoration: none !important;
}

.mform-default-wide .keywords-list-input .keyword-word-box, .mform-default .keywords-list-input .keyword-word-box{
  border-radius:5px;
  border:1px solid #3f6faf;
}

.keywords-list-input .keyword-word-box .keyword-remove{
  color:orangered;
  padding-left:5px;
  cursor:pointer;
}

STYLE;

        return Html::get()->css($style);
    }
}
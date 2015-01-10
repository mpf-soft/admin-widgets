<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 19.12.2014
 * Time: 11:19
 */

namespace mpf\widgets\form\fields;


use mpf\web\helpers\Html;

class ForumTextarea extends Textarea{

    public $tags = [];

    /**
     * @var string
     * Can be added before or after textarea. The more exact position can be set from CSS. HTML class will also be updated
     * so that includes this in name. So that CSS can be adapted.
     */
    public $tagsHitLocation = 'before';

    public $tagsSeparator = ', ';

    public function getInput(){
        return $this->getBeforeInput() . parent::getInput() . $this->getAfterInput() . $this->getScripts() . $this->getStyle();
    }

    protected function getBeforeInput(){
        if ('before' != $this->tagsHitLocation){
            return "";
        }
        return $this->getInputHint();
    }

    protected function getAfterInput(){
        if ('after' != $this->tagsHitLocation){
            return "";
        }
        return $this->getInputHint();
    }

    protected function getInputHint(){
        $tags = [];

        foreach ($this->tags as $name=>$hint){
            $tags[] = Html::get()->link('#', "[$name]", ['title' => $hint]);
        }

        $hint = Html::get()->tag('span', $this->translate("Possible tags to use: "))
            . Html::get()->tag('span', implode($this->tagsSeparator, $tags));

        return Html::get()->tag('div', $hint, ['class' => 'forumtextarea-tags-list']);
    }

    public function getScripts(){
        $script = <<<SCRIPT
\$(document).ready(function(){
    $('.forumtextarea-tags-list a').click(function(){
         var _parent = this.parentNode.parentNode.parentNode;
         var text  = $(this).text() + $(this).text().replace('[', '[\\\\');
         var position = $('textarea', _parent).getCursorPosition();
         console.log("Position: " + position);
         var value = $('textarea', _parent).val();
         $('textarea', _parent).val(value.substring(0, position) + text + value.substring(position));
         return false;
    });
});

(function ($, undefined) {
    $.fn.getCursorPosition = function() {
        var el = $(this).get(0);
        var pos = 0;
        if('selectionStart' in el) {
            pos = el.selectionStart;
        } else if('selection' in document) {
            el.focus();
            var Sel = document.selection.createRange();
            var SelLength = document.selection.createRange().text.length;
            Sel.moveStart('character', -el.value.length);
            pos = Sel.text.length - SelLength;
        }
        return pos;
    }
})(jQuery);
SCRIPT;
        return Html::get()->script($script);
    }

    public function getStyle(){
        $style = <<<STYLE

STYLE;
        return Html::get()->css($style);
    }

    /**
     * @param string $original
     * @param array $rules
     * @return string
     */
    public static function parseText($original, $rules){
        foreach ($rules as $name => $rule){
            $original = self::applyRule($original, $name, $rule);
        }
        return $original;
    }

    /**
     * @param string $text
     * @param string $name
     * @param string|callback $rule
     * @return string
     */
    protected static function applyRule($text, $name, $rule){

        return $text;
    }
}
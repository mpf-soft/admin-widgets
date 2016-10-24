<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 08.12.2015
 * Time: 11:47
 */

namespace mpf\widgets\form\fields;


use mpf\web\helpers\Form;
use mpf\web\helpers\Html;
use mpf\WebApp;
use mpf\widgets\form\Field;

class Markdown extends Field
{
    public $skipMarkdownPassFor = ['#\[code=([a-zA-Z0-9_\-]+)\](.*?)\[/code\]#sm', '#\[code\](.*?)\[/code\]#sm', '#\[php\](.*?)\[/php\]#sm', '#\[javascript\](.*?)\[/javascript\]#sm'];

    public $extraFilters = [
        '#\[code=([a-zA-Z0-9_\-]+)\](.*?)\[/code\]#sm' => '<pre><code class="$1">$2</code></pre>',
        '#\[code\](.*?)\[/code\]#sm' => '<pre><code>$1</code></pre>',
        '#\[php\](.*?)\[/php\]#sm' => '<pre><code class="php">$1</code></pre>',
        '#\[javascript\](.*?)\[/javascript\]#sm' => '<pre><code class="javascript">$1</code></pre>',
        '#\[color=([\#a-zA-Z0-9_\-]+)\](.*?)\[/color\]#sm' => '<span style="color:$1;">$2</span>'
    ];

    public $hintText = "This input uses Markdown syntax. <a href='https://daringfireball.net/projects/markdown/syntax' target='_blank'>Click For Details</a>";

    /**
     * A POST with the text will be sent in an AJAX request and the returned content will be displayed.
     * @var
     */
    public $previewURL;

    /**
     * Get HTML Code for Input
     * @return string
     */
    public function getInput()
    {
        $this->htmlOptions['class'] = (isset($this->htmlOptions['class']) ? $this->htmlOptions['class'] . ' ' : '') . 'input markdown-input';
        $this->htmlOptions['ajax-url'] = $this->previewURL ?: WebApp::get()->request()->getCurrentURL();
        $this->htmlOptions['csrf-key'] = WebApp::get()->request()->getCsrfKey();
        $this->htmlOptions['csrf-value'] = WebApp::get()->request()->getCsrfValue();
        return Form::get()->textarea($this->getName(), $this->getValue(), $this->htmlOptions);
    }

    /**
     * @return string
     */
    public function getHint()
    {
        return Html::get()->tag("span", $this->translate($this->hintText), ["class" => "markdown-hint"]);
    }

    /**
     * @return string
     */
    public function getPreview()
    {
        return Html::get()->tag("div", "", ["class" => "markdown-preview"]);
    }

    protected static $preparedReplacedText = [];

    /**
     * @param string $t
     * @return string
     */
    protected static function encodeStrips($t)
    {
        $strip = new self();
        $strip = $strip->skipMarkdownPassFor;
        foreach ($strip as $regexp) {
            preg_match_all($regexp, $t, $matches);
            if (count($matches[0])) {
                foreach ($matches[0] as $match) {
                    if ($match) {
                        self::$preparedReplacedText[$md = 'TOREPLACEBACK:' . md5($match)] = htmlentities($match);
                        $t = str_replace($match, $md, $t);
                    }
                }
            }
        }

        return $t;
    }

    /**
     * @param string $t
     * @return string
     */
    protected static function processTextFilters($t)
    {
        $t = str_replace(array_keys(self::$preparedReplacedText), array_values(self::$preparedReplacedText), $t);
        $extra = new self();
        $extra = $extra->extraFilters;
        foreach ($extra as $regexp => $replace) {
            $t = preg_replace($regexp, $replace, $t);
        }
        return $t;
    }

    /**
     * @param string $original
     * @return string
     */
    public static function processText($original)
    {
        return self::processTextFilters(\Michelf\Markdown::defaultTransform(self::encodeStrips($original)));
    }

    /**
     * Overwrite field getContent to add hints
     * @return string
     */
    public function getContent()
    {
        return parent::getContent() . $this->getHint() . $this->getPreview();
    }
}
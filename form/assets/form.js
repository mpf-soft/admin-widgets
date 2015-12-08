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

$(document).ready(function () {
    mpfFormOnLoadInit();
});

function mpfFormOnLoadInit() {

    function markdownPreview(element){
        var _parent = element.parentNode;
        var csrfKey = $(element).attr('csrf-key');
        var csrfValue = $(element).attr('csrf-value');
        var post = {
            MarkdownPreview : 1,
            text : $(element).val()
        };
        post[csrfKey] = csrfValue;
        $.post(
            $(this).attr('ajax-url'), post,
            function (data) {
                $(".markdown-preview", _parent).html(data);
            }
        );
    }

    $('.birthday').change(function () {
        var parent = this.parentNode;
        $('.birthday_value', parent).val($('.bday-year').val() + '-' + $('.bday-month').val() + '-' + $('.bday-day').val());
    });
    if ($('.autocomplete').length) {
        $('.autocomplete').autocomplete();
    }

    $(".markdown-input").each(function () {
        var interval;
        $(this).focus(function(){
            var _element = this;
            interval = setInterval(function(){
                markdownPreview(_element);
            }, 5000);
        }).blur(function(){
            clearInterval(interval);
            markdownPreview(this);
        });
    });


}
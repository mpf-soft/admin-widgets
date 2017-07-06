/**
 * Created by mirel on 18.08.2015.
 */

var MPFForm_AutoComplete = {
    lastAjaxSearchedText: '',
    lastTextOnTheInput: '',
    selectionWasMade : false,
    /**
     * Call this method for each autocomplete element
     * @param element
     */
    init: function (element) {
        var enterPressed = false;
        var _self = this;
        $(element).focus(function () {
            if ($('.form-autocomplete-list', this.parentNode).find('li').length) { // only if there are any options available
                _self.displayList(this, 'focus');
            }
        }).blur(function () {
            var __this = this;
            setTimeout(function(){
                $('.form-autocomplete-list', __this.parentNode).fadeOut();
            }, 100);
        }).keyup(function () {
            if (_self.selectionWasMade || _self.lastAjaxSearchedText == this.value){
                return true;
            }
            if ($(this).attr('autc_ajax') == '1') {
                if ($(this).val().length >= $(this).attr('autc_minletters')) {
                    _self.displayList(this, 'ajax search');
                    _self.ajaxSearch($(this).val(), $(this).attr('autc_url'), JSON.parse($(this).attr('autc_extraparams') + ''), $('.form-autocomplete-list', this.parentNode));
                } else if ($('.form-autocomplete-list', this.parentNode).is(':visible')) {
                    $('.form-autocomplete-list', this.parentNode).fadeOut();
                }
            } else {
                _self.displayList(this, 'non-ajax search');
                _self.filterList(this.value, $('.form-autocomplete-list', this.parentNode));
            }
        }).keydown(function (e) {
            if (e.which == 40) { //down
                if (enterPressed && $('.form-autocomplete-list', this.parentNode).find('li').length) {
                    _self.displayList(this, 'down after enter');
                    enterPressed = false;
                }
                _self.moveDown($('.form-autocomplete-list', this.parentNode));
            } else if (e.which == 38) { //up
                if (enterPressed && $('.form-autocomplete-list', this.parentNode).find('li').length) {
                    _self.displayList(this, 'up after enter');
                    enterPressed = false;
                }
                _self.moveUp($('.form-autocomplete-list', this.parentNode));
            } else if (e.which == 13) {
                $(this).val($('.form-autocomplete-list', this.parentNode).find('li.selected').text());
                console.log("ENTER on: " + $('.form-autocomplete-list', this.parentNode).find('li.selected').text());
                _self.selectionWasMade = true;
                $('#' + $(this).attr('autc_for')).val($('.form-autocomplete-list', this.parentNode).find('li.selected').text());
                $('.form-autocomplete-list', this.parentNode).fadeOut();
                setTimeout(function(){
                    _self.selectionWasMade = false;
                }, 200);
                enterPressed = true;
                return false;
            } else if (e.which == 27) {
                enterPressed = false;
                $('.form-autocomplete-list', this.parentNode).fadeOut();
            }
        });
        $('.form-autocomplete-list li', element.parentNode).hover(function () {
            $('li', this.parentNode).removeClass('selected');
            $(this).addClass('selected');
        }).click(function () {
            $(element).val($(this).text());
            console.log('Click on: ' + $(this).text());
            _self.selectionWasMade = true;
            $('#' + $(element).attr('autc_for')).val($(this).text());
            $('.form-autocomplete-list', element.parentNode).fadeOut();
            setTimeout(function(){
                _self.selectionWasMade = false;
            }, 200);
            return false;
        })
    },
    checkScroll: function (list) {
        if ($('li.selected', list).position().top + $('list.selected', list).height() > $(list).height()) {
            $(list).scroll($('li.selected', list).position().top + $('list.selected', list).height());
        }
    },
    displayList: function (element, log) {
        if (!$('.form-autocomplete-list', element.parentNode).is(':visible')) {
            $('.form-autocomplete-list', element.parentNode).html('').fadeIn();
        }
        $('.form-autocomplete-list', element.parentNode)
            .css('top', $(element).position().top + $(element).height() + 5 + 'px')
            .css('left', $(element).position().left + 'px')
            .css('width', $(element).width() + 'px')
            .fadeIn();
        if (log){
            console.log('displayList: ' + log);
        }
    },
    moveUp: function (list) {
        var all = $('li', list);
        if (all.length == 0) {
            return;//no action if there are no items
        }
        if (!$('li.selected', list).length) {
            $(all[all.length - 1]).addClass('selected');
            this.checkScroll(list);
            return;//if none are selected then select the last one
        }

        for (var i = 0; i < all.length; i++) {
            if ($(all[i]).hasClass('selected')) {
                if (i != 0) {
                    $(all[i - 1]).addClass('selected');
                    $(all[i]).removeClass('selected');
                } else {
                    $(all[i]).removeClass('selected');
                    $(all[all.length - 1]).addClass('selected');
                }
                break;
            }
        }
        this.checkScroll(list);
    },
    moveDown: function (list) {
        var all = $('li:not(.hidden)', list);
        if (all.length == 0) {
            return;// no action if there are no items;
        }
        if (!$('li.selected', list).length) {
            $(all[0]).addClass('selected');
            this.checkScroll(list);
            return;//if none are selected then select the first one
        }
        for (var i = 0; i < all.length; i++) {
            if ($(all[i]).hasClass('selected')) {
                if (i != all.length - 1) {
                    $(all[i + 1]).addClass('selected');
                    $(all[i]).removeClass('selected');
                } else {
                    $(all[i]).removeClass('selected');
                    $(all[0]).addClass('selected');
                }
                break;
            }
        }
        this.checkScroll(list);
    },
    filterList: function (text, list) {
        if (text.length == 0) {
            $('li', list).removeClass('hidden');
            return;
        }
        $('li', list).each(function () {
            if ($(this).text().toLowerCase().indexOf(text.toLowerCase()) == -1) {
                $(this).addClass('hidden');
            } else {
                $(this).removeClass('hidden');
            }
        })
    },
    ajaxSearch: function (text, url, extraOptions, htmlListContainer) {
        var _self = this;
        text = text.trim();
        _self.lastTextOnTheInput = text;
        setTimeout(function () {
            if (text != _self.lastTextOnTheInput || text == _self.lastAjaxSearchedText) { // check for eventual changes
                return 0;
            }
            extraOptions['text'] = text;
            _self.lastAjaxSearchedText = text;
            $.post(url, extraOptions,
                function (result) {
                    htmlListContainer.html('');
                    $.each(result, function (index, value) {
                        htmlListContainer.append("<li>" + value + "</li>");
                    });
                }, 'json');
        }, 200);

    }
};

$.fn.autocomplete = function () {
    this.each(function () {
        MPFForm_AutoComplete.init(this);
    });
};
$(document).ready(function () {
    "use strict";
    var $helper = $('.helper');
    var $about = $('.about');
    var $searchMain = $('.search-main');

    $helper.popover({'trigger': 'focus'});
    $about.popover({'trigger': 'hover'});

    if ($searchMain.length != 0) {
        $searchMain.typeahead({
            limit: 10,
            highlight: true,
            remote: {
                url: $searchMain.data('target').replace('%25', '%')
            }
        }).on('typeahead:selected', function () {
            $(this).closest('form').submit();
        });
    }

    $.nette.init();

    //Extension of nette ajax calls
    $.nette.ext('main-admin',
        {
            success: function (payload) {
                initAjaxDependend();
            }
        },
        {
            selector: null
        });


    function phpToMoment(phpDateTimeFormat) {
        var conversions = {
            'd': 'DD',
            'D': 'ddd',
            'j': 'D',
            'l': 'dddd',
            'N': 'E',
            'S': 'o',
            'w': 'e',
            'z': 'DDD',
            'W': 'W',
            'F': 'MMMM',
            'm': 'MM',
            'M': 'MMM',
            'n': 'M',
            't': '',
            'L': '',
            'o': 'YYYY',
            'Y': 'YYYY',
            'y': 'YY',
            'a': 'a',
            'A': 'A',
            'B': '',
            'g': 'h',
            'G': 'H',
            'h': 'hh',
            'H': 'HH',
            'i': 'mm',
            's': 'ss',
            'u': 'SSS',
            'e': 'zz',
            'I': '',
            'O': '',
            'P': '',
            'T': '',
            'Z': '',
            'c': '',
            'r': '',
            'U': 'X',
        };
        
        return phpDateTimeFormat.replace(/[A-Za-z]+/g, function(match) {
            return conversions[match] || match;
        });
        }

    $('.datetime, .date, .time').not(".grido .date").each(function () {
        var format = $(this).data('format');
        var language = $("html").attr('lang');
        if (!language) {
            language = 'en';
        }

        if (!format) {
            format = 'Y-m-d';
        }
        $(this).datetimepicker({format: phpToMoment(format), locale: language});

        //Do i need to wrap it into input-group
        if (!$(this).parent('.input-group').length) {
            $(this).wrap('<div class="input-group"></div>');
        }
        var $icon = $('<span class="input-group-addon"><i class="fa fa-calendar"></i></span>');

        var self = $(this);
        $icon.click(function () {
            self.focus();
        });
        $(this).after($icon);
    });
    
    //Inits of ajax dependent things to init at load
    initAjaxDependend();


    /**
     * Allow anchored tabs
     */
    //Support for urling tabs
    var url = document.location.toString();
    if (url.match('#')) {
        var name = url.split('#')[1];
        if (name) {
            $('.nav-tabs').find('a[href="#' + name + '"]').tab('show');
        }
    }

    // Change hash for page-reload
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    });

    //$('ul.nav.nav-tabs').tabdrop();

    $('.wysiwyg').summernote({
        height: 200,
        tabsize: 2,
        codemirror: {
            theme: 'monokai'
        },
        //modules: $.extend($.summernote.options.modules, {"specialchars": FilesManager}),
        toolbar: [

            ['style', ['style', 'bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['misc', ['fullscreen', 'codeview', 'undo', 'redo', 'link', 'video']]
        ],
        callbacks: {
            onPaste: function (e) {
                var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                e.preventDefault();
                setTimeout(function () {
                    document.execCommand('insertText', false, bufferText);
                }, 10);
            }
        }
    });

    $('.codemirror').each(function () {
        var self = $(this);
        var editor = CodeMirror.fromTextArea(self[0], {
            lineNumbers: true,
            mode: self.data('codemirror-mode') || 'htmlmixed'
        });
    });

    $('.graph').each(function(){
        var that = $(this);
        var colors = ["#71c73e", "#77b7c5", "#d54848", "#6c42e5", "#e8e64e", "#dd56e6", "#ecad3f", "#618b9d", "#b68b68", "#36a766", "#3156be", "#00b3ff", "#646464", "#a946e8", "#9d9d9d"];
        Morris.Area({
            element: that,
            data: that.data('data'),
            xkey: 'period',
            ykeys: ['amount'],
            labels: ['Amount'],
            hideHover: 'auto',
            lineColors: colors,
            fillOpacity: 0.3,
            behaveLikeLine: true,
            lineWidth: 2,
            pointSize: 4,
            gridLineColor: '#cfcfcf',
            resize: true
        });
    });

    $('.pop').popover();

    $('.selfpost').change(function() {
        $($(this).data('target')).submit();
    });
    
    $('.typeahead-dependend').each(function(){

        var url = $(this).data('suggest-handler');
        var replacement = $(this).data('suggest-replacement');
        var depedency = $(this).data('suggest-depedency');
        var minLength = $(this).data('suggest-min');
        var $depedency = $('#' + depedency);
        var depedency_replacement = $(this).data('suggest-depedency-replacement');
        var mySource = new Bloodhound({
            datumTokenizer: function (datum) {
                return Bloodhound.tokenizers.whitespace(datum.value);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: url,
                replace: function(u, uriEncodedQuery) {
                    return u.replace(depedency_replacement, $depedency.val()).replace(replacement, uriEncodedQuery);
                },
                filter: function (parsedResponse) {
                    return $.map(parsedResponse.results, function (item) {
                        return {
                            value: item[0]
                        };
                    });
                }
            }
        });

        mySource.initialize();

        $(this).typeahead({
                minLength: minLength,
                highlight: true
            },
            {
                source: mySource.ttAdapter()
            });
    });
});

function initAjaxDependend() {
    $("select").not(".datagrid select").select2({'width': 'resolve'});
    $(".datagrid select[multiple]").select2({'width': 'resolve'});
    
    $('.invalidator').each(function () {
        var elem = $(this);
        elem.data('oldVal', elem.val());

        // Look for changes in the value
        elem.bind("propertychange change click keyup input paste", function (event) {
            // If value has changed...
            if (elem.data('oldVal') != elem.val()) {
                // Updated stored value
                elem.data('oldVal', elem.val());

                var data = {};
                var parameterName = elem.data('invalidate-parameter') || 'value';
                data[parameterName] = elem.val();
                var url = elem.data('invalidate-url').replace(/&amp;/g, '&');
                $.nette.ajax({
                    type: 'GET',
                    url: url,
                    data: data
                });
            }
        });
    });
}

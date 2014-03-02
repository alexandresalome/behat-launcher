var refreshPage = function (timeout) {
    $.ajax({
        url: document.location,
        dataType: "html",
        success: function (text) {
            var $content = $(text).find('*[data-refresh]').each(function (i, e) {
                var $e = $(e);
                $("*[data-refresh='" + $e.attr('data-refresh') + "']").replaceWith($e);
            });

            setTimeout(function() {
                refreshPage(timeout);
            }, 2000);
        }
    })
};

$(function () {
    if ($("#content").hasClass('refresh')) {
        refreshPage(2000);
    }

    $(document).on('click', 'a[data-locale]', function (event) {
        event.preventDefault();

        var locale = $(event.currentTarget).attr('data-locale');
        document.cookie = 'locale=' + locale + ';max-age=31104000;path=/';
        document.location.reload();
    });

    $(document).on('click', 'a.output-file', function (event) {
        event.preventDefault();

        var $target = $(event.currentTarget);

        if ($target.hasClass('output-file-empty')) {
            return;
        }

        var href = $target.attr('href');
        var width = $(document).width() - 100;
        var height = $(window).height() - 160;

        console.log(width, height);

        var $iframe = $('<div class="modal">'
            + '<div style="width:'+ width +'px; height: '+ height +'px" class="modal-dialog">'
                + '<div class="modal-content">'
                    + '<iframe style="border: 0; width:'+ width +'px; height: '+ height +'px" id="output-file" src="' + href + '"></iframe>'
                    + '<div class="modal-footer">'
                        + '<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="glyphicon glyphicon-arrow-left"></span> Close output</button>'
                    + '</div>'
                + '</div>'
            + '</div>'
        + '</div>');
        $iframe.modal();
    });


});

function decorateFeatureDirectory(e)
{
    var $cb = $('<input type="checkbox" />');
    var $exp = $('<a class="expand">&nbsp; +</a>');
    var $col = $('<a class="collapse">&nbsp; -</a>')
    var $row = $(e);
    var path = $row.attr('data-path');
    $cb.attr('data-path', path);
    $exp.attr('data-path', path);
    $col.attr('data-path', path);

    $col.hide();

    var $header = $row.find('> div.header');
    var $rows = $row.find('> div.rows');

    $header.prepend($cb);
    $header.append($exp);
    $header.append($col);

    // select/unselect
    $cb.click(function () {
        $(e).find('> div.rows input[type="checkbox"]').each(function(i, e) {
            var $e = $(e);
            if ($e.prop('checked') != $cb.prop('checked')) {
                $e.click();
            }
        });
    });

    var collapse = function () {
        $rows.slideUp();
        $col.hide();
        $exp.show();
    };

    var expand = function () {
        $rows.slideDown();
        $col.show();
        $exp.hide();
    };

    var toggle = function () {
        if ($rows.is(':visible')) {
            collapse();
        } else {
            expand();
        }
    };

    $exp.click(expand);
    $col.click(collapse);

    // toggle
    $header.find('> label').click(function () {
        toggle();
    })
}

refreshPage = function () {
    $.ajax({
        url: document.location,
        dataType: "html",
        success: function (text) {
            var $content = $(text).find('*[data-refresh]').each(function (i, e) {
                var $e = $(e);
                $("*[data-refresh='" + $e.attr('data-refresh') + "']").replaceWith($e);
            });
        }
    })
};

$(function () {
    if ($("#content").hasClass('refresh')) {
        setInterval(refreshPage, 2000);
    }

    $(document).on('click', 'a[data-locale]', function (event) {
        event.preventDefault();

        var locale = $(event.currentTarget).attr('data-locale');
        document.cookie = 'locale=' + locale + ';max-age=31104000;path=/';
        document.location.reload();
    });

    $("div.block-features").each(function (i, e) {
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
            $(e).find('> div.rows input[type="checkbox"]').prop('checked', $cb.prop('checked'));
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
    });

    $(document).on('click', 'a.output-file', function (event) {
        event.preventDefault();

        var $target = $(event.currentTarget);

        if ($target.hasClass('output-file-empty')) {
            return;
        }

        var href = $target.attr('href');
        var $row = $target.closest('tr');

        if ($row.next().is('.output-file')) {
            $row.next().remove();
        }

        if ($target.hasClass('current')) {
            $target.removeClass('current');

            return;
        }

        $row.find('a.output-file').removeClass('current');
        $target.addClass('current');

        var colspan = $row.find('> td').length;
        var $iframe = $('<tr class="output-file"><td colspan="' + colspan + '"><iframe src="' + href + '"></iframe></td></tr>');
        $row.after($iframe);
    });
});

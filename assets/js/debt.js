$(function () {
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

    if ($("#content").hasClass('refresh')) {
        refreshPage(2000);
    }

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

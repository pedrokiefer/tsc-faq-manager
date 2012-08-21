/**
 * Pedro Kiefer <pedro@tecnosenior.com>
 * Date: 8/20/12
 */

(function ($) {
    $.fn.faqSearch = function (options) {
        var renderResult = function (data, even) {
            var rowClass = even ? "even" : "odd";
            var header = $("<div/>", {class:"question-head " + rowClass})
                .append($("<h2></h2>")
                .append($("<a></a>", {text:data["question"], href:"#"}))
            );

            header.click(function () {
                $(this).next().slideToggle('slow');
                return false;
            });
            var content = $("<div></div>", {class:"question-body"}).html(data["answer"]).hide();
            var html = $("<div></div>", {class:"faq-question"}).append(header).append(content);

            return html;
        };

        var settings = $.extend({
            target:false,
            bindImage:false,
            renderResult:renderResult
        }, options);

        return this.each(function () {
            var self = this;

            if (settings.bindImage != false) {
                settings.bindImage.click(function () {
                    $(self).trigger('submit');
                })
            }

            $(this).submit(function (e) {
                e.preventDefault();
                var form = $(this);
                $.post(TscFaqAjax.ajaxurl, form.serialize(), function (data) {
                    if (data.status == "error") {
                        log.console(data);
                    } else if (data.status == "success") {
                        $(settings.target).empty();
                        $.each(data.result, function (index, value) {
                            $(settings.target).append(settings.renderResult(value, (index % 2 == 1)));
                        })
                    }
                });
            });
        });
    }
})(jQuery);
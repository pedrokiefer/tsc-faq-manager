jQuery(document).ready(function ($) {
    var renderFaq = function (data, even) {
        var html = $("<dt></dt>").html(data["question"])
            .after($("<dd></dd>").html(data["answer"]));

        return html;
    };
    $("form[name=search]").faqSearch({
        target:$("#faq-questions-list"),
        renderResult: renderFaq
    });
    $("form[name='ask-question']").submit(function (e) {
        e.preventDefault();
        var form = $(this);
        $.post(TscFaqAjax.ajaxurl, form.serialize(), function (data) {
            if (data.status == "error") {
                alert(data.message);
            } else if (data.status == "success") {
                alert("Sent!");
            }
        });
    });
});
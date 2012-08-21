jQuery(document).ready(function ($) {
    $("form[name=search]").faqSearch({
        target:$("#faq-questions-list"),
        bindImage:$("#faq-magnifier")
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
    $('.faq-questions .question-head').click(function () {
        $(this).next().slideToggle('slow');
        return false;
    }).next().hide();
});

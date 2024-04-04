jQuery(".question .question-title").hasClass("active") &&
  jQuery(".question .question-title.active")
    .closest(".question")
    .find(".question-inner")
    .show(),
  jQuery(".question .question-title").click(function () {
    jQuery(this).hasClass("active")
      ? jQuery(this)
        .removeClass("active")
        .closest(".question")
        .find(".question-inner")
        .slideUp(200)
      : jQuery(this)
        .addClass("active")
        .closest(".question")
        .find(".question-inner")
        .slideDown(200);
  }),
  $('a[href*="#"]')
    .not('[href="#"]')
    .not('[href="#0"]')
    .click(function (e) {
      if (
        location.pathname.replace(/^\//, "") ==
        this.pathname.replace(/^\//, "") &&
        location.hostname == this.hostname
      ) {
        var t = $(this.hash);
        (t = t.length ? t : $("[name=" + this.hash.slice(1) + "]")),
          t.length &&
          (e.preventDefault(),
            $("html, body").animate(
              { scrollTop: t.offset().top },
              500,
              function () {
                var e = $(t);
                return !e.is(":focus") && void e.attr("tabindex", "-1");
              }
            ));
      }
    });

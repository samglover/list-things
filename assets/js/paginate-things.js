jQuery(document).ready(function ($) {
  $('.list-of-things__container').each(function () {
    const thisSection = $(this);

    thisSection.on('click', '.things-pagination .page-numbers:not(.current):not(.dots)', function (e) {
      e.preventDefault();

      const nonce = {
        nonce: thisSection.data('thingsNonce'),
        nonce_action: 'things_nonce_' + thisSection.data('thingsSectionId'),
      };
      const args = $.extend(true, {}, thisSection.data('thingsArgs'));
      const options = $.extend(true, {}, thisSection.data('thingsOptions'));

      const page = $(this).data('page');

      if (page && page > 0) {
        args.paged = page;
        paginateThings_ajax(nonce, args, options);
      }
    });
  });

  function paginateThings_ajax(nonce, args, options) {
    let thisSection = '#list-of-things-' + options.things_section_id;
    $.ajax({
      type: 'POST',
      url: vars.ajaxurl,
      data: {
        _ajax_nonce: nonce.nonce,
        _ajax_nonce_action: nonce.nonce_action,
        action: 'new_get_things',
        args: args,
        options: options,
      },
      beforeSend: function () {
        // Scroll to top of the list
        $('html, body').animate({
          scrollTop: $(thisSection).offset().top - 100
        }, 300);
        $(thisSection).children('.list-of-things').remove();
        $(thisSection).append('<p class="things-loader">Loading &hellip;</p>');
      },
      success: function (response) {
        $(thisSection).children(':not(.search-sort-things)').remove();
        $(thisSection).append(response);
      },
      error: function (response) {
        $(thisSection).children('.things-loader').remove();
        $(thisSection).append('<p class="things-error-message">Error loading page.</p>');
        $(thisSection).append('<pre>' + response + '</pre>');
      },
    });
  }
});

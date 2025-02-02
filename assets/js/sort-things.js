jQuery(document).ready(function ($) {
  if (!document.querySelector('.thing-sorter')) return;
  
  $('.thing-sorter').each(function () {
    let thisSection = $(this).parents('.list-of-things__container'); 
    let nonce = {
      nonce: thisSection.data('thingsNonce'),
      nonce_action: 'things_nonce_' + thisSection.data('thingsSectionId'),
    }
    let args = thisSection.data('thingsArgs');
    let options = thisSection.data('thingsOptions');
    
    let sortButtons = $(this).find('.things-sort-button');
    sortButtons.each(function () {
      $(this).on('click', function () {
        sortButtons.prop('disabled', false);
        if (!$(this).hasClass('things-randomize-button')) $(this).prop('disabled', true);
        thisSection.addClass('sorted');
        args.order = $(this).data('thingsOrder');
        args.orderby = $(this).data('thingsOrderby');
        options.sorted = false;
        sortThings_ajax(nonce, args, options);
      });
    });
  });

  function sortThings_ajax(nonce, args, options) {
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
        $(thisSection).scrollTop();
        $(thisSection).children('.list-of-things').remove();
        $(thisSection).append('<p class="things-loader">Sorting &hellip;</p>');
      },
      success: function (response) {
        $(thisSection).children(':not(.search-sort-things )').remove();
        $(thisSection).append(response);
      },
      error: function (response) {
        $(thisSection).append('<p class="things-error-message">Error message:</p>');
        $(thisSection).append('<pre>' + response + '</pre>');
      },
    });
  }
});
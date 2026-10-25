jQuery(document).ready(function ($) {
  if (!document.querySelector('.thing-searcher')) return;
  $('.thing-searcher').each(function () {
    let thisSection = $(this).parents('.list-of-things__container'); 
    let args = thisSection.data('thingsArgs');
    let options = thisSection.data('thingsOptions');

    let searchThingsInput = $(this).find('.search-things-input');  
    let clearSearchThings = $(this).find('.clear-search-things'); 
    searchThingsInput.on('input', () => {
      if (searchThingsInput.val()) {
        clearSearchThings.css('display', '');
      } else {
        clearSearchThings.hide();
      }
    });
    clearSearchThings.on('click', function () {
      clearSearchThings.hide();
      searchThingsInput.val('');
      if (thisSection.hasClass('searched')) {
        thisSection.removeClass('searched');
        args.s = '';
        options.searched = false;
        searchThings_ajax(args, options);
      }
    });
    $(this).on('submit', function () {
      args.s = searchThingsInput.val();
      options.searched = true;
      searchThings_ajax(args, options);
    });
  });

  function searchThings_ajax(args, options) {
    let thisSection = '#list-of-things-' + options.things_section_id;
    let searchThingsInput = $(thisSection + ' .search-things-input');
    let clearSearchThings = $(thisSection + ' .clear-search-things');
    $.ajax({
      type: 'POST',
      url: vars.ajaxurl,
      data: {
        action: 'new_get_things',
        args: args,
        options: options,
      },
      beforeSend: function () {
        $(thisSection).scrollTop();
        $(thisSection).children('.list-of-things').remove();
        $(thisSection).append('<p class="things-loader">Searching &hellip;</p>');
      },
      success: function (response) {
        $(thisSection).children(':not(.search-sort-things )').remove();
        if (searchThingsInput.val()) {
          $(thisSection).addClass('searched');
          clearSearchThings.css('display', '');
        } else {
          $(thisSection).removeClass('searched');
          clearSearchThings.hide();
        }
        $(thisSection).append(response);
      },
      error: function (response) {
        $(thisSection).append('<p class="things-error-message">Error message:</p>');
        $(thisSection).append('<pre>' + response + '</pre>');
      },
    });
  }
});
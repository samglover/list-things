jQuery(document).ready(function ($) {
  if (!document.querySelector('.thing-filters__form')) return;
  
  $('.thing-filters__form').each(function () {
    let thisSection = $(this).parents('.list-of-things__container');
    let nonce = {
      nonce: thisSection.data('thingsNonce'),
      nonce_action: 'things_nonce_' + thisSection.data('thingsSectionId'),
    }
    let args = thisSection.data('thingsArgs');
    let options = thisSection.data('thingsOptions');

		let showFiltersButton = $(thisSection).find('.thing-filters__button');
		let thingFiltersFormContainer = $(thisSection).find('.thing-filters__form__container');
		let resetFiltersButton = $(thisSection).find('.reset-thing-filters__button');
		let thingFiltersForm = $(thisSection).find('.thing-filters__form');
		
		$(showFiltersButton).click(function (event) {
			event.preventDefault();
			$(this).toggleClass('thing-filters__open');
			$(thingFiltersFormContainer).toggle().toggleClass('open');
		});
		
		$(resetFiltersButton).click(function (event) {
			event.preventDefault();
			resetFilters(nonce, args, options);
		});
		
		$(thingFiltersForm).on('change', 'input', function () {
			console.log(args);
			args = updateTaxQuery(args, options.things_section_id);
			console.log(args);
			filterThings_ajax(nonce, args, options);
		});
	});
	
	function resetFilters(nonce, args, options) {
		$('#' + options.things_section_id + '__thing-filters__form')[0].reset();
		delete args.tax_query;
		filterThings_ajax(nonce, args, options)
	}
	
	function updateTaxQuery(args, sectionID) {
		args.tax_query = {};

		let thisThingFiltersForm = $('#' + sectionID + '__thing-filters__form');
		let thingFilters = $(thisThingFiltersForm).find('.thing-filter');
		
		thingFilters.each(function() {
			let checkedTerms = $(this).find('input:checked');
			let taxonomy = $(this).data('taxonomy');
			
			if (checkedTerms.length) {
				args.tax_query[taxonomy] = {
					taxonomy: taxonomy,
					field: 'slug',
					terms: []
				}
				
				checkedTerms.each(function () {
					args.tax_query[taxonomy].terms.push($(this).data('term'));
				});
			}
		});

		return args;
	}

	function filterThings_ajax(nonce, args, options) {
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
        $(thisSection).append('<p class="things-loader">Filtering &hellip;</p>');
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
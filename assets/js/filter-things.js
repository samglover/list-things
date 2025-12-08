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
		let thingFilters = $(thisSection).find('.thing-filters__form');
		$(showFiltersButton).click(function (event) {
			event.preventDefault();
			$(this).toggleClass('thing-filters__open');
			$(thingFilters).toggleClass('open');
		});
	});
});
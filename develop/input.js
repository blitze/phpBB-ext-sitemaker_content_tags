(function($, window, document, undefined) {
	'use strict';

	$(document).ready(function() {
		$('.tag-editor').each(function() {
			$(this).tagEditor({
				maxTags: $(this).data('maxTags'),
				autocomplete: {
					source: window.tag_search_url,
					minLength: 3
				}
			});
		});
			
	});
})(jQuery, window, document);
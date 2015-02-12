jQuery(document).ready(function ($) {
	$('#mklasen_submit_add_faq').on('click', function() {
		val = $('.select_faq_category').val();
		if (!val) {
			shortcode = '[faq]';
		} else {
			shortcode = '[faq category="'+val+'"]';
		}
		//Insert content
		parent.tinyMCE.activeEditor.setContent(parent.tinyMCE.activeEditor.getContent() + shortcode);
		//Close window
		parent.jQuery("#TB_closeWindowButton").click();
	});
});
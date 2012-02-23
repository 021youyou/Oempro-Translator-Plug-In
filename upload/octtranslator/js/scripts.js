$(document).ready(function() {
	$('.js-copy-original').click(function() {
		$('#' + $(this).attr('originalid').replace('TranslationOrig-', 'TranslationNew-')).val($('#' + $(this).attr('originalid')).val());
		return false;
	});
	$('.js-google-translate').click(function(event) {
		GoogleTranslate($(this).attr('originalid'));
		return false;
	});
	$('.translation-target').focus(function() {
		$('.translate-bar.focus').removeClass('focus');
		var id = $(this).attr('id').replace('TranslationNew-', '');
		$('#translation-TranslationNew-'+id).addClass('focus');
	});
	
	$('.js-translate-all').click(function() {
		$('.js-new-translation').each(function() {
			var currentID = $(this).attr('id').replace('TranslationNew-', '');
			GoogleTranslate('TranslationOrig-' + currentID);
		})
	});

	
});

function GoogleTranslate(OriginalID) {
	var SourceItem = $('#' + OriginalID);
	var TargetItem = $('#' + OriginalID.replace('TranslationOrig-', 'TranslationNew-'));

	google.language.translate({text: SourceItem.val(), type: 'text'}, "en", target_language, function(result) {
		if (!result.error) {
			TargetItem.val(result.translation.replace('% s', '%s'));
		} else {
			TargetItem.val('failed');
		}
	});
}
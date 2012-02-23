				<style>
				div.form-row-note.translate-bar{height:0px;overflow:hidden;width:516px;}
				div.form-row-note.translate-bar.focus{background-color:#0077cc;height:auto;padding:3px 0;}
				div.form-row-note.translate-bar.focus p{color:#fff !important;}
				div.form-row-note.translate-bar.focus select{border-color:#fff;}
				div.form-row-note.translate-bar p{font-size:12px !important;margin:0px;padding-left:9px;}
				div.form-row-note.translate-bar select{border:1px solid #a6a6a6;color:#000000;font-size:11px;height:18px;line-height:18px;padding:0px !important;width:330px;}
				</style>
				<script src="<?php print(PLUGIN_URL); ?>octtranslator/js/scripts.js" type="text/javascript" charset="utf-8"></script>

				<form id="TranslatorEditForm" method="post" action="<?php InterfaceAppURL(); ?>/octtranslator/edit/<?php print($TargetLanguageCode); ?>/<?php print($StartFrom); ?>/<?php print($ItemsPerPage); ?>">
					<div id="page-shadow">
						<div id="page">
							<div class="page-bar">
								<ul class="livetabs" tabcollection="translation-tabs-1">
									<li id="tab-translation-continue"><a href="#"><?php print($PluginLanguage['Screen']['0018']); ?></a></li>
									<li id="tab-translation-tools"><a href="#"><?php print($PluginLanguage['Screen']['0019']); ?></a></li>
								</ul>
							</div>
							<div class="white">
								<div tabcollection="translation-tabs-1" id="tab-content-translation-continue">
									<?php if ($PageSuccessMessage != ''): ?>
										<h3 class="form-legend success"><?php print($PageSuccessMessage); ?></h3>
									<?php elseif ($PageErrorMessage != ''): ?>
										<h3 class="form-legend error"><?php print($PageErrorMessage); ?></h3>
									<?php elseif (validation_errors()): ?>
										<h3 class="form-legend error"><?php InterfaceLanguage('Screen', '0275', false); ?></h3>
									<?php else: ?>
										<h3 class="form-legend"><?php print($PluginLanguage['Screen']['0023']); ?></h3>
									<?php endif; ?>

									<div class="form-row no-bg">
										<p><?php print($PluginLanguage['Screen']['0027']); ?></p>
									</div>

									<?php
									$ItemCounter = 0;
									foreach ($LanguageStrings as $ID=>$Value)
										{
										if ($ItemCounter >= $ItemsPerPage) break;
										
										$ItemCounter++;
										OCT_TranslationRow($ID, $Value[0], $Value[1]);
										}
									?>
								</div>
								<div tabcollection="translation-tabs-1" id="tab-content-translation-tools">
									<div class="form-row no-bg">
										<p><?php print($PluginLanguage['Screen']['0029']); ?></p>
									</div>
									<div class="form-row no-bg">
										<div class="form-action-container clearfix left" style="margin:0px;padding:0px;">
											<a class="button" href="<?php InterfaceAppURL(); ?>/octtranslator/edit/<?php print($TargetLanguageCode); ?>/export/"><span class="left">&nbsp;</span><span class="right">&nbsp;</span><strong><?php print(strtoupper($PluginLanguage['Screen']['0028'])); ?></strong></a>
										</div>
									</div>
									<div class="form-row no-bg">
										<div class="form-action-container clearfix left" style="margin:0px;padding:0px;">
											<a class="button js-delete-lang" href="#"><span class="left">&nbsp;</span><span class="right">&nbsp;</span><strong><?php print(strtoupper($PluginLanguage['Screen']['0030'])); ?></strong></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="span-18 last">
						<div class="form-action-container">
							<a class="button" targetform="TranslatorEditForm" id="ButtonSave"><span class="left">&nbsp;</span><span class="right">&nbsp;</span><strong><?php print(strtoupper($PluginLanguage['Screen']['0013'])); ?></strong></a>
							<?php if ($StartFrom + $ItemsPerPage < $TotalLanguageStrings): ?>
								<a class="button" id="ButtonGoToNextPage"><span class="left">&nbsp;</span><span class="right">&nbsp;</span><strong><?php print(strtoupper(sprintf($PluginLanguage['Screen']['0024'], $CurrentPage + 1, $TotalPages))); ?></strong></a>
							<?php endif; ?>
							<?php if ($StartFrom > 0): ?>
								<a class="button" id="ButtonGoToPrevPage"><span class="left">&nbsp;</span><span class="right">&nbsp;</span><strong><?php print(strtoupper(sprintf($PluginLanguage['Screen']['0025'], $CurrentPage - 1, $TotalPages))); ?></strong></a>
							<?php endif; ?>
							<input type="hidden" name="Command" value="TargetLanguage" id="<?php print($TargetLanguageCode); ?>" />
							<input type="hidden" name="Command" value="SaveTranslation" id="Command" />
						</div>
					</div>
				</form>

<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript" charset="utf-8">
	google.load("language", "1");
	var target_language = '<?php print($TargetLanguageCode); ?>';
	
	$(document).ready(function() {
		$('#ButtonGoToNextPage').click(function() {
			window.location.href="<?php InterfaceAppURL(); ?>/octtranslator/edit/<?php print($TargetLanguageCode); ?>/<?php print($StartFrom + $ItemsPerPage); ?>/<?php print($ItemsPerPage); ?>";
		});
		$('#ButtonGoToPrevPage').click(function() {
			window.location.href="<?php InterfaceAppURL(); ?>/octtranslator/edit/<?php print($TargetLanguageCode); ?>/<?php print($StartFrom - $ItemsPerPage); ?>/<?php print($ItemsPerPage); ?>";
		});
		
		$('.js-delete-lang').click(function() {
			var result = confirm('<?php print($PluginLanguage['Screen']['0031']); ?>');
			
			if (result == true) {
				window.location.href = '<?php InterfaceAppURL(); ?>/octtranslator/edit/<?php print($TargetLanguageCode); ?>/delete/';
			} else {
				return false;
			}
		});
	});
</script>



<?php

function OCT_TranslationRow($ID, $OriginalValue = '', $TranslatedValue = '')
	{
	$Template  = '<div class="form-row '.(form_error('TranslationNew['.$ID.']') != '' ? 'error' : '').'" id="form-row-TranslationNew-'.$ID.'">';
	$Template .= '<label style="width:90%;" for="TranslationNew-'.$ID.'">'.implode(' &rarr; ', $Keys).'</label>';
	$Template .= '<textarea name="TranslationOrig['.$ID.']" id="TranslationOrig-'.$ID.'" class="textarea" style="height:50px; width:95%;background-color:transparent;border:none;padding:0;background-image:none;" readonly="readonly">'.$OriginalValue.'</textarea><br>';
	$Template .= '<textarea name="TranslationNew['.$ID.']" id="TranslationNew-'.$ID.'" class="textarea translation-target js-new-translation" style="height:50px; width:95%;margin-top:30px;">'.set_value('TranslationNew['.$ID.']', $TranslatedValue).'</textarea>';
	$Template .= '<div style="margin:0px;width:652px;" class="form-row-note translate-bar" id="translation-TranslationNew-'.$ID.'">';
	$Template .= '<p>';
	$Template .= '<strong>Tools: </strong>';
	$Template .= '<a href="#" style="color:#FFF; margin-left:20px;" class="js-google-translate" id="personalization-link-TranslationNew-'.$ID.'" originalid="TranslationOrig-'.$ID.'">Google Translate</a>';
	$Template .= '<a href="#" style="color:#FFF; margin-left:15px;" class="js-copy-original" id="personalization-link-TranslationNew-'.$ID.'" originalid="TranslationOrig-'.$ID.'">Copy Original</a>';
	$Template .= '</p>';
	$Template .= '</div>';
	$Template .= '</div>';

	print $Template;
	}


?>
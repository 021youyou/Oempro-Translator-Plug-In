				<form id="TranslatorOverviewForm" method="post" action="<?php InterfaceAppURL(); ?>/octtranslator/wizard/">
							<div id="page-shadow">
								<div id="page">
									<div class="page-bar">
										<h2><?php print(strtoupper($PluginLanguage['Screen']['0003'])); ?></h2>
									</div>
									<div class="white" style="min-height:380px;">
										<?php
										if (isset($PageErrorMessage) == true):
										?>
											<h3 class="form-legend error"><?php print($PageErrorMessage); ?></h3>
										<?php
										elseif (isset($PageSuccessMessage) == true):
										?>
											<h3 class="form-legend success"><?php print($PageSuccessMessage); ?></h3>
										<?php
										elseif (validation_errors()):
										?>
										<h3 class="form-legend error"><?php InterfaceLanguage('Screen', '0275', false); ?></h3>
										<?php
										else :
										?>
										&nbsp;
										<?php
										endif;
										?>
										<div class="form-row no-bg">
											<p><?php print($PluginLanguage['Screen']['0007']); ?></p>
										</div>
										<div class="form-row <?php print((form_error('TargetLanguage') != '' ? 'error' : '')); ?>" id="form-row-ContinueTranslation">
											<label for="TargetLanguage"><?php print($PluginLanguage['Screen']['0009']); ?>:</label>
											<select name="TargetLanguage" id="TargetLanguage" class="select">
												<option value="" <?php echo set_select('TargetLanguage', '', true); ?>><?php print($PluginLanguage['Screen']['0010']); ?></option>
												<optgroup label="<?php print($PluginLanguage['Screen']['0015']); ?>">
													<?php foreach($Translations as $Code=>$Status): ?>
														<option value="<?php print($Code); ?>" <?php echo set_select('TargetLanguage', $Code, false); ?>><?php print($Languages[$Code]); ?> (<?php print($Code); ?>)</option>
													<?php endforeach; ?>
												</optgroup>
												<optgroup label="<?php print($PluginLanguage['Screen']['0016']); ?>">
													<?php foreach($Languages as $Code=>$Name): ?>
														<?php if ($Translations[$Code] == true) continue; ?>
														<option value="<?php print($Code); ?>" <?php echo set_select('TargetLanguage', $Code, false); ?>><?php print($Name); ?> (<?php print($Code); ?>)</option>
													<?php endforeach; ?>
												</optgroup>
											</select>
											<div class="form-row-note"><p><?php print($PluginLanguage['Screen']['0008']); ?></p></div>
											<?php print(form_error('TargetLanguage', '<div class="form-row-note error"><p>', '</p></div>')); ?>
										</div>
									</div>
								</div>
							</div>
						
					<div class="span-18 last">
						<div class="form-action-container">
							<a class="button" targetform="TranslatorOverviewForm" id="ButtonDefaultOptInEmail"><span class="left">&nbsp;</span><span class="right">&nbsp;</span><strong><?php print(strtoupper($PluginLanguage['Screen']['0004'])); ?></strong></a>
							<input type="hidden" name="Command" value="Proceed" id="Command" />
						</div>
					</div>
				</form>

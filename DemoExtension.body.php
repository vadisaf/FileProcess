<?php
//hook function
$wgHooks['EditPage::showEditForm:initial'][] = 'DemoExtension::injectHTMLInEditor';

class DemoExtension {
    public static function injectHTMLInEditor(&$editPage,&$output) {
		$editPage->editFormTextAfterWarn .='<div id="demoDiv" class="highlight-green">****Hi This is HTML Injected via Demo Extension****</div>';
		return true;
    }
}

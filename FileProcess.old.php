<?php
if(!defined('MEDIAWIKI')){
    die("This is a mediawiki extension and cannot be accessed directly.");
}

$wgExtensionCredits['FileProcess']['other'] = array(
    'path'=>__FILE__, //path of the extension setup file
    'name'=>'FileProcess', // name of extension
    'author'=>'DADA', //name of author
    'url'=>'', //extension url where a user can find details about the extension.
    'description'=>'FileProcess gets your file from uploads and process it using the given Python script, writing the results back to the specified wiki page', //description of extension
	 'version'=>'1.0.0', //version of extension
    'licence-name'=>'', //name or url to the license under which the extension is released
);


//hook function
$wgHooks['EditPage::showEditForm:initial'][] = 'FileProcess::injectHTMLInEditor';
$wgHooks['UploadComplete'][] = 'FileProcess::onSpecialUploadComplete';
$wgHooks['UploadForm:initial'][] = 'FileProcess::onUploadForm_initial';
$wgHooks['UploadForm:BeforeProcessing'][] = 'FileProcess::onUploadForm_BeforeProcessing';

#$needed_url = "KRASAVA";
#echo $needed_url;
$x = "OEEEE";
class FileProcess {
    public static $needed_url = "PRIV";
    public static function injectHTMLInEditor(&$editPage,&$output) {
		global $x;
		$editPage->editFormTextAfterWarn .='<div id="demoDiv" class="highlight-green">****Hi This is HTML Injected via Demo Extension****</div>';
		echo self::$needed_url;
		echo $x;
		return true;
    }

    public static function onSpecialUploadComplete( $form ) {
		global $wgOut;
    }
    public static function onUploadForm_initial( SpecialUpload $uploadFormObj ) {
		global $wgOut, $wgRequest, $wgUser, $wgParser;
		global $x;
		echo $wgOut->getPageTitle();
		$wgOut->addWikiText( "==Summary==" );
		echo "THIS IS AN UPLOAD PAGE!!! YOYOYO";
		#playing with redirection
		#self::$wgOut = $wgOut;
		#$wgOut = new FileAttachDummyOutput;
		#$wgOut->redirect();
		#$wgOut->addWikiText( "==Summary==" );
		#$wgOut->redirect( $_SERVER["HTTP_REFERER"]);
		#$wgOut->setPageTitle( "Karamel" );
		#echo $wgUser->getName();
		#echo shell_exec('php ./maintenance/edit.php -m Main_Page < text.txt');
		#$title = Title::newFromText("Main_Page");
		#echo $title->getBaseText();
		#echo $_SERVER["HTTP_REFERER"];
		$title = Title::newFromText("Main_Page");
		$page = WikiPage::factory($title);

		$text = "test";
		$summary = "1st edit";
		$content = ContentHandler::makeContent( $text, $title );
		$page->doEditContent($content, $summary);
		echo "Hello World ";
		echo $wgParser->getTitle();
		echo is_object( $wgParser );
		echo $wgParser->mOutput->mSections;
		self::$needed_url = $_SERVER["HTTP_REFERER"];
		echo self::$needed_url;
		$x = $_SERVER["HTTP_REFERER"];
		echo $x;
		return true;

    }
    public static function onUploadForm_BeforeProcessing( SpecialUpload $uploadFormObj ) {
		global $wgOut;
		$wgOut->redirect($_SERVER["HTTP_REFERER"]);
    }
}


/*class FileAttachDummyOutput {
	function redirect( $url ) {
		global $wgOut;
		$wgOut = FileProcess::$wgOut;
		$wgOut->redirect( $_SERVER["HTTP_REFERER"] );
	}
}*/

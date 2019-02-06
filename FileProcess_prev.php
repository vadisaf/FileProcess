<?php
use MediaWiki\MediaWikiServices;
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

class FileProcess {

    public static function writeToFile($file, $link) {
    		$fp = fopen($file, 'w');
		fwrite($fp, $link);
		fclose($fp);

		return true;
    }

    public static function injectHTMLInEditor(&$editPage,&$output) {
		$editPage->editFormTextAfterWarn .='<div id="demoDiv" class="highlight-green">****Hi This is HTML Injected via Demo Extension****</div>';
		return true;
    }

    public static function onSpecialUploadComplete( $form ) {
		global $wgOut;
		//get reference for redirection (to the page where file needs to be)
		$reference = file_get_contents('/var/www/html/files/ref');

		//extracting page name from the link
		$page_name = substr(strrchr($reference, '/'), 1);
		//vs451: TBD
		if (strpos($page_name, ':') || strpos($page_name, '&') || strpos($page_name, 'Main_Page')) {
			#$file = new SpecialUpload();
			$wgOut->redirect("http://127.0.0.1/index.php/Main_Page");
		}


		//extracting the file name from another link
		$file_name = substr(strrchr(file_get_contents('/var/www/html/files/ref1'), '/'), 1);

		//process upoaded file using python script
		$short_fn = substr(strrchr($file_name, ':'), 1);
		$output = shell_exec("python3 ".__DIR__."/process.py ".$short_fn);


		//editing the page from which upload request came
		$title = Title::newFromText($page_name);
		$page = WikiPage::factory($title);
		$prev_content = $page->getContent()->getNativeData();
		$text = substr($prev_content, 0, -2)."| [[".$file_name."]] || ".$output."\n|-\n|}";
		#[[Special:Upload|UPLOAD]]  [[".$file_name."]]";
		$summary = "#st edit";
		$content = ContentHandler::makeContent( $text, $title );
		$page->doEditContent($content, $summary);



		//redirect to the page from which upload request came
		/*NOTE: redirection only works when the line 581 (which calls the File page) in SpecialUpload.php is commented.
		Otherwise you will go to the page with file details, but not to the initial page from which upload request was called*/
		$wgOut->redirect($reference);
		return true;
    }
    public static function onUploadForm_initial( SpecialUpload $uploadFormObj ) {
		global $wgOut, $wgRequest, $wgUser, $wgParser;
		//put reference from the previous page into file
		self::writeToFile('/var/www/html/files/ref', $_SERVER["HTTP_REFERER"]);



		# puts reference to the previous page into a file
		#$file = __DIR__ . '/ref';
		#$content = "HELLO";
		#$fp = fopen('./ref', 'w');
		#fwrite($fp, 'PROCESSED');
		#fclose($fp);
		#file_put_contents($file, $content);
		#$wgOut->addWikiText( "==Summary==" );

		//how to add text on page
		/*$title = Title::newFromText("Main_Page");
		$page = WikiPage::factory($title);

		$text = "test";
		$summary = "1st edit";
		$content = ContentHandler::makeContent( $text, $title );
		$page->doEditContent($content, $summary);*/
		#echo "Hello World ";
		/*some parser commands:
		echo $wgParser->getTitle();
		echo is_object( $wgParser );
		echo $wgParser->mOutput->mSections;*/
		#echo gettype($_SERVER["HTTP_REFERER"]);
		#shell_exec('php /var/www/html/extensions/FileProcess/test.php');

		return true;

    }

    //this function is not used currently
    public static function onUploadForm_BeforeProcessing( SpecialUpload $uploadFormObj ) {
		global $wgOut;

    }

}

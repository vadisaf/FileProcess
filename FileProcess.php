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


//hook functions
$wgHooks['UploadComplete'][] = 'FileProcess::onSpecialUploadComplete';
$wgHooks['UploadForm:initial'][] = 'FileProcess::onUploadForm_initial';
$wgHooks['UploadForm:BeforeProcessing'][] = 'FileProcess::onUploadForm_BeforeProcessing';

class FileProcess {
    //writeToFile() is used to save references to file and page link
    public static function writeToFile($file, $link) {
    $fp = fopen($file, 'w');
		fwrite($fp, $link);
		fclose($fp);

		return true;
    }

    //triggered when file is uploaded
    public static function onSpecialUploadComplete( $form ) {

		    global $wgOut;
		    //get reference for redirection (to the page where file needs to be)
		    $reference = file_get_contents('/var/www/html/files/ref');
		    //extracting page name from the link
		    $page_name = substr(strrchr($reference, '/'), 1);
    //extracting the file name from another link
    $file_full_link = file_get_contents('/var/www/html/files/ref1');
		$file_name = substr(strrchr(file_get_contents('/var/www/html/files/ref1'), '/'), 1);


		if (strpos($page_name, ':') || strpos($page_name, '&') || strpos($reference, 'Main_Page')) {
			#$wgOut->redirect("http://127.0.0.1/index.php/Main_Page");
      $wgOut->redirect($file_full_link);
      return true;
		} else {
      //process upoaded file using python script
  		$short_fn = substr(strrchr($file_name, ':'), 1);
  		$output = shell_exec("python3 ".__DIR__."/process.py ".$short_fn);


  		//editing the page from which upload request came
  		$title = Title::newFromText($page_name);
  		$page = WikiPage::factory($title);
  		$prev_content = $page->getContent()->getNativeData();
  		$text = substr($prev_content, 0, -2)."| [[".$file_name."]] || ".$output."\n|-\n|}";
  		#[[Special:Upload|UPLOAD]]  [[".$file_name."]]";
  		$summary = "new file uploaded";
  		$content = ContentHandler::makeContent( $text, $title );
  		$page->doEditContent($content, $summary);
      //redirect to the page from which upload request came
  		/*NOTE: redirection only works when the line 581 (which calls the File page) in SpecialUpload.php is commented.
  		Otherwise you will go to the page with file details, but not to the initial page from which upload request was called*/
  		$wgOut->redirect($reference);
  		return true;
    }

}
    public static function onUploadForm_initial( SpecialUpload $uploadFormObj ) {
		global $wgOut, $wgRequest, $wgUser, $wgParser;
		//put reference from the previous page into file
		self::writeToFile('/var/www/html/files/ref', $_SERVER["HTTP_REFERER"]);

		return true;

    }

    //this function is not used currently
    public static function onUploadForm_BeforeProcessing( SpecialUpload $uploadFormObj ) {
		global $wgOut;

    }

}

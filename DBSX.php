<?php
//inicio
//headers meta tags & cache settings
@require_once('DBSX/FRAMEWORKS.php');
//Delete all cache
@header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
@header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
@header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
@header("Cache-Control: post-check=0, pre-check=0", false);
@header("Pragma: no-cache");
//Debug errors
@ini_set("display_errors","1");
@ini_set("display_startup_errors","1");
?>


<?php

//Begin Main Class
class DBSX {

    
/*
Minimum Requeriments :
    PHP 7.4.26
    MySQL 5.7.36
*/

//configurations
public $preloader = true;

//cms location ***
public $cms_location = 'CMS/';

//internal
public $version = '3.0';
public $msg = '';

public $page_url; // complete page url
public $page_url_protocol; // protocol
public $page_file_name; // url with pagename only
public $page_url_noparams; // url without pagename
public $page_url_params; // url params only
    
public $id = ''; // id param
public $c = ''; // category param
public $sc = ''; // sub category param    
    
private $ITEM_RESULTS_FIRST;
private $ITEM_RESULTS_COUNT;
private $ITEM_RESULTS_PER_PAGE;

//Main subclasses objects
public SQL $SQL;
public LISTVIEW $LISTVIEW;
public GALLERY $GALLERY;
public BANNER $BANNER;

public $BANNER_ID = 0;
public DATA_LIST $DATA_LIST;

public const MSG_ERROR_INIT = '<span class="DBSX_LOG_ERROR">';
public const MSG_SUCCESSFUL_INIT = '<span class="DBSX_LOG_SUCCESSFUL">';
public const MSG_END = '</span>';
    
    //Main Constructor
    function __construct() {

        //Init Subclasses
        $this->msg .= "[ DBSX ] Object was successful created" . "\n<br>";
        $this->SQL = new SQL($this);
        $this->LISTVIEW = new LISTVIEW($this);
        $this->GALLERY = new GALLERY($this);
        $this->BANNER = new BANNER($this);
        $this->DATA_LIST = new DATA_LIST($this);
        
        //init global get params
        $this->id = @$_GET['id'];
        $this->c = @$_GET['c'];
        $this->sc = @$_GET['sc'];
        
        $this->page_url_protocol = 'http';
        if (@$_SERVER["HTTPS"] == "on") {
            $this->page_url_protocol .= "s";
        }
        $this->page_url_protocol .= "://";

        $this->page_url .= $this->page_url_protocol . $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
            
        //page file name
        $PageWithoutParams = strstr($this->page_url, '?', true);
        if ($PageWithoutParams == '') $PageWithoutParams = $this->page_url;
        $this->page_file_name = basename($PageWithoutParams);

        //page url without params
        $PageWithoutParams = strstr($this->page_url, '?', true);
        if ($PageWithoutParams == '') $PageWithoutParams = $this->page_url;
        $this->page_url_noparams = $PageWithoutParams;

        //page params
        $PageParams = strstr($this->page_url, '?', false);
        $this->page_url_params = $PageParams;
            
    }


    // Returns a random number
    function RANDOM_NUMBER($digitsLength) {
    $random = substr(number_format(time() * rand(), 0, '', ''), 0, $digitsLength);
    $this->msg .= $this::MSG_SUCCESSFUL_INIT . "RANDOM_NUMBER was generated successful" . $this::MSG_END . "\n<br>";
    return $random;
    }

    //debug messages
    function LOG() {
        $output = '<div class="DBSX_LOG">' . '<span style="color:#f90">DBSX Debug Console : </span><br>' . $this->msg . '</div>' ;
        echo $output;
    }


    //Fix url with params and assign them to url_params
    function FIX_DATA_URL($str_url,$DataObject) {
        $WithoutParams = '';
        $WithoutParams = strstr($str_url, '?', true);
        $Params = strstr($str_url, '?');

        if ($WithoutParams != '') {
        $DataObject->data_url = $WithoutParams;
        }

        $DataObject->data_params = str_replace("?", "&" ,$DataObject->data_params);    
    }
    
}

@require_once('DBSX/SQL.php');
@require_once('DBSX/BANNER.php');
@require_once('DBSX/LISTVIEW.php');
@require_once('DBSX/GALLERY.php');
@require_once('DBSX/DATA_LIST.php');

?>
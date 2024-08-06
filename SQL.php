<?php

class SQL extends DBSX  {

public $version = 'MySQL 5.7.36';
private $DBSX = '';

private $query = '';
private string $string = '';
public $data = '';
    
//SQL database default conection local

public string $server = 'localhost';
public string $user = 'root';
public string $pass = '';
public string $dbname = 'zoologia';

//SQL database default conection server
/*
public string $server = 'localhost';
public string $user = 'netsites_simposio';
public string $pass = 'M^MFyRJER89k';
public string $dbname = 'netsites_simposio';
*/

public $link = '';


    //Main Constructor
    function __construct($DBSX) {
        $this->DBSX = $DBSX;
        $this->query = '';
        $this->link = '';
        $this->DBSX->msg .= "[ SQL ] Object was successful created" . "\n<br>";
    }

    //Internal functions declarations.
    function OPEN($server='',$user='',$pass='',$dbname='') {
        
        //Set arguments as class properties
        if ($server != '') $this->server = $server;
        if ($user != '') $this->user = $user;
        if ($pass != '') $this->pass = $pass;
        if ($dbname != '') $this->dbname = $dbname;
        
        $this->link = @mysqli_connect($this->server,$this->user,$this->pass,$this->dbname);

        if (!@$this->link) {
            $this->DBSX->msg .= $this->DBSX::MSG_ERROR_INIT . "Error trying to connect to server : '" . $this->server . "' | " . mysqli_connect_error() . $this->DBSX::MSG_END . "\n<br>";
            return false;
        }
        else {
            $this->DBSX->msg .= "Connection established with server : '" . $this->server . "'\n<br>";
            if (@$this->link->query("USE $this->dbname")) {
                $this->DBSX->msg .= "Database " . $this->dbname . " was succesfully selected and it's ready for next operations" . "\n<br>";	
                return true;
            }
            else {
                $this->DBSX->msg .= $this->DBSX::MSG_ERROR_INIT . "Error trying to select database : '" . $this->dbname . "'  |  " . @mysqli_error($this->link) . $this->DBSX::MSG_END . "\n<br>";
                return false;
            }
        }
    }
    
    //End mysqli connection
    function CLOSE() {
        if (@$this->link) {
            if ($this->link->close()) {
                //Delete data contained on main variables and memory
                $this->DBSX->msg .= $this->DBSX::MSG_SUCCESSFUL_INIT . 'Connection to the server was closed' . $this->DBSX::MSG_END . "\n<br>";
                $this->link = null;
            }
        }
        
        //preloader control
        if ($this->DBSX->preloader) {

        echo <<<EOT

        <!-- Preloader -->
        <div id="preloader">
          <div id="status">&nbsp;</div>
        </div>

        <script>
        $(window).on('load', function() { // makes sure the whole site is loaded 
          $('#status').delay(1200).fadeOut(); // will first fade out the loading animation 
          $('#preloader').delay(1200).fadeOut(); // will fade out the white DIV that covers the website. 
          $('body').delay(350).css({'overflow':'visible'});
        })
        </script>

        EOT;

        }

    }
    
    //SQL Query
    function QUERY($query_string) {
        
        $returnArray = array();
        $currentIndex = 0;
        
        $this->string = $query_string;
        
        if (@$this->link) {
            @$this->query = @$this->link->query($this->string);
        }
        else {
         $this->query = '';
        }
        
        if (!@$this->query) {
            
            $this->DBSX->msg .= $this->DBSX::MSG_ERROR_INIT . " >>> Query error trying to getting data from $query_string : " . @mysqli_error($this->link) . $this->DBSX::MSG_END . "\n<br>";
            //$this->DBSX->LOG();
            return false;
            
        }
        else {
            
            if ($this->query->num_rows > 0) {
            $this->DBSX->msg .= $this->DBSX::MSG_SUCCESSFUL_INIT . "Query $query_string was successful returning data" . $this->DBSX::MSG_END . "\n<br>";
            while ($this->data = $this->query->fetch_array(MYSQLI_ASSOC)) {
                if ($this->data != '') {
                $currentIndex += 1;
                $returnArray[$currentIndex] = $this->data;
                }
            }
            $this->data = $returnArray;
            //var_dump($returnArray);
            return $returnArray;
            }
            else {
            return false;
            }
        }            
    }
    
    //Return specific single field value
    function GET_DATA_FIELD(string $paramTable='',string $paramField='',int $registryID=1) {
        if ($paramTable == '') ;
        $this->string = "SELECT $paramField FROM $paramTable WHERE id = $registryID";
        $this->query = $this->QUERY($this->string);
        if (!@$this->query) {
        $this->DBSX->msg .= $this->DBSX::MSG_ERROR_INIT . " >>> GET_DATA_FIELD errors $this->string doesn't exists " . @mysqli_error($this->link) . $this->DBSX::MSG_END . "\n<br>";
        return false;
        }
        else {
            //var_dump($this->data);
            //exit;
            foreach ($this->data as $item => $data) {
                if ($data != '') {
                $this->DBSX->msg .= $this->DBSX::MSG_SUCCESSFUL_INIT . "GET_DATA_FIELD recived data successful" . $this->DBSX::MSG_END . "\n<br>";
                return @$data[$paramField];
                }
                else {
                $this->DBSX->msg .= $this->DBSX::MSG_ERROR_INIT . " >>> GET_DATA_FIELD errors field $paramField from table $paramTable " . @mysqli_error($this->link)  . $this->DBSX::MSG_END . "\n<br>";
                return false;
                }
            }
        }
    }

    //Return a single item as associative array with fields specified in requested id
    function GET_DATA_ROW(string $paramTable = '',$paramFields = '*',int $registryID = 1) {
        
        $this->string = "SELECT $paramFields FROM $paramTable WHERE id = $registryID";
        $this->query = $this->QUERY($this->string);
        if (!@$this->query) {
        $this->DBSX->msg .= $this->DBSX::MSG_ERROR_INIT . " >>> GET_DATA_ROW errors $this->string doesn't exists " . @mysqli_error($this->link) . $this->DBSX::MSG_END . "\n<br>";
        return false;
        }
        else {
            //var_dump($this->data);
            //exit;
            foreach ($this->data as $item => $data) {
                if ($data != '') {
                $this->DBSX->msg .= $this->DBSX::MSG_SUCCESSFUL_INIT . "GET_DATA_ROW recived data successful" . $this->DBSX::MSG_END . "\n<br>";
                return $data;
                }
                else {
                $this->DBSX->msg .= $this->DBSX::MSG_ERROR_INIT . " >>> GET_DATA_ROW errors field $paramFields from table $paramTable " . @mysqli_error($this->link)  . $this->DBSX::MSG_END . "\n<br>";
                return false;
                }
            }
        }
    }
    
    function GET_DATA_FROM(string $paramTable,string $paramField,int $registryID = 1) {
        $this->string = "SELECT * FROM $paramTable WHERE id = $registryID";
        $this->query = $this->query($this->string);
 
        if (!$this->query) {
            $this->DBSX->msg .= $this->DBSX::MSG_ERROR_INIT . "GET_DATA_FROM_IMAGES error trying to getting data from images on table $paramTable field $paramField registry $registryID : " . @mysqli_error($this->link)  . $this->DBSX::MSG_END . "\n<br>";
            return 0;
        }
        else {
        //First we create an Array to contain each image name
        $imgDA = array(); // Image Data Array
        $imgNACE = 0; // Image Names Array Current Element
        
        var_dump($this->query);
        exit();
        while ($this->data = $this->query->fetch_assoc()) {
            if ($this->data != '') {
                $this->DBSX->msg .= $this->DBSX::MSG_SUCCESSFUL_INIT . "GET_DATA_FROM_IMAGES Data was received from table $paramTable " . $this->DBSX::MSG_END . "\n<br>";
                //Recover data saved by phprunner (as JSON)
                $imgObtained = $this->data[$paramField];
                //Decode JSON String
                $objImgObtained = $imgObtained;
                $objImgObtained = json_decode($imgObtained);
                $index2 = 0;
                while ($index2 < count($objImgObtained)) {
                    //Url prefix fix
                    $imgNameObtained = $this->DBSX->cms_location . $objImgObtained[$index2]->name;
                    $imgDA[$index2] = $imgNameObtained; //Set Images data on current element of Image Name Array
                    $index2 += 1;
                }
                $imgNACE += 1; //Increse by one current element
            }
            else {
                $this->DBSX->msg .= $this->DBSX::MSG_ERROR_INIT . "GET_DATA_FROM_IMAGES error Datafield $paramField from table $paramTable is empty "  . @mysqli_error($this->link)  . $this->DBSX::MSG_END . "\n<br>"; 
            }
            
        } // End While
        return $imgDA;
        }	
    }
    

}

?>
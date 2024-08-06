<?php

class DATA_LIST extends DBSX  {

    public $data_field = 'descargas';
    public $data_table = '';
    public $data_id = 1;
        
     private $DBSX = '';
        
        function __construct($DBSX) {
            $this->DBSX = $DBSX;
            $this->DBSX->msg .= "[ FILES ] Object was successful created" . "\n<br>";
        }
        
        function RENDER() {
        /*
        $paramTable = $this->data_table;
        $paramField = $this->data_field;
        $registryID = $this->data_id;
        */
        $datafiles = $this->DBSX->SQL->GET_DATA_FIELD($this->data_table,$this->data_field,$this->data_id);

        if (!$datafiles) {
            return false;
        }

        $json_data = json_decode($datafiles);

        $output = '';
        foreach ($json_data as $item => $value) {    
            
            $ext = @pathinfo($value, PATHINFO_EXTENSION);
            $variable = $value;
            $colpos = @strrpos($variable, "_");
            $result = @substr($variable, 0, $colpos);       
            
            $output .= '<div class="py-1"><a style="color:#555; font-weight:bold;" href="' .  $this->DBSX->cms_location.$value->name .'" target="_blank"><i style="color:#555; padding-right:10px; display: inline-block !important; font: normal normal normal 14px/1 FontAwesome !important;" class="fa fa-file fa-lg"></i>'.$value->usrName.'</a></div>';
        }

        //var_dump($value->usrName);
        //exit();

        return $output;

        }
        
}

?>
<?php

class GALLERY extends DBSX  {

    public $data_images_field = 'imagenes';
    public $data_table = '';
    public $data_id = 1;
    public $image_max_height = 'auto';
    
    public $image_width = '';
    
    
     private $DBSX = '';
        
        function __construct($DBSX) {
            $this->DBSX = $DBSX;
            $this->DBSX->msg .= "[ GALLERY ] Object was successful created" . "\n<br>";
        }
        
        function RENDER() {
            
            $images_data = $this->DBSX->SQL->GET_DATA_FIELD($this->data_table,$this->data_images_field,$this->data_id);
    
            if (!$images_data) {
                return false;
            }
            $output = '';
            $output .= '
                        <div class="lightbox-gallery">
                        <div class="container-fluid">
                        <div class="row photos">
                        ';
            
            //convert string from images_data_field to json object to access | name, usrName, size, type
            $json_data = json_decode($images_data);
            //var_dump($this->json_data);
            $json_images_num = @count($json_data);
            if ($json_images_num > 0) {
                foreach ($json_data as $item => $data) {
                    //echo $data->name . '<br>';
                    //if (@count($this->json_data) < 4) {
                    $url_image = $this->DBSX->cms_location . $data->name;
                    $grid_class = '';
                    
                    
                        if ($json_images_num = 1) $grid_class = "col-sm-12 col-md-12 col-lg-12 text-center item img"; //img || img-thumbnail
                        if ($json_images_num = 2) $grid_class = "col-sm-12 col-md-6 col-lg-6 text-center item img";
                        if ($json_images_num = 3) $grid_class = "col-sm-12 col-md-4 col-lg-4 text-center item img";  
                        if ($json_images_num >= 4) $grid_class = "col-sm-12 col-md-3 col-lg-3 text-center item img";
                    
                    
                    if ($this->image_width == '') {
                    $output .= '<div class="'.$grid_class.'"><a href="'.$url_image.'" rel="lightbox" ><img border="0" class="img-fluid" style="max-height:'.$this->image_max_height.' !important; " src="'.$url_image.'"></a></div>';
                    }
                    else {
                    $output .= '<div class="'.$grid_class.'"><a href="'.$url_image.'" rel="lightbox" ><img border="0" class="img" style="max-height:'.$this->image_max_height.' !important; " src="'.$url_image.'" width="'.$this->image_width .'"></a></div>';
                    }
                    //$output .= '<div class="'.$grid_class.'"><a href="'.$url_image.'" rel="lightbox" ><img class="img-fluid" src="'.$url_image.'"></a></div>';
                    //}
                }
            $this->DBSX->msg .= $this->DBSX::MSG_SUCCESSFUL_INIT . "GALLERY Object was RENDER successful" . $this->DBSX::MSG_END . "\n<br>";
            $output .= '</div>
                        </div>
                        </div>
                        ';
            return $output;
            }
            else {
            $this->DBSX->msg .= $this->DBSX::MSG_ERROR_INIT . " >>> GALLERY Object doesn't have any images" . $this->DBSX::MSG_END . "\n<br>";
            return false;
            }
    
        }
    }

?>
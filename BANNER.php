<?php

class BANNER extends DBSX  {

    public $data_conditions = '';
    public $data_image_field = 'imagenes';
    public $data_fields = '*';
    public $data_table = '';
    public $data_url = '';
    private $default_data_url = '';
    public $data_params = '';
    public $data_id = 1;
    public $banner_height = 'auto';
    public $banner_width = '100%';
    
    public $banner_use_captions = true;
    public $banner_use_title = true;
    public $banner_use_description = true;
    
    public $banner_data = '';
    
    public $data_json = '';
    
    public $CustomURLFieldName = 'archivo_externo';
    
    private $DBSX = '';
                    
        function __construct($DBSX) {
            $this->DBSX = $DBSX;
            $this->DBSX->BANNER_ID++;
            $this->DBSX->msg .= "[ GALLERY ] Object was successful created" . "\n<br>";
        }
                
        function RENDER() {
            $this->default_data_url = $this->data_url;
            //echo $this->data_image_field;
            $index = 0;
            $itemindex = 1;
            $classcode = '';
            $itemclasscode = '';
            $imagen = '';
    
            $textAlign = 'start';
    
            //fix url & params probelms
            $this->DBSX->FIX_DATA_URL($this->data_url,$this);
    
            if ($this->data_conditions == '') $this->data_conditions = ' 1=1 ';
                    
            if ($this->data_json == '') {
            $banner_data = $this->DBSX->SQL->QUERY("SELECT $this->data_fields FROM $this->data_table WHERE $this->data_conditions");  //$this->data_table,$this->data_images_field,$this->data_id);
            //$json_data = json_decode($images_data);
            
            $output = '';
            $output .= '<div id="DBSX_Carousel' . $this->DBSX->BANNER_ID . '" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-pause="false" data-bs-interval="4000" >';
            $output .= '<div class="carousel-indicators">';
            foreach ($banner_data as $item => $property) {
                    if ($index == 0) {
                        $classcode = 'class="active" aria-current="true"';
                    }
                    else {
                        $classcode = '';
                    }
            $output .= '<button type="button" data-bs-target="#DBSX_Carousel' . $this->DBSX->BANNER_ID . '" data-bs-slide-to="'.$index.'" '.$classcode.' aria-label="Slide '.($index+1).'"></button>';
            $index++;
            }
            $output .= '</div>';
    
            $output .= '<div class="carousel-inner text-center">';
    
            
            foreach ($banner_data as $item => $property) {
    
                $portadas = json_decode($property[$this->data_image_field]);
                foreach ($portadas as $items => $values) {
                    $imagen = $values->name;
                }
     
                if ($itemindex == 1) {
                    $itemclasscode = 'class="carousel-item active"';
                }
                else {
                    $itemclasscode = 'class="carousel-item"';
                }
                
                
                //if is not defined a File check if is defined CustomURLFieldName
                if (@$property[$this->CustomURLFieldName] != NULL) {
                    $this->data_url = $property[$this->CustomURLFieldName];
                    
                }
                else {
                    //restore value
                    $this->data_url = $this->default_data_url;
                    //$this->DBSX->msg .= ">>>>>>>" . $property[$this->CustomURLFieldName];
                }
    
                $output .= '<div '.$itemclasscode.'">';
    
                if ($this->data_url != '') {
                    //if ($this->data_params != '') {
                        $output .= '<a href="'.$this->data_url.'?id='.$property['id'].'&'.$this->data_params.'">'.'<img src="'.$this->DBSX->cms_location . $imagen.'" width="'.$this->banner_width.'" height="'.$this->banner_height.'">'.'</a>';
                        //$output .= $this->data_params . 'PARAMETROS';
                    //}
                    /*
                    else {
                        $output .= '<a href="'.$this->data_url.'?id='.$property['id'].'">'.'<img src="'.$this->DBSX->cms_location . $imagen.'" width="'.$this->banner_width.'" height="'.$this->banner_height.'">'.'</a>';
                        
                    }
                    */
                }
                else {
                $output .= '<img src="'.$this->DBSX->cms_location . $imagen.'" width="'.$this->banner_width.'" height="'.$this->banner_height.'" >';
                }
    
                if ($this->banner_use_captions) {
                    $output .= '<div class="container">';
                    $output .= '<div class="carousel-caption text-start bg-dark p-4 bg-opacity-75 ">';
                    if ($this->banner_use_title) {
                    $output .= '<h2>'.$property['titulo'].'</h2>';
                    }
                    if ($this->banner_use_description) {
                    $output .= '<p>'.$property['resumen'].'</p>';
                    }
                    $output .= '</div>';
                    $output .= '</div>';
                }
    
                $output .= '</div>';
            
            $itemindex++;
            }
    
            $output .= '</div>';
    
            $output .= '<button class="carousel-control-prev" type="button" data-bs-target="#DBSX_Carousel' . $this->DBSX->BANNER_ID . '" data-bs-slide="prev">';
            $output .= '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
            $output .= '<span class="visually-hidden">Previous</span>';
            $output .= '</button>';
            $output .= '<button class="carousel-control-next" type="button" data-bs-target="#DBSX_Carousel' . $this->DBSX->BANNER_ID . '" data-bs-slide="next">';
            $output .= '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
            $output .= '<span class="visually-hidden">Next</span>';
            $output .= '</button>';
            $output .= '</div>';
    
            $this->banner_data = $banner_data;
    
            return utf8_encode($output);
    
            }
    
    
        }
                    
    }

    ?>
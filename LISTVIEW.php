<?php

class LISTVIEW extends DBSX  {

    /* //////////////////////////////////////////////////////////////////
    Configuration Variables
    Default field names that must match with DB fields to be constructed
    If DB fields are not the same then need to be defined after call RENDER()
    */
    
    //DB Structure must match with DBFields
    public $layoutFieldNames = array(
        "MainImage" => "portada",  
        "MainImageCustomURL" => "",
        "Title" => "titulo",
        "Description" => "descripcion",
        "Date" => "fecha",
        "InternalFile" => "documento",
        "ExternalFile" => "url",
        "Category" => "",
        "SubCategory" => ""
    );
    
    
    public $ActiveFieldName = '';
    
    //values used to specify how much columns extends max 12
    public int $layoutLeftExpand = 2;
    public int $layoutRightExpand = 10;
    
    
    public string $layoutClass = 'text-start';
    
    
    
    //Custom public properties
    public string $image_max_height = 'auto';
    public string $image_height = '';
    public bool $use_pagination = true;
    
    private $RowIndex = 0;
    public $ordered = true;
    public $data = '';
    public $data_url = '';
    private $default_data_url = '';
    public $data_params = '';
    public $data_table = '';
    //public $data_fields = "id, $this->MainImageFieldName , $this->TitleFieldName , $this->DescriptionFieldName";
    public $data_fields = '*';
    public $data_conditions = '';
    
    public $data_target = '_self';
        
    
    /* ////////////////////////////////////////////////////////////////// */
    //Internal vars
    private $DBSX = '';
    public $layout = 1; // 1-Default List | 2-Blocks design
    public $layoutColumns = 3;
    private $URLdetails = '';
    public $ItemsPerPage = 0;
    private $cp; // Current page
    private $fr;
    private $TotalItems;
    private $next_status = 'enabled';
    private $prev_status = 'enabled';
    private $AlternatedIndex;
    private $next_url;
    private $prev_url;
        
        //Main Constructor
        function __construct($DBSX) {
            $this->DBSX = $DBSX;
            $this->DBSX->msg .= "[ LISTVIEW ] Object was successful created" . "\n<br>";
            
            $this->data_fields = "id, " . $this->layoutFieldNames["MainImage"] . " , " . $this->layoutFieldNames["Title"] . " , " . $this->layoutFieldNames["Description"];
    
            if ($this->ItemsPerPage == 0) $this->ItemsPerPage = 5;
            $this->cp = @$_GET['cp'];
            $this->fr = @$_GET['fr'];
            
            if (!$this->cp) { $this->cp = 1; }
            if (!$this->fr) { $this->fr = 0; }
    
        }
        
        //LISTVIEW RENDER
        function RENDER() {
    
            //remember initial data url
            $this->default_data_url = $this->data_url;
    
             //fix url & params probelms
            //$this->DBSX->FIX_DATA_URL($this->data_url,$this);
            
            if ($this->data_conditions == '') {
                $this->data_conditions = '1=1';
            }
    
            //Control categoty & subcategory by params
            if ($this->DBSX->c != '') {
                if ($this->data_conditions == '') $this->data_conditions .= ' ' . $this->layoutFieldNames["Category"] . ' = ' . $this->DBSX->c;
                if ($this->data_conditions != '') $this->data_conditions .= ' AND ' . $this->layoutFieldNames["Category"] . ' = ' . $this->DBSX->c;
            }
            if ($this->DBSX->sc != '') {
                if ($this->data_conditions == '') $this->data_conditions .= ' ' . $this->layoutFieldNames["SubCategory"] . ' = ' . $this->DBSX->sc;
                if ($this->data_conditions != '') $this->data_conditions .= ' AND ' . $this->layoutFieldNames["SubCategory"] . ' = ' . $this->DBSX->sc;
            }
            
            $output = '';
    
            $sql_query_string = "SELECT $this->data_fields FROM $this->data_table WHERE $this->data_conditions LIMIT $this->fr , $this->ItemsPerPage";      
            $this->TotalItems = @count($this->DBSX->SQL->QUERY("SELECT id FROM $this->data_table WHERE $this->data_conditions"));
            $this->data = @$this->DBSX->SQL->QUERY($sql_query_string);
            //$this->data = $sql_query_data;
            //var_dump($this->data);
            //exit();
            
            if ($this->data) {
                $base_params = '';
                if ($this->DBSX->c != '') $base_params .= 'c='.$this->DBSX->c.'&';
                if ($this->DBSX->sc != '') $base_params .= 'sc='.$this->DBSX->sc.'&';
                $this->next_url = $this->DBSX->page_url_noparams . '?'.$base_params.'cp=' . ($this->cp+1) . '&fr=' . ( $this->cp * $this->ItemsPerPage );
                $this->prev_url = $this->DBSX->page_url_noparams . '?'.$base_params.'cp=' . ($this->cp-1) . '&fr=' . ( ($this->cp-2) * $this->ItemsPerPage );
    
                $output .= '<div class="row  pt-0 pb-0 align-items-start DBSX_LISTVIEW">';
                foreach ($this->data as $item => $data) {
                    $this->RowIndex++;
                    //MainImage
                    $MainImage = $data[$this->layoutFieldNames["MainImage"]];
                    $MainImageData = json_decode($MainImage);
                    $MainImageURL = '';
    
                    //if is defined a FileFieldName link to internal file
                    if (@$data[$this->layoutFieldNames["InternalFile"]] && !$data[$this->layoutFieldNames["ExternalFile"]]) {
                        $file_data = json_decode($data[$this->layoutFieldNames["InternalFile"]]);
                        foreach ($file_data as $item => $filedata) {
                           $this->data_url = $this->DBSX->cms_location . $filedata->name;
                        }
                        $this->data_target = '_blank';
                    }
                    else {
                        //if is not defined a File check if is defined CustomURLFieldName
                        if (@$data[$this->layoutFieldNames["ExternalFile"]]) {
                            $this->data_url = $data[$this->layoutFieldNames["ExternalFile"]];
                            $this->data_target = '_blank';
                        }
                    }
                    //
                    //$this->layoutFieldNames["MainImageCustomURL"] == ''
                    if (@$this->layoutFieldNames["MainImageCustomURL"]) {
                       //foreach ($MainImageData as $item => $dataImage) {
                            $MainImageURL = $this->layoutFieldNames["MainImageCustomURL"];
                        //    break;
                        //}
                    }
                    else {
                        //$MainImageURL = $this->layoutFieldNames["MainImageCustomURL"];
                        foreach ($MainImageData as $item => $dataImage) {
                            $MainImageURL = $this->DBSX->cms_location . $dataImage->name;
                            break;
                        }
                    }
    
                    // ***** Normal List Design & ordered *****
                    if ($this->layout == 1 && $this->ordered) {
                        $output .= '<div class="row  pt-0 pb-0 align-items-start DBSX_LISTVIEW">';
                        $output .= '<div class="col-12 col-md-' . $this->layoutLeftExpand . ' pb-4 ' .  '">';
    
                        $output .= '<center><a href="'.$this->data_url.'?id='.$data['id'].$this->data_params.'" target="'.$this->data_target.'">'.'<img src="'.$MainImageURL.'"' . ' class="img-fluid" ' .' style="max-height:'.$this->image_max_height.' !important; height:'.$this->image_height.'px; ">' . '</a></center>';
    
                        $output .= '</div>';
                        $output .= '<div class="col-12 col-md-' . $this->layoutRightExpand . ' pb-5 ' . $this->layoutClass . '">';
                        $output .= '<h4>' . '<a href="'.$this->data_url.'?id='.$data['id'].$this->data_params.'" target="'.$this->data_target.'">' . @$data[$this->layoutFieldNames["Title"]] . '</a>' . '</h4>';
                        if (@$this->layoutFieldNames["Date"] != '' && @$data[$this->layoutFieldNames["Date"]]) {
                            $date = date_create($data[@$this->layoutFieldNames["Date"]]);
                            $formated_date = date_format($date,"d/m/Y");
                            $output .= $formated_date . '<br>';
                        }
                        
                        $output .= $data[$this->layoutFieldNames["Description"]];
                        //$output .= $this->RowIndex;
                        $output .= '</div>';
                        $output .= '</div>';
                    }
                    // ***** Normal List Design & unordered *****
                    if ($this->layout == 1 && !$this->ordered) {
    
                        if ($this->RowIndex == 1) {
                            $output .= '<div class="row  pt-0 pb-0 align-items-start DBSX_LISTVIEW">';
                            $output .= '<div class="col-12 col-md-' . $this->layoutLeftExpand . ' pb-4 ' .  '">';
    
                            $output .= '<center><a href="'.$this->data_url.'?id='.$data['id'].$this->data_params.'" target="'.$this->data_target.'">'.'<img src="'.$MainImageURL.'"' . ' class="img-fluid" ' .' style="max-height:'.$this->image_max_height.' !important; height:'.$this->image_height.'px; ">' . '</a></center>';
    
                            $output .= '</div>';
                            $output .= '<div class="col-12 col-md-' . $this->layoutRightExpand . ' pb-5 ' . $this->layoutClass . '">';
                            $output .= '<h4>' . '<a href="'.$this->data_url.'?id='.$data['id'].$this->data_params.'" target="'.$this->data_target.'">' . $data[$this->layoutFieldNames["Title"]] . '</a>' . '</h4>';
                            if ($this->layoutFieldNames["Date"] != '' && $data[$this->layoutFieldNames["Date"]]) {
                                $date = date_create($data[$this->layoutFieldNames["Date"]]);
                                $formated_date = date_format($date,"d/m/Y");
                                $output .= $formated_date . '<br>';
                            }
                            
                            $output .= $data[$this->layoutFieldNames["Description"]];
                            $output .= '</div>';
                            $output .= '</div>';
                        }
                        else if ($this->RowIndex == 2) {
                            $output .= '<div class="row  pt-0 pb-0 align-items-start DBSX_LISTVIEW">';
                            $output .= '<div class="col-12 col-md-' . $this->layoutRightExpand . ' pb-5 ' . $this->layoutClass . ' ">';
                            $output .= '<h4>' . '<a href="'.$this->data_url.'?id='.$data['id'].$this->data_params.'" target="'.$this->data_target.'">' . $data[$this->layoutFieldNames["Title"]] . '</a>' . '</h4>';
                            if ($this->layoutFieldNames["Date"] != '' && $data[$this->layoutFieldNames["Date"]]) {
                                $date = date_create($data[$this->layoutFieldNames["Date"]]);
                                $formated_date = date_format($date,"d/m/Y");
                                $output .= $formated_date . '<br>';
                            }
                            
                            $output .= $data[$this->layoutFieldNames["Description"]];
                            $output .= '</div>';
                            $output .= '<div class="col-12 col-md-' . $this->layoutLeftExpand . ' pb-4 ' .  '">';
    
                            $output .= '<center><a href="'.$this->data_url.'?id='.$data['id'].$this->data_params.'" target="'.$this->data_target.'">'.'<img src="'.$MainImageURL.'"' . ' class="img-fluid" ' .' style="max-height:'.$this->image_max_height.' !important; height:'.$this->image_height.'px; ">' . '</a></center>';
    
                            $output .= '</div>';
                            $output .= '</div>';
                        }
                    }
    
    
                    // ***** Blocks Design *****
                    if ($this->layout == 2) {
    
                        $output .= '<div class="col-12 col-md-' . (12/$this->layoutColumns) .' pb-4 ' . $this->layoutClass . '">';
                        $output .= '<center><a href="'.$this->data_url.'?id='.$data['id'].$this->data_params.'" target="'.$this->data_target.'">'.'<img src="'.$MainImageURL.'"' . ' class="img-fluid text-center"  style="max-height:'.$this->image_max_height.' !important; height:'.$this->image_height.'px;">' . '</a></center>';
                        $output .= '<h4 class="">' . '<a href="'.$this->data_url.'?id='.$data['id'].$this->data_params.'" target="'.$this->data_target.'">' . $data[$this->layoutFieldNames["Title"]] . '</a>' . '</h4>';
                        
                        if (@$this->layoutFieldNames["Date"] != '' && @$data[$this->layoutFieldNames["Date"]]) {
                            $date = date_create(@$data[$this->layoutFieldNames["Date"]]);
                            $formated_date = date_format($date,"d/m/Y");
                            $output .= $formated_date . '<br>';
                        }
                        
                        $output .= $data[$this->layoutFieldNames["Description"]];
    
                        $output .= '</div>';
    
                    }
    
                    //restore data url
                    $this->data_url = $this->default_data_url;
                    if ($this->RowIndex >= 2) {
                        $this->RowIndex = 0;
                    }
                }
                $output .= '</div>';
    
                if ($this->use_pagination && $this->TotalItems > $this->ItemsPerPage) {
    
                //Enable | Disable Pagination Nav
                if ($this->cp == 1) {
                    $this->prev_status = 'disabled';
                    $this->fr = 0;
                }
                else {
                    $this->fr = ( $this->cp - 1 ) * $this->ItemsPerPage;
                }
                if ( ($this->cp * $this->ItemsPerPage) >= $this->TotalItems ) {
                    $this->next_status = 'disabled';
                }
    
                $output .= '<nav aria-label="">';
                $output .= '<ul class="pagination justify-content-center">';
                $output .= '<li class="page-item '.$this->prev_status.'">';
                $output .= '<a class="page-link" style="padding:19px;" href="'.$this->prev_url.'" tabindex="-1">Anterior</a>';
                $output .= '</li>';
                $output .= '<li>&nbsp; &nbsp; &nbsp;</li>';
                $output .= '<li class="page-item '.$this->next_status.'">';
                $output .= '<a class="page-link" style="padding:19px;" href="'.$this->next_url.'" >Siguiente</a>';
                $output .= '</li>';
                $output .= '</ul>';
                $output .= '</nav>';
                }
                            $this->DBSX->msg .= $this->DBSX::MSG_SUCCESSFUL_INIT . "LISTVIEW has succesful RENDER " . $sql_query_string . $this->DBSX::MSG_END . "\n<br>";
            }
            else {
                $this->DBSX->msg .= $this->DBSX::MSG_ERROR_INIT . " >>> LISTVIEW Error trying to get query data " . $sql_query_string . $this->DBSX::MSG_END . "\n<br>";
                return false;
            }
            
            return utf8_encode($output);
    
        }
            
    }

?>
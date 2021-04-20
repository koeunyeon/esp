<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/core/ESP.php");

function uri_bind(){
    $url = strtok($_SERVER["REQUEST_URI"], '?');

    $arr_path = explode("/", $url);
    $arr_path = array_filter($arr_path, function($item){ return $item != "";});
    $arr_path = array_values($arr_path); // array key reindex
    
    $path = "";
    $resource = "";
    $act = "";
    if (count($arr_path) == 0){
        $resource = "";
        $act = "index";
        $path = "index";
    }
    elseif(count($arr_path) == 1){
        $resource = $arr_path[0];
        $act = "index";
        $path = $arr_path[0] . "/index";
    }
    elseif(count($arr_path) == 2){    
        $resource = $arr_path[0];
        $act = $arr_path[1];
        $path = implode("/", $arr_path);
    }
    elseif(count($arr_path) > 2){
        $resource = $arr_path[0];
        $act = $arr_path[1];
        $id = $arr_path[2];
        $path = $resource . "/" . $act;        
        $_GET['id'] = $id;        
        $_GET['__esp_uri'] = array_slice($arr_path, 2);
    }
    else{
        ESP::response_404_page_not_found();
    }
    
    $file_path = $_SERVER["DOCUMENT_ROOT"] . "/src/$path.php";
    if (file_exists($file_path)){        
        ESP::resource($resource, $act);
        require($file_path);
        exit();
    }else{             
        ESP::response_404_page_not_found();
    }
}

uri_bind();

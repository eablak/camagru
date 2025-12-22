<?php

namespace App\Controllers;
use App\Database;

class EditingController{

    var $file_path;

    public function __construct(){
        $this->file_path = dirname(__DIR__);
        $this->file_path = str_replace('/App', '/public/assets/html', $this->file_path);
        $this->db = Database::connect();
    }

    public function editing_index(){
        include $this->file_path . '/editing.html';
    }


}


?>
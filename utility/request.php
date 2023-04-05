<?php 

class Request {
    public function __construct() {
        $this->uri = $_REQUEST['uri'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        
        if ($this->method === 'POST') $this->body = $_POST;
        else if ($this->method === 'GET') $this->body = $_GET;
        else $this->body = [];
    }
}

?>
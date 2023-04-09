<?php 

class Response {

    public function status($code) {
        http_response_code($code);
        return $this;
    }
    public function json($data) {
        header('Content-type: application/json; charset=utf-8');
        $json = json_encode($data);
        if (!$json) {
            $json = json_encode(['error' => 'Server error']);
            $this->status(500);
        } 
        echo $json;
        exit;
    }
    
}

?>
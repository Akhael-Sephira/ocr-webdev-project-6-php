<?php

class Router {

    public $routes = [
        'GET'=>[],
        'POST'=>[],
    ];

    /**
     * Find a corresponding route.
     * Put URI params into the $req object under the key uri_params.
     * If failure, send a not found response.
     */
    public function use($req, $res) {

        foreach($this->routes[$req->method] as $route) {

            $split_route = explode("/", $route['uri']);
            $split_req = explode("/", $req->uri);

            if (sizeof($split_route) != sizeof($split_req)) continue;

            $params = [];
            // Compare request & route, step by step
            for($i = 0; $i < sizeof($split_route); $i++) {
                // If param, add to params array and ignore.
                if (str_starts_with($split_route[$i], ":")) {
                    $params[substr($split_route[$i], 1)] = $split_req[$i];
                    continue;
                }
                else if ($split_route[$i] != $split_req[$i]) break; 
                else if ($i == (sizeof($split_route) - 1)) {
                    // Corresponding route found.
                    $req->uri_params = $params;
                    foreach($route['handlers'] as $handler) {
                        $handler($req, $res);
                    };
                    exit();
                }
            }

        };

        include('./notFound.php');

    }

    /** 
     * Add a new POST route
    */
    public function post($route, ...$handlers) {
        
        array_push($this->routes['POST'], [
            'uri'=>$route,
            'handlers'=>$handlers
        ]);
    }

    /** 
     * Add a new GET route
    */
    public function get($route, ...$handlers) {
        
        array_push($this->routes['GET'], [
            'uri'=>$route,
            'handlers'=>$handlers
        ]);
    }
}

?>
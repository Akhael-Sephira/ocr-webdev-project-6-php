<?php

require_once('request.php');
require_once('response.php');

class App {

    /**
     * Test if the URI start by the given route.
     * If true - Invoke all handlers in order with a Request & Response object as arguments
     */
    public function use($route, ...$handlers) {

        $req = new Request();
        $res = new Response();

        if (!empty($req->uri)) {
            $req->uri = "/{$req->uri}";
        } else {
            $req->uri = "/";
        }

        if (str_starts_with($req->uri, $route)) {
            $req->uri = substr($req->uri, strlen($route));
            if (strlen($req->uri) === 0) {
                $req->uri = "/";
            }

            foreach($handlers as $handler) {
                $handler($req, $res);
            };
            exit();
        }
    }
}

?>
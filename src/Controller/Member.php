<?php

namespace Bigfiche\Controller;

class Member {

    public function index() {        
        return json_encode(["tous les"=>"membres"]);
    }
    public function show($id) {        
        return json_encode(["id"=>$id]);
    }

}

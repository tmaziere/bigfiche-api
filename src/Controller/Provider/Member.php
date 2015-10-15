<?php

namespace Bigfiche\Controller\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;

class Member implements ControllerProviderInterface {

    public function connect(Application $app) {
        
        $members = $app["controllers_factory"];

        $members->get("/", "Bigfiche\\Controller\\Member::index");
        $members->get("/{id}", "Bigfiche\\Controller\\Member::show")
                ->assert('id', '\d+');

        /*$members->post("/", "MyApp\\Controller\\UserController::store");

          $members->get("/{id}", "MyApp\\Controller\\UserController::show");

          $members->get("/edit/{id}", "MyApp\\Controller\\UserController::edit");

          $members->put("/{id}", "MyApp\\Controller\\UserController::update");

          $members->delete("/{id}", "MyApp\\Controller\\UserController::destroy"); */

        return $members;
    }

}

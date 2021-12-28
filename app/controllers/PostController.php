<?php
namespace app\controllers;
use libs\DB;
class PostController {
    public function index($currentRoute) {
        $db = new DB();
        $db->table('customer');
        $posts = $db->get();
        var_dump($currentRoute);
    }


}
<?php

use App\Lib\Request;
use App\Lib\Response;
use App\Lib\Router;

use App\Controller\Stock;

Router::get('/stocks', function(Request $req, Response $res){
    (new Stock())->index($req, $res);
});

Router::post('/stocks', function(Request $req, Response $res){
    (new Stock())->store($req, $res);
});
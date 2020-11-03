<?php

namespace App\Controller;

use App\Lib\Db;
use App\Lib\Request;
use App\Lib\Response;

class Stock
{
    /**
     * @param Request $req
     * @param Response $res
     * @return array|string
     */
    public function validation(Request $req, Response $res)
    {
        if (!isset($req->getBody()["product_id"]) || empty($req->getBody()["product_id"]))
            $res->error("Product ID gereklidir.", 400);
        if (!isset($req->getBody()["name"]) || empty($req->getBody()["name"]))
            $res->error("Name gereklidir.", 400);
        if (!isset($req->getBody()["stock"]) || empty($req->getBody()["stock"]))
            $res->error("Stock gereklidir.", 400);
        if (!isset($req->getBody()["created_date"]) || empty($req->getBody()["created_date"]))
            $res->error("Created Date gereklidir.", 400);

        return $req->getBody();
    }

    /**
     * @param Request $req
     * @param Response $res
     */
    public function index(Request $req, Response $res)
    {
        $stocks = (new DB())->select("stocks");
        if(!is_object($stocks) && isset($stocks["error"])){
            $res->error($stocks["message"], 500);
            exit;
        }
        $res->success("Başarılı.", $stocks);
    }

    /**
     * @param Request $req
     * @param Response $res
     */
    public function store(Request $req, Response $res)
    {
        $inputs = $this->validation($req, $res);

        $exists = (new DB())->first("stocks", array_keys($inputs), $inputs["product_id"]);

        if(!is_object($exists) && isset($exists["error"])){
            $res->error($exists["message"], 500);
            exit;
        }

        if ($exists) {
            $res->error("Aynı kayıttan bulunuyor!", 409);
            exit;
        }

        $stocks = (new DB())->create("stocks", $inputs);
        $res->success("Başarılı.", $stocks);
    }
}
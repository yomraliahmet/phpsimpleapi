<?php

namespace App\Lib;

class Response
{
    private $status = 200;

    /**
     * @param int $code
     * @return $this
     */
    public function status(int $code)
    {
        $this->status = $code;
        return $this;
    }

    /**
     * @param array $data
     * @return array
     */
    public function data($data = [])
    {
        if (is_array($data) && isset($data["error"])) {
            $this->status(400)->error($data["message"], 400);
            exit;
        }
        $jsonData = [];
        if (is_array($data)) {
            foreach ($data as $key => $stock) {
                if ($stock->product_id)
                    $jsonData[$key]["product_id"] = str_pad($stock->product_id, 7, '0', STR_PAD_LEFT);
                if ($stock->name)
                    $jsonData[$key]["name"] = (string)$stock->name;
                if ($stock->stock)
                    $jsonData[$key]["stock"] = (int)$stock->stock;
                if ($stock->created_date)
                    $jsonData[$key]["created_date"] = date_format(date_create($stock->created_date), "Y-m-d h:i:s");
            }
        } else {
            if ($data->product_id)
                $jsonData["product_id"] = str_pad($data->product_id, 7, '0', STR_PAD_LEFT);
            if ($data->name)
                $jsonData["name"] = (string)$data->name;
            if ($data->stock)
                $jsonData["stock"] = (int)$data->stock;
            if ($data->created_date)
                $jsonData["created_date"] = date_format(date_create($data->created_date), "Y-m-d h:i:s");
        }


        return $jsonData;
    }

    /**
     * @param string $message
     * @param array $data
     */
    public function success($message = "Başarılı!", $data = [])
    {
        http_response_code($this->status);
        header('Content-Type: application/json');

        $jsonData = [
            "code" => 0,
            "msg" => $message,
            "data" => $this->data($data)
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param $message
     * @param $code
     */
    public function error($message, $code)
    {
        http_response_code($this->status);
        header('Content-Type: application/json');

        $jsonData = [
            "code" => $code,
            "msg" => $message,
        ];

        echo json_encode($jsonData, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
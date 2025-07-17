<?php

namespace App\Controllers;

class TicketsController
{
    public function show($id)
    {
        echo json_encode(["id" => $id]);
    }
}

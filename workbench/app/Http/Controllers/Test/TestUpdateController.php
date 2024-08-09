<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\Request;

class TestUpdateController
{
    public function __invoke(Request $request)
    {
        return view('app');
    }
}
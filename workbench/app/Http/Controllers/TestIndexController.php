<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\Request;

class TestIndexController
{
    public function __invoke(Request $request)
    {
        return view('app');
    }
}
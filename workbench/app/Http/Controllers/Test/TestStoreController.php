<?php

namespace Workbench\App\Http\Controllers;

use Illuminate\Http\Request;

class TestStoreController
{
    public function __invoke(Request $request)
    {
        return view('app');
    }
}

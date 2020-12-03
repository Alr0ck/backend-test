<?php

namespace App\Http\Controllers\V1\FileManager;

use Illuminate\Http\Request;
use App\Http\Controllers\V1\Controller;

use App\Services\File;

class FileController extends Controller
{
    
    public function show($name)
    {
        File::generateLink($name);
    }

}

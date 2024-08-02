<?php

namespace App\Http\Controllers;

 
use App\DataTables\UsersDataTable;
 
class UserController extends Controller
{
    public function index(UsersDataTable $dataTable)
    {
        // dd($dataTable);
        return $dataTable->render('user.index');
    }
}

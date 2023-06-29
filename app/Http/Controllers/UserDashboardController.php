<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::guard('clientes')->user();
        return view('indexUser', ['user' => $user]);
    }
    public function pagamentos()
    {
        $user = Auth::guard('clientes')->user();
        return view('pagamentos', ['user' => $user]);
    }
    public function transferencia()
    {
        $user = Auth::guard('clientes')->user();
        return view('transferencia', ['user' => $user]);
    }
    public function extrato()
    {
        $user = Auth::guard('clientes')->user();
        return view('extrato', ['user' => $user]);
    }
}

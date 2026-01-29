<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->get('search', '');

        $clients = User::searchClients($search)->get();

        $results = $clients->map(function ($client) {
            return [
                'id' => $client->id,
                'full_name' => $client->full_name,
                'email' => $client->email,
            ];
        });

        return response()->json($results);
    }
}

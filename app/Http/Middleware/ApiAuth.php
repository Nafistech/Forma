<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */

     //Abdelrhman - handel the user access token to the routes
    public function handle(Request $request, Closure $next): Response
    {
        $access_token=$request->header("authorization-token");
        if ($access_token !==null) {
            $user=User::where("access_token",$access_token)->first();
        if ($user !==null) {
            return $next($request);

        }else {
            return response()->json([
                "msg"=>"Access token not correct"
            ],403);
        }
        }else {
            return response()->json([
                "msg"=>"Access token not found."
            ],404);
        }

    }

}

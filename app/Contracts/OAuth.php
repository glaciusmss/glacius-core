<?php
/**
 * @author Lam Kai Loon <lkloon123@hotmail.com>
 */

namespace App\Contracts;


use Illuminate\Http\Request;

interface OAuth
{
    public function createAuth();

    public function oAuthCallback(Request $request);

    public function deleteAuth();
}

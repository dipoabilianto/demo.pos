<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetBranchContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            if ($user->branch_id && ! $user->isSuperadmin() && ! $user->isOwner()) {
                session()->put('branch_id', $user->branch_id);
            }

            if (! session()->has('branch_id') && ($user->isSuperadmin() || $user->isOwner())) {
                $firstBranch = Branch::active()->first();
                if ($firstBranch) {
                    session()->put('branch_id', $firstBranch->id);
                }
            }
        }

        return $next($request);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\SearchHits\UserHitResource;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;

class TestController extends Controller
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    public function __invoke(Request $request)
    {
        dump($request->search);
        dd($request->page);

        $pagination = User::search($request->search)
            ->paginate();

        return response()->json([
            'data' => UserHitResource::collection($pagination),
            'current_page' => $pagination->currentPage(),
            'next_page' => $pagination->hasMorePages() ? $pagination->currentPage() + 1 : null,
            'prev_page' => $pagination->onFirstPage() ? null : $pagination->currentPage() - 1,
        ]);
    }
}

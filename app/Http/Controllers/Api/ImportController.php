<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BukuKasImportRequest;
use App\Imports\BukuKasImport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ImportController extends Controller
{
    public function bukuKas(BukuKasImportRequest $request): JsonResponse
    {
        $company = Auth::user()->companies()->first();

        try {
            DB::beginTransaction();

            Excel::import(new BukuKasImport($company, $request->name), $request->file('file'));

            DB::commit();

            return $this->createdResponse(null);
        } catch (Throwable $t) {
            DB::rollBack();

            return response()->json([
                'message' => $t->getLine(),
            ], 400);
        }
    }
}

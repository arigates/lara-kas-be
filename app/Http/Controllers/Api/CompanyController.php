<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CompanyStoreRequest;
use App\Http\Requests\Api\CompanyUpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $companies = Auth::user()->companies()->get();

        return $this->showedResponse(CompanyResource::collection($companies));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CompanyStoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $company = Auth::user()->companies()->create($data);

        return $this->createdResponse(new CompanyResource($company));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $company = Auth::user()->companies()->find($id);
        if (! $company) {
            return $this->notFoundResponse();
        }

        return $this->showedResponse(new CompanyResource($company));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyUpdateRequest $request, string $id): JsonResponse
    {
        $company = Auth::user()->companies()->find($id);
        if (! $company) {
            return $this->notFoundResponse();
        }

        $columns = ['name', 'phone', 'email', 'address'];

        foreach ($columns as $column) {
            if ($request->filled($column)) {
                $company->{$column} = $request->{$column};
            }
        }

        $company->save();

        return $this->updatedResponse(new CompanyResource($company->fresh()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $company = Auth::user()->companies()->find($id);
        if (! $company) {
            return $this->notFoundResponse();
        }

        $company->delete();

        return $this->deletedResponse();
    }

    public function totalArAp(string $id): JsonResponse
    {
        $company = Auth::user()->companies()->find($id);
        if (! $company) {
            return $this->notFoundResponse();
        }

        $totalArAp = Customer::selectRaw('SUM(CASE WHEN ar_ap_balance >= 0 THEN ar_ap_balance ELSE 0 END) as ap_total, SUM(CASE WHEN ar_ap_balance < 0 THEN ar_ap_balance ELSE 0 END) as ar_total')
            ->where('company_id', '=', $company->id)
            ->get();

        $result = count($totalArAp) > 0 ? $totalArAp[0] : [];

        return $this->showedResponse($result);
    }
}

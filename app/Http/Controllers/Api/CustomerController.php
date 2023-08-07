<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CustomerStoreRequest;
use App\Http\Requests\Api\CustomerUpdateRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $customers = Customer::query();

        if ($request->has('company_id')) {
            $companyId = $request->get('company_id');
            $company = Auth::user()->companies()->find($companyId);
            if (! $company) {
                return $this->notFoundResponse();
            }
        } else {
            $company = Auth::user()->companies()->first();
        }

        $customers = $customers->where('company_id', '=', $company->id);
        $customers = $customers->orderBy('updated_at', 'desc')->get();

        return CustomerResource::collection($customers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CustomerStoreRequest $request): JsonResponse
    {
        $companyId = $request->company_id;
        $company = Auth::user()->companies()->find($companyId);
        if (! $company) {
            return $this->notFoundResponse();
        }

        $data = $request->validated();
        $customer = $company->customers()->create($data);

        return $this->createdResponse(new CustomerResource($customer));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $customer = Customer::with('ArAps')->find($id);
        if (! $customer) {
            return $this->notFoundResponse();
        }

        $company = Auth::user()->companies()->find($customer->company_id);
        if (! $company) {
            return $this->notFoundResponse();
        }

        return $this->showedResponse(new CustomerResource($customer));
    }

    /**
     * Display the specified resource.
     */
    public function showPublic(string $id): JsonResponse
    {
        $customer = Customer::with('ArAps')->find($id);
        if (! $customer) {
            return $this->notFoundResponse();
        }

        return $this->showedResponse(new CustomerResource($customer));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerUpdateRequest $request, string $id): JsonResponse
    {
        $customer = Customer::find($id);
        if (! $customer) {
            return $this->notFoundResponse();
        }

        $company = Auth::user()->companies()->find($customer->company_id);
        if (! $company) {
            return $this->notFoundResponse();
        }

        $columns = ['name', 'phone', 'email', 'address'];

        foreach ($columns as $column) {
            if ($request->filled($column)) {
                $customer->{$column} = $request->{$column};
            }
        }

        $customer->save();

        return $this->updatedResponse(new CustomerResource($customer));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $customer = Customer::find($id);
        if (! $customer) {
            return $this->notFoundResponse();
        }

        $company = Auth::user()->companies()->find($customer->company_id);
        if (! $company) {
            return $this->notFoundResponse();
        }

        $customer->delete();

        return $this->deletedResponse();
    }
}

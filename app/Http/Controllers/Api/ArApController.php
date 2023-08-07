<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ArApStoreRequest;
use App\Http\Requests\Api\ArApUpdateRequest;
use App\Http\Resources\ArApResource;
use App\Models\ArAp;
use App\Models\Customer;
use App\Traits\CalculateCustomerArApBalance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class ArApController extends Controller
{
    use CalculateCustomerArApBalance;

    /**
     * Display a listing of the resource.
     *
     * @TODO validate request
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $ArAps = ArAp::query();

        if ($request->has('customer_id')) {
            $customerId = $request->customer_id;
            $ArAps = $ArAps->where('customer_id', '=', $customerId);
        }

        $ArAps = $ArAps->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();

        return ArApResource::collection($ArAps);
    }

    public function indexPublic(Request $request): AnonymousResourceCollection
    {
        if (! $request->has('customer_id')) {
            abort(404);
        }

        $customerId = $request->customer_id;
        $ArAps = ArAp::where('customer_id', '=', $customerId)->orderBy('date', 'desc')->orderBy('created_at', 'desc')->get();

        return ArApResource::collection($ArAps);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArApStoreRequest $request): JsonResponse
    {
        $customer = Customer::find($request->customer_id);
        if (! $customer) {
            return $this->notFoundResponse();
        }

        $company = Auth::user()->companies()->find($customer->company_id);
        if (! $company) {
            return $this->notFoundResponse();
        }

        $data['date'] = $request->date;
        $data['description'] = $request->description;

        if ($request->type === ArAp::TYPE_AP) {
            $data['ap'] = $request->nominal;
        } else {
            $data['ar'] = $request->nominal;
        }

        try {
            DB::beginTransaction();

            $ArAp = $customer->ArAps()->create($data);

            $this->calculateCustomerArApBalance($customer->id);

            DB::commit();

            return $this->createdResponse(new ArApResource($ArAp));
        } catch (Throwable $t) {
            DB::rollBack();

            return response()->json([
                'message' => $t->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $ArAp = ArAp::find($id);
        if (! $ArAp) {
            return $this->notFoundResponse();
        }

        $customer = Customer::find($ArAp->customer_id);
        if (! $customer) {
            return $this->notFoundResponse();
        }

        $company = Auth::user()->companies()->find($customer->company_id);
        if (! $company) {
            return $this->notFoundResponse();
        }

        return $this->showedResponse(new ArApResource($ArAp));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArApUpdateRequest $request, string $id)
    {
        $ArAp = ArAp::find($id);
        if (! $ArAp) {
            return $this->notFoundResponse();
        }

        $customer = Customer::find($ArAp->customer_id);
        if (! $customer) {
            return $this->notFoundResponse();
        }

        $company = Auth::user()->companies()->find($customer->company_id);
        if (! $company) {
            return $this->notFoundResponse();
        }

        if ($request->filled('nominal')) {
            if ($ArAp->type === ArAp::TYPE_AP) {
                $ArAp->ap = $request->nominal;
            } else {
                $ArAp->ar = $request->nominal;
            }
        }

        if ($request->filled('date')) {
            $ArAp->date = $request->date;
        }

        if ($request->filled('description')) {
            $ArAp->description = $request->description;
        }

        try {
            DB::beginTransaction();

            $ArAp->save();

            $this->calculateCustomerArApBalance($customer->id);

            DB::commit();
        } catch (Throwable $t) {
            DB::rollBack();

            return response()->json([
                'message' => $t->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $ArAp = ArAp::find($id);
        if (! $ArAp) {
            return $this->notFoundResponse();
        }

        $customer = Customer::find($ArAp->customer_id);
        if (! $customer) {
            return $this->notFoundResponse();
        }

        $company = Auth::user()->companies()->find($customer->company_id);
        if (! $company) {
            return $this->notFoundResponse();
        }

        try {
            DB::beginTransaction();

            $ArAp->delete();

            $this->calculateCustomerArApBalance($customer->id);

            DB::commit();

            return $this->deletedResponse();
        } catch (Throwable $t) {
            DB::rollBack();

            return response()->json([
                'message' => $t->getMessage(),
            ], 400);
        }
    }
}

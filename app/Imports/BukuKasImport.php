<?php

namespace App\Imports;

use App\Models\ArAp;
use App\Models\Company;
use App\Traits\CalculateCustomerArApBalance;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BukuKasImport implements ToCollection, WithHeadingRow
{
    use CalculateCustomerArApBalance;

    protected string $name;

    protected Company $company;

    public function __construct(Company $company, string $name)
    {
        $this->company = $company;
        $this->name = $name;
    }

    public function collection(Collection $collection)
    {
        $customer = $this->company->customers()->create([
            'name' => $this->name,
        ]);

        //        dd($collection);
        foreach ($collection as $row) {

            $data['date'] = $row['tanggal'];
            $data['description'] = $row['deskripsi'];
            $ar = intval($row['memberi']);
            $ap = intval($row['menerima']);
            $data['ar'] = $ar;
            $data['ap'] = $ap;

            if ($ar > 0) {
                $data['type'] = ArAp::TYPE_AR;
            }

            if ($ap > 0) {
                $data['type'] = ArAp::TYPE_AP;
            }

            $customer->ArAps()->create($data);
        }

        $this->calculateCustomerArApBalance($customer->id);
    }

    public function headingRow(): int
    {
        return 11;
    }
}

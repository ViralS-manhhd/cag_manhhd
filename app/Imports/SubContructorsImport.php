<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Tenant;
use App\Models\SubConstructor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SubContructorsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        try {
            $tenant_id = Tenant::where('uen', $row['tenant_uen'])->first()->id;
            return new SubConstructor([
                'name' => $row['name'],
                'uen' => $row['uen'],
                'tenancy_start_date' => Carbon::createFromFormat(DATE_FORMAT, $row['tenancy_start_date']),
                'tenancy_end_date' => Carbon::createFromFormat(DATE_FORMAT, $row['tenancy_end_date']),
                'tenant_id' => $tenant_id,
            ]);
        } catch (\Exception $ex) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'uen' => 'required|unique:tenants,uen|unique:sub_constructors,uen',
            'tenancy_start_date' => 'required',
            'tenancy_end_date' => 'required',
        ];
    }

    public function onError(\Throwable $e)
    {
        // Handle the exception how you'd like.
    }

    public function onFailure(Failure ...$failures)
    {
        // Handle the failures how you'd like.
    }
}

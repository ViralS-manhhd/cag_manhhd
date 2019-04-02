<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Country;
use App\Models\PassHolder;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class TenantPassHoldersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsFailures;

    public $error = [];
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        try {
            $country_id = Country::where('name', $row['nationality'])->first()->id;
            $uen = backpack_user()->tenant ? backpack_user()->tenant->uen : backpack_user()->subConstructor->uen;
            $company_uen = $uen;
            $zones = explode(',', $row['zone']);
            session()->put(SESS_ZONES, $zones);

            return new PassHolder([
                'applicant_name' => $row['applicant_name'],
                'nric' => $row['pass_number'],
                'pass_expiry_date' => Carbon::createFromFormat(DATE_FORMAT, $row['passexpirydate']),
                'country_id' => $country_id,
                'company_uen' => $company_uen,
                'ru_name' => $row['ru_name'],
                'ru_email' => $row['ru_email'],
                'as_name' => $row['as_name'],
                'as_email' => $row['as_email']
            ]);
        } catch (\Exception $ex) {
            if (is_null(Country::where('name', $row['nationality'])->first())) {
                $this->error[] = 'Country <b>' . @$row['nationality'] . '</b> not found';
            }
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'applicant_name' => 'required',
            'pass_number' => 'required',
            'passexpirydate' => 'required',
            'nationality' => 'required',
            'ru_name' => 'required',
            'ru_email' => 'required',
            'as_name' => 'required',
            'as_email' => 'required',
        ];
    }
//
//    public function sheets(): array
//    {
//        return [
//            // Select by sheet index
//            0 => new TenantPassHoldersImport(),
//        ];
//    }

    public function onError(\Throwable $e)
    {
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PassHolder;
use App\Events\PassHolderExpireSoon;
use Carbon\Carbon;

class PassHolderExpireChecking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cag:pass_holder:checking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking pass holder expiry';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $passHolderExprires =  $this->getPassHolderExpireSoon();
        foreach ($passHolderExprires as $pass) {
            event(new PassHolderExpireSoon($pass));
        }
    }

    public function getPassHolderExpireSoon()
    {
        return PassHolder::orWhere(function ($query) {
                        $query->where('pass_expiry_date', '<=', Carbon::now()->addWeeks(4))
                        ->where('pass_expiry_date', '>', Carbon::now()->addWeeks(4)->subDay());
                    })
                    ->orWhere(function ($query) {
                        $query->where('pass_expiry_date', '<=', Carbon::now()->addWeeks(3))
                        ->where('pass_expiry_date', '>', Carbon::now()->addWeeks(3)->subDay());
                    })
                    ->orWhere(function ($query) {
                        $query->where('pass_expiry_date', '<=', Carbon::now()->addWeeks(2))
                        ->where('pass_expiry_date', '>', Carbon::now()->addWeeks(2)->subDay());
                    })
                    ->orWhere(function ($query) {
                        $query->where('pass_expiry_date', '<=', Carbon::now()->addWeeks(1))
                        ->where('pass_expiry_date', '>', Carbon::now()->addWeeks(1)->subDay());
                    })
                    ->get();
    }
}

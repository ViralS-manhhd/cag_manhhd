<?php

namespace App\Http\Controllers\Admin;

use App\Models\PassHolder;
use App\Models\Tenant;
use Backpack\Base\app\Http\Controllers\Controller;
use Carbon\Carbon;

class AdminController extends Controller
{
    protected $data = []; // the information we send to the view

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['2fa', backpack_middleware()]);
    }

    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $this->data['title'] = trans('backpack::base.dashboard'); // set the page title
        if(backpack_user()->hasAnyRole(config('backpack.cag.roles'))) {
            $pass_holders = PassHolder::all();
        } else {
            $uen = backpack_user()->tenant ? backpack_user()->tenant->uen : backpack_user()->subConstructor->uen;
            $pass_holders = PassHolder::where('company_uen', $uen)->get();
        }
        $this->data['pass_holders'] = $pass_holders;
        $this->data['pass_holders_active'] = $pass_holders->where('status', PASS_STATUS_VALID);
        $this->data['pass_holders_expireIn4Weeks'] = $pass_holders->where('pass_expiry_date','<=', Carbon::now()->addWeeks(4))->where('pass_expiry_date','>', Carbon::now());
        $this->data['pass_pending_return'] = $pass_holders->whereIn('status', [PASS_STATUS_BLACKLISTED, PASS_STATUS_WAITING_CONFIRM_RETURN]);
        return view('dashboard.dashboard', $this->data);
    }

    /**
     * Redirect to the dashboard.
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        // The '/admin' route is not to be used as a page, because it breaks the menu's active state.
        return redirect(backpack_url('dashboard'));
    }
}

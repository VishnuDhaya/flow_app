<?php

namespace App\Http\Controllers\InvApp;
use App\Services\InvApp\InvAppService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class InvAppController extends Controller{

    /**
     * @var InvAppService
     */
    private $serv;

    public function __construct(){
        $this->serv = new InvAppService;
    }

    public function home_view(Request $request){

        $data = $this->serv->get_home_data();
        return view('investorsite.home',$data);
    }

//    public function assets_view()
//    {
//        $data = $this->serv->get_assets_data();
//        return view('investorsite.myasset',$data);
//    }

    public function transactions_view($fund_code = null)
    {
        $data = $this->serv->get_transactions($fund_code);
        return view('investorsite.transactions',$data);
    }

    public function bond_details_view($fund_code)
    {
        $data = $this->serv->get_bond_details($fund_code);
        return view('investorsite.bond_details',$data);
    }

    public function bank_acc()
    {
        $data = $this->serv->get_bank_acc();
        return view('investorsite.bank_account',$data);
    }

    public function add_bank_acc(Request $data)
    {
        $data = $data->all();
        unset($data['_token']);
        $data = $this->serv->add_bank_acc($data);
        return redirect(route('view_acc'));
    }

}
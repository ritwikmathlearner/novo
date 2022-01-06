<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KolSession;
use App\Models\Qrimage;
use App\Models\User;
use DB;
use Carbon\Carbon;
use DateTime;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SessionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $keyword = $request->get('search');
        $perPage = 15;

        if (!empty($keyword)) {
            $session = KolSession::where('session_name', 'LIKE', "%$keyword%")->orWhere('unique_code', 'LIKE', "%$keyword%")
                ->latest()->paginate($perPage);
        } else {
            $session = KolSession::latest()->paginate($perPage);
        }

        return view('admin.session.index', compact('session'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $users = User::select('id', DB::raw('CONCAT ( name , " [" , email , "]") AS name'))
                ->where('id', '>', "5")
                ->get();
        $users = $users->pluck('name', 'id');
        
        
        return view('admin.session.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate(
            $request,
            [
                'user_id' => 'required',
                'session_name' => 'required',
                'start_date_time' => 'required',
            ]
        );

        $data = $request->all();
        $data['start_date_time'] = Carbon::createFromDate($data['start_date_time']);
        $data['created_at'] = Carbon::createFromDate(now());
        $data['unique_code'] = $unique_code = $this->generateUniqueCode();
        
        
        $session = KolSession::create($data);
        
        
        //        Qrcodes
        $QrcodesData = [];
        
        $qr_code_image_name = "$unique_code.svg";
        $QrcodesData['qr_code_image'] = $qr_code_image_name;
        
        $joining_url = url('api/attendee-login/'. $unique_code);
        $QrcodesData['joining_url'] = $joining_url;

        $QrcodesData['created_at'] = Carbon::createFromDate(now());
        $QrcodesData['kol_session_id'] = $session->id;
        
        QrCode::size(256)->generate($joining_url, public_path('qrimages')."/$qr_code_image_name");
        
        $Qrimgedata = Qrimage::create($QrcodesData);
        
//                dd($QrcodesData);



        return redirect('admin/session')->with('flash_message', 'Session added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
//        DB::enableQueryLog();
        //
        $session = KolSession::findOrFail($id);
//        $qrimage = Qrimage::where('kol_session_id', '=', $session->id)->find(1);
        $qrimage = $session->qrimage()->first();
        
        $kol_user = User::findOrFail($session->user_id);
        
        
        return view('admin.session.show', compact('session', 'qrimage', 'kol_user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function generateUniqueCode()
    {
        do {
            $code = random_int(100000, 999999);
        } while (KolSession::where("unique_code", "=", $code)->first());
  
        return $code;
    }
}

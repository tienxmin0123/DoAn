<?php

namespace App\Http\Controllers;

use App\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\LoaiNguoiDung;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class CustomerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   if($request->search){
         
            $user['listUser'] = DB::table('nguoi_dungs')
            ->select('TenNguoidung','nguoi_dungs.created_at','loai_nguoi_dungs.TenLoai','SDT','DiaChi','Email','username','nguoi_dungs.id','nguoi_dungs.TrangThai','nguoi_dungs.active')
            ->join('loai_nguoi_dungs','nguoi_dungs.loai_nguoi_dungs_id','=','loai_nguoi_dungs.id')
            ->where('nguoi_dungs.SDT','LIKE',"%$request->search%")
            ->orWhere('nguoi_dungs.TenNguoidung','LIKE',"%$request->search%")
            ->orWhere('nguoi_dungs.Email','LIKE',"%$request->search%")
            ->orWhere('nguoi_dungs.username','LIKE',"%$request->search%")
            ->paginate(5);
 
        }else 
        $user['listUser'] = DB::table('nguoi_dungs')
        ->select('TenNguoidung','nguoi_dungs.created_at','loai_nguoi_dungs.TenLoai','SDT','DiaChi','Email','username','nguoi_dungs.id','nguoi_dungs.TrangThai','nguoi_dungs.active')
        ->join('loai_nguoi_dungs','nguoi_dungs.loai_nguoi_dungs_id','=','loai_nguoi_dungs.id')
        ->paginate(5);
        return view('pages.quan-ly-nguoi-dung', $user);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $typeUser=DB::table('loai_nguoi_dungs')->get();

        // return $typeUser;
        return view('pages.them.them-nguoi-dung', compact('typeUser'));
    }
    public function store(Request $request)
    {

        $rule=[
            "email"=>"required|email|unique:nguoi_dungs",
            "username"=>"required|unique:nguoi_dungs|min:5",
            "sdt"=>"required|unique:nguoi_dungs|numeric",
            "password"=>"min:5",
            "password_verified"=>"same:password"
        ];
        $customMessage=[
            "password.min"=>"M???t kh???u kh??ng ???????c b?? h??n 5 k?? t???",
            "password_verified.same"=>"M???t kh???u x??c nh???n kh??ng ch??nh x??c .",
            "sdt.unique"=>"S??? ??i???n tho???i ".$request->sdt." ???? c?? ng?????i s??? d???ng!",
            "sdt.numeric"=>"S??? ??i???n tho???i ".$request->sdt." kh??ng ????ng ?????nh d???ng!",
            "email.email"=>"Email ".$request->email." kh??ng ????ng ?????nh d???ng!",
            "email.unique"=>"Email ".$request->email." ???? c?? ng?????i s??? d???ng!",
            "username.unique"=>"T??n t??i kho???n ".$request->username." ???? c?? ng?????i s??? d???ng!",
            "username.min" =>"T??n t??i kho???n ph???i l???n h??n 5 k?? t??? !",
        ];
        $validator=Validator::make($request->all(),$rule,$customMessage);
        if($validator->fails())
        {
            return redirect('/quan-ly-nguoi-dung/them-nguoi-dung')->withErrors($validator);
        }

           $user = new NguoiDung;
           $user->Email = $request->email;
           $user->TenNguoiDung = $request->name;
           $user->SDT = $request->sdt;
           $user->DiaChi = $request->dia_chi;
           $user->GioiTinh = $request->sex;
           $user->username = $request->username;
           $user->password = Hash::make($request->password);
           $user->loai_nguoi_dungs_id = $request->loai_nguoi_dungs_id;;
           $user->save();
           return redirect('/quan-ly-nguoi-dung');

    }
    public function CheckUser(Request $request)
    {
        $email=DB::table('nguoi_dungs')->where('Email','=',$request->email)->get();
        $sdt=DB::table('nguoi_dungs')->where('SDT','=',$request->sdt)->get();
        $username=DB::table('nguoi_dungs')->where('username','=',$request->username)->get();
        dd($email, $sdt, $username);
    }


    public function show(Request $request, $id)
    {
        $admin =Auth::user();
      

        $user =NguoiDung::where('nguoi_dungs.id','=',$id)
        ->join('loai_nguoi_dungs','nguoi_dungs.loai_nguoi_dungs_id','=','loai_nguoi_dungs.id')
        ->select('loai_nguoi_dungs.TenLoai','nguoi_dungs.id','nguoi_dungs.TenNguoidung','nguoi_dungs.DiaChi','nguoi_dungs.SDT','nguoi_dungs.Email','nguoi_dungs.TrangThai','nguoi_dungs.username','nguoi_dungs.GioiTinh','nguoi_dungs.created_at')
        ->first();

        if($admin->id==$user->id){
            return redirect('/quan-ly-nguoi-dung/my-profile');
        }

        $orders['orders'] = DB::select('SELECT nguoi_dungs.TenNguoidung,nguoi_dungs.SDT,don_hangs.id,don_hangs.ThoiGianMua,don_hangs.Tongtien FROM `don_hangs`
        INNER JOIN `nguoi_dungs` ON `don_hangs`.nguoi_dungs_id=`nguoi_dungs`.id WHERE don_hangs.nguoi_dungs_id='.$id);
        $amountItemsPage = 10;
        $totalPages = FLOOR(sizeof($orders['orders']) / $amountItemsPage);
        if (sizeof($orders['orders']) % $amountItemsPage > 0) {
            $totalPages++;
        }
        $currentPage = 1;
        $html = '<ul class="pagination"><li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>';
        if (is_numeric($request->page)) {
            $currentPage = $request->page;
            if($currentPage>1) $html = '<ul class="pagination"><li class="page-item">
            <a class="page-link" href="/quan-ly-nguoi-dung/show/'.$id.'?page=' . ($currentPage - 1) . '">Previous</a></li>';
        };

        $orders = array_slice($orders['orders'], ($currentPage - 1) * $amountItemsPage, $amountItemsPage);
        for ($i = 1; $i <= $totalPages; $i++) {
            $disabled='';
            if($i==$currentPage)$disabled='disabled';
            $html .= '<li class="page-item '.$disabled.'"><a class="page-link" href="/quan-ly-nguoi-dung/show/'.$id.'?page=' . $i . '">' . $i . '</a></li>';
        }

        if ($currentPage == $totalPages) {
            $html .= '  <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>';
        } else {
            $html .= ' <li class="page-item"><a class="page-link" href="/quan-ly-nguoi-dung/show/'.$id.'?page=' . ($currentPage + 1) . '">Next</a></li>';
         }

        //  dd($user);
        return view('pages.cap-nhat.cap-nhat-nguoi-dung',compact('user','orders','html'));
    }
    public function MyProfile(Request $request)
    {  

        $admin =Auth::user();
        $user =NguoiDung::where('nguoi_dungs.id','=',$admin->id)
        ->join('loai_nguoi_dungs','nguoi_dungs.loai_nguoi_dungs_id','=','loai_nguoi_dungs.id')
        ->select('loai_nguoi_dungs.TenLoai','nguoi_dungs.id','nguoi_dungs.TenNguoidung','nguoi_dungs.DiaChi','nguoi_dungs.SDT','nguoi_dungs.Email','nguoi_dungs.TrangThai','nguoi_dungs.username','nguoi_dungs.GioiTinh','nguoi_dungs.created_at')
        ->first();

        return view('pages.cap-nhat.my-profile',compact('user'));
    }

    public function edit(Request $request,$id)
    {
        //
        $rule = [
            "DiaChi" => "required",
            "SDT" => "required",
        ];
        $customMessage = [
           "DiaChi.required" => "?????a ch??? kh??ng ???????c b??? tr???ng",
           "SDT.required" => "S??? ??i???n tho???i kh??ng ???????c b??? tr???ng"

        ];
        $validator = Validator::make($request->all(), $rule, $customMessage);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $data=NguoiDung::find($id);
        $data->TenNguoidung=$request->name;
        $data->DiaChi = $request->DiaChi;
        
        $data->SDT= $request->SDT;
        $data->save();
        return response()->json(["message"=>"C???p nh??t ng?????i d??ng th??nh c??ng","user"=>$data],200);
    }

   
    public function editPassword(Request $request,$id)
    {
        $data=NguoiDung::find($id);
        
     if (!(Hash::check($request->get('oldPassword'), $data->password))) {
            // The passwords matches
            return response()->json(["message" => "M???t kh???u c?? kh??ng ????ng"], 500);
        }else
        {
            $data->password=bcrypt($request->password);
            $data->save();
            return response()->json(["message"=>"Thay ?????i m???t kh???u th??nh c??ng"]);
        }
    }

    public static function getProductsByUser( $id)
    {
        $products = DB::select('SELECT SUM(SoLuong) AS amount FROM chi_tiet_don_hangs WHERE `don_hangs_id` IN (SELECT id FROM don_hangs WHERE nguoi_dungs_id= '.$id.')');
       $amounts =$products[0];
        $amount=(int)$amounts->amount;
        return $amount;
    }


        // }
        
    public function update(Request $request,  $id)
    {


        // $rule=[
        //     "email"=>"required|email|unique:nguoi_dungs",

        //     "sdt"=>"required|unique:nguoi_dungs|numeric",
        //     "password"=>"min:5",
        //     "password_verified"=>"same:password"
        // ];
        // $customMessage=[
        //     "password.min"=>"M???t kh???u kh??ng ???????c b?? h??n 5 k?? t???",
        //     "password_verified.same"=>"M???t kh???u x??c nh???n kh??ng ch??nh x??c .",
        //     "sdt.unique"=>"S??? ??i???n tho???i ".$request->sdt." ???? c?? ng?????i s??? d???ng!",
        //     "sdt.numeric"=>"S??? ??i???n tho???i ".$request->sdt." kh??ng ????ng ?????nh d???ng!",
        //     "email.email"=>"Email ".$request->email." kh??ng ????ng ?????nh d???ng!",
        //     "email.unique"=>"Email ".$request->email." ???? c?? ng?????i s??? d???ng!",
        // ];
        // $validator=Validator::make($request->all(),$rule,$customMessage);
        // if($validator->fails())
        // {
        //     return redirect('/quan-ly-nguoi-dung/show/'.$id)->withErrors($validator);
        // }


        $admin =Auth::user();
        if($request->password){
            $rule=[
            "password"=>"required|min:5",
        ];
        $customMessage=[
            "password.min"=>"M???t kh???u kh??ng ???????c b?? h??n 5 k?? t???",
            "password.required"=>"M???t kh???u kh??ng ???????c b??? tr???ng",
        ];
        $validator=Validator::make($request->all(),$rule,$customMessage);
        if($validator->fails())
        {
            if($admin->id==$id) return redirect('/quan-ly-nguoi-dung/my-profile')->withErrors($validator);
            return redirect('/quan-ly-nguoi-dung/show/'.$id)->withErrors($validator);
        }
        }
         
           $user = NguoiDung::find($id);
        //    $user->Email = $request->email;
           $user->TenNguoiDung = $request->name;
           if(!$request->password) $user->password = $user->password;
           else 
           $user->password =  Hash::make($request->password);
           $user->DiaChi = $request->dia_chi;
           $user    ->TrangThai = $request->TrangThai;
           $user->GioiTinh = $request->sex;
           $user->save();
           if($admin->id==$id) return redirect('/quan-ly-nguoi-dung/my-profile');
           return redirect('/quan-ly-nguoi-dung/show/'.$id);
    }
    public function destroy(NguoiDung $nguoiDung)
    {
        //
    }
}

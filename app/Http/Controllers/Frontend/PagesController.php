<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\Tournament;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PagesController extends Controller
{
    public function get_country_states(Request $request,$country_id){
        $res=array();
        $res['status']=0;
        if(!empty($country_id) && intval($country_id) > 0){
            $states=DB::table('states')->where('country_id', $country_id)->get();
            $res['states']=$states;
            exit(json_encode($states));
        }
        else{
            $res['msg']="Invalid country";
        }
        exit(json_encode($res));
    }
    public function home(){
        $page = DB::table('home_cms')->first();
        return response()->json([
            'data' => $page,
            'categories'=>Category::where('status','active')->orderBy('created_at', 'asc')->get(),
            'tournaments' => Tournament::with(['images', 'tournamentCategories','category'])
                            ->where('is_active', 1)
                            ->where('is_featured', 1)
                            ->orderBy('id', 'desc')
                            ->latest()
                            ->take(5)
                            ->get(),
            'trending_tournaments' => Tournament::with(['images', 'tournamentCategories','category','teams'])
                            ->where('is_active', 1)
                            ->orderBy('created_at', 'asc')
                            ->latest()
                            ->take(5)
                            ->get(),
            'contact_us' => DB::table('contact_us_cms')->where('id', 1)->first()
            // 'trending_tournaments' => DB::table('tournaments')->where('is_active', '1')->orderBy('created_at', 'desc')->take(5)->get(),
    
        ], 200);
    }
    public function tournament_matches(){
        
    }
    public function privacyPolicy()
    {
        $page = Page::where('slug', 'privacy-policy')->first();
        return response()->json(['data' => $page], 200);
    }
    public function termsAndConditions()
    {
        $page = Page::where('slug', 'terms-and-conditions')->first();
        return response()->json(['data' => $page], 200);
    }
    public function disclaimer(){
        $page = Page::where('slug', 'disclaimer')->first();
        return response()->json(['data' => $page], 200);
    }
    public function aboutUs(){
        $page = DB::table('about_us')->first();
        // dd($page);
        return response()->json(['data' => $page], 200);
    }
    public function contactUs(){
        $page = DB::table('contact_us_cms')->first();
        return response()->json(['data' => $page], 200);
    }
    public function saveContactUs(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile_no' => 'required',
            'email' => 'required',
            'looking_for' => 'required',
            'message' => 'required',    
        ]);
        if($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $data = $request->all();
        DB::table('contact_us_queries')->insert($data);
        return response()->json(['message' => 'Contact us saved successfully'], 200);
    }

    public function faq(){
        $page = DB::table('faq')->get();
        return response()->json(['data' => $page], 200);
    }

}

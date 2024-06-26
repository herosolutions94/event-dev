<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Components\Services\OrganizationService;
use App\Models\TournamentLevel;
use App\Models\Affiliation;
use App\Http\Requests\StoreTournamentRequest;
use Illuminate\Support\Facades\DB;


class SettingsController extends Controller
{
    public function create()
    {

        $settings = DB::table('site_settings')->get();
        $social_yelp = $settings->where('key', 'social_yelp')->first();
        $social_google_store = $settings->where('key', 'social_google_store')->first();
        $social_linkedin = $settings->where('key', 'social_linkedin')->first();
        $social_instagram = $settings->where('key', 'social_instagram')->first();
        $social_facebook = $settings->where('key', 'social_facebook')->first();
        $tournament_fee = $settings->where('key', 'tournament_fee')->first();
        $logo = $settings->where('key', 'logo')->first();


        return view('admin.settings.create',
            [
                'settings' => $settings,
                'social_yelp' => $social_yelp,
                'social_google_store' => $social_google_store,
                'social_linkedin' => $social_linkedin,
                'social_instagram' => $social_instagram,
                'social_facebook' => $social_facebook,
                'tournament_fee' => $tournament_fee,
                'logo' => $logo,
            ]);
    }

    public function store(Request $request)
    {
        DB::select('delete from site_settings');

        $insertArray = [];
        $insertArray[] = [
            'key' => 'social_yelp',
            'value' => $request->social_yelp,
        ];
        $insertArray[] = [
            'key' => 'social_google_store',
            'value' => $request->social_google_store,
        ];
        $insertArray[] = [
            'key' => 'social_linkedin',
            'value' => $request->social_linkedin,
        ];
        $insertArray[] = [
            'key' => 'social_instagram',
            'value' => $request->social_instagram,
        ];
        $insertArray[] = [
            'key' => 'social_facebook',
            'value' => $request->social_facebook,
        ];
        $insertArray[] = [
            'key' => 'tournament_fee',
            'value' => $request->tournament_fee,
        ];
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo')->store('uploads', 'public');
            $insertArray[] = [
                'key' => 'logo',
                'value' => $logo,
            ];
        }
        DB::table('site_settings')->insert($insertArray);

        return redirect()->route('admin.settings.create')->with('flash_message_success','Setting added successfully');
    }
    public function change_password(Request $request){
        $admin=Admin::find(1);
        $input = $request->all();
        if($input){
            $this->validate($request, [
                'current_password'     => 'required',
                'new_password'     => 'required',
                'confirm_password' => 'required|same:new_password',
            ]);
            if(Hash::check($input['current_password'],$admin->site_password)){
                $admin->site_password=Hash::make($input['new_password']);
                $admin->save();
                return redirect('admin/change-password')
                ->with('success','Updated Successfully');
            }
            else{
                return redirect('admin/change-password')
                ->with('error','Current Password is not right!');
            }
        }
        return view('admin.auth.change_password',$this->data);
    }
    
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Components\Services\OrganizationService;
use App\Models\Tournament;
use App\Models\TournamentType;
use App\Models\EventType;
use App\Models\TournamentLevel;
use App\Http\Requests\StoreTournamentRequest;
use Illuminate\Support\Facades\DB;


class TournamentController extends Controller
{
    public function index()
    {
        $tournament = Tournament::with('category')->latest()->paginate(10);
        return view('admin.tournaments.index',
            [
                'data' => $tournament,
            ]);
    }
    public function show($id)
    {
        $tournament = Tournament::with(
            'images', 
            'tournamentCategories',
            'category',
            'teams.teamMembers',
            'reviews.user',
            'tournamentType',
            )->findOrFail($id);
        // return $tournament;
        if($tournament){
            $tournament->tournamenType=TournamentType::where('id',$tournament->type)->get()->first();
            $tournament->EventType=EventType::where('id',$tournament->event_type)->get()->first();
            $tournament->TournamentLevel=TournamentLevel::where('id',$tournament->level)->get()->first();
            $tournament->documents_arr=$tournament->documents;
            $tournament->staff_arr=DB::table('tournament_staff')->where('tournament_id',$tournament->id)->get();
            $matches=array();
            foreach($tournament->macthes_schedule as $match){
                $matchObj=(Object)[];
                $matchObj->schedule_date=$match->schedule_date;
                $matchObj->schedule_time=$match->schedule_time;
                $matchObj->schedule_breaks=$match->schedule_breaks;
                $matchObj->venue_availability=$match->venue_availability;
                $matches[]=$matchObj;
            }
            $tournament->matches=$matches;
            $staffArr=array();
            foreach($tournament->staff_arr as $staff_obj){
                $staff=(Object)[];
                $staff->contact=$staff_obj->contact;
                $staff->responsibility=$staff_obj->responsibility;
                $staffArr[]=$staff;
            }
            $tournament->staffArr=$staffArr;

            $tournament->logos_arr=$tournament->logos;
            $tournament->banner_arr=$tournament->banners;
            if($tournament->firstRound){
                $tournament->firstRound=$tournament->firstRound;
                $tournament->firstRound->matches=$tournament->firstRound->matches;
                foreach($tournament->firstRound->matches as $m_key=>$match){
                    $match->team_1=$match->team_1;
                    $match->team_2=$match->team_2;
                    $match->winner_row=$match->winner_row;
                }
            }
            // dd($tournament->firstRound);
        }
        return view('admin.tournaments.show', compact('tournament'));
    }
    public function create()
    {
        return view('admin.tournaments.create');
    }

    public function store(StoreTournamentRequest $request)
    {
        $data = $request->validated();
        $tournament = Tournament::create($data);
        if($tournament){
            return redirect()->route('admin.tournaments.index')->with('success', 'Tournament saved successfully');
        }
        return redirect()->route('admin.tournaments.index')->with('error', 'Something went wrong');
    }
    public function edit($id)
    {
        $tournament = Tournament::findOrFail($id);
        return view('admin.tournaments.edit', compact('tournament'));
    }
    public function update(StoreTournamentRequest $request, $id)
    {
        $tournament = Tournament::findOrFail($id);
        $data = $request->validated();
        $tournament->update($data);
        return redirect()->route('admin.tournaments.index')->with('success', 'Tournament updated successfully');
    }
    public function featured(Request $request)
    {
        $tournament = Tournament::findOrFail($request->id);
        $tournament->is_featured = $request->is_featured;
        $tournament->save();
        return redirect()->route('admin.tournaments.index')->with('success', 'Tournament updated successfully');
    }
    public function destroy($id)
    {
        $tournament = Tournament::findOrFail($id);
        $tournament->is_active = 0;
        $tournament->save();
        return redirect()->route('admin.tournaments.index')->with('success', 'Tournament deleted successfully');
    }

    public function activate($id)
    {
        $tournament = Tournament::findOrFail($id);
        $tournament->is_active = 1;
        $tournament->save();
        return redirect()->route('admin.tournaments.index')->with('success', 'Tournament activated successfully');
    }


    
}

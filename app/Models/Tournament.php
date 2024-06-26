<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    // define fillable fields
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category_id',
        'type',
        'start_date',
        'end_date',
        'registration_dead_line',
        'event_type',
        'country_id',
        'city',
        'postal_code',
        'address',
        'latitude',
        'longitude',
        'number_of_teams',
        'format',
        'prize_distribution',
        'level',
        'entry_fee',
        'rules',
        'code_of_conduct',
        'age',
        'equipment_requirements',
        'schedule_date',
        'schedule_time',
        'schedule_breaks',
        'venue_availability',
        'second_match_date',
        'second_match_time',
        'second_match_breaks',
        'second_venue_availability',
        'third_match_date',
        'third_match_time',
        'third_match_breaks',
        'third_venue_availability',
        'fourth_match_date',
        'fourth_match_time',
        'fourth_match_breaks',
        'fourth_venue_availability',
        'contact_information',
        'roles_and_responsibilities',
        'sponsor_information',
        'overview',
        'is_featured',
        "open_date",
        "is_started",
        "available_teams",
        "match_type",
        "winners_pool",
        "eleminated_pool",
        "looser_pool",
        "pending_match_teams",
        "sponsors",
        "bank_information",
        "tournament_logo",
        "is_bracket_generated",
        "location",
        "lat",
        "long",
        "tournament_type"
    ];

    public function images()
    {
        return $this->hasMany(TournamentImage::class);
    }
    public function logos()
    {
        return $this->hasMany(TournamentImage::class,'tournament_id','id')->where('caption','logo');
    }
    public function documents()
    {
        return $this->hasMany(TournamentImage::class,'tournament_id','id')->where('caption','document');
    }
    public function banners()
    {
        return $this->hasMany(TournamentImage::class,'tournament_id','id')->where('caption','banner');
    }

    public function tournamentCategories()
    {
        return $this->hasMany(TournamentCategory::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function type(){
        return $this->belongsTo(TournamentType::class);
    }
    public function country(){
        return $this->belongsTo(Country::class);
    }
    public function teams(){
        return $this->hasMany(Team::class);
    }
    public function pendingTeamsCount()
    {
        return $this->hasMany(Team::class)->where('status', 'pending')->count();
    }
    public function teamMembers(){
        return $this->hasManyThrough(TeamMember::class, Team::class, 'tournament_id', 'team_id');
    }
    public function reviews(){
        return $this->hasMany(Review::class);
    }
    public function rounds(){
        return $this->hasMany(TournamentRounds::class,'tournament_id','id')->orderBy('id', 'asc');;
    }
    public function macthes_schedule(){
        return $this->hasMany(Tournament_matches_schedule::class,'tournament_id','id')->orderBy('id', 'asc');;
    }
    public function descRounds(){
        return $this->hasMany(TournamentRounds::class,'tournament_id','id')->orderBy('id', 'desc');;
    }
    public function inProgressRound()
    {
        return $this->hasOne(TournamentRounds::class, 'tournament_id', 'id')
                    ->where('status', 'in_progress')
                    ->latest() // You might want to order by the latest round first
                    ->limit(1);
    }
    public function firstRound()
    {
        return $this->hasOne(TournamentRounds::class, 'tournament_id', 'id')
                    ->where('round_no', 1)
                    ->latest() // You might want to order by the latest round first
                    ->limit(1);
    }
    public function completed_rounds(){
        return $this->hasMany(TournamentRounds::class,'tournament_id','id')->where("status",'completed');
    }
    public function tournamentType(){
        $query = $this->belongsTo(TournamentType::class, 'type');
        $query->where('is_active', 1);
        return $query;
    }
    public function team(){
        return $this->belongsTo(Team::class);
    }

    public function wishlist(){
        return $this->hasMany(Wishlist::class);
    }
}

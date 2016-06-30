<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'views';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'view_date',
        'movie_id'
    ];
}
<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'actors';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
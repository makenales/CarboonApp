<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carbon extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'carbon';

    protected $fillable = [
    	'activity',
    	'activityType',
    	'mode',
    	'fuelType',
    	'country',
    	'carbonFootprint',
    	'expires_at'
    ];
}

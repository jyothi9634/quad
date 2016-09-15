<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkJobs extends Model
{
    // Table
    protected $table = 'network_jobs';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];


    protected $fillable = [
    	'user_id', 
    	'job_title',
    	'job_description', 
    	'created_by',
		'created_at',
		'created_ip',
		'updated_by',
		'updated_at',
		'updated_ip'
    ];
}

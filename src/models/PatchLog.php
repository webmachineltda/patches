<?php

namespace Webmachine\Patches\Models;

use Illuminate\Database\Eloquent\Model;

class PatchLog extends Model {
    
    protected $table = 'patch_logs';
    
    protected $fillable = ['patch', 'comment'];
        
    public $timestamps = false;
 
    public static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }    
}
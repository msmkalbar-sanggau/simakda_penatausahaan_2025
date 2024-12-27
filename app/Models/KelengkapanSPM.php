<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelengkapanSPM extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kelengkapan_spm';


    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'list_id',
        'uraian',
        'level',
        'jns_spp',
        'jenis_ls',
        'kontrak',
        'urut'

    ];

    protected $hidden = [
        'username_created',
        'created_at',
        'username_updated',
        'updated_at'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiSPP extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'verifikasi_spp';


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
        'no_spp',
        'kd_skpd',
        // 'kd_sub_skpd',
        'checked',
        'id_kelengkapan_spm',
    ];

    protected $hidden = [
        'username_created',
        'created_at',
        'username_updated',
        'updated_at'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InviteCode extends Model
{
    protected $fillable = [
        'admin_id',
        'code',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'invite_code_id');
    }

    public static function generate(): string
    {
        do {
            $code = strtoupper(Str::random(4) . '-' . Str::random(4));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function isValid(): bool
    {
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        return true;
    }
}
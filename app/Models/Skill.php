<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'name',
        'color_code',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }

    public function getTextColor()
    {
        $hex = ltrim($this->color_code, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $brightness = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        return $brightness > 155 ? '#000000' : '#FFFFFF';
    }
}

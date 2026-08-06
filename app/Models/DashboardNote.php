<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardNote extends Model
{
    protected $table = 'dashboard_notes';

    protected $fillable = ['content', 'updated_by'];

    /** Get the single note (always row id=1). */
    public static function getNote(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            ['content' => null, 'updated_by' => null]
        );
    }
}

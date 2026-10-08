<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['code', 'company_name', 'contact_person', 'email', 'phone', 'address', 'status'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function parts()
    {
        return $this->hasMany(Part::class);
    }
}

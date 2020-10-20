<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $fillable = ["shopify_id", "topic", "user_id", "data", "is_executed", "updated_at", "created_at"];


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory;
    //  use SoftDeletes;

    protected $table = 'conversations';
    protected $fillable = ['type', 'name', 'description', 'created_by'];

    public function users()
    {
        return $this->belongsToMany(User::class)
                ->using(ConversationUser::class)   // tell Eloquent to use the custom pivot model
                ->withPivot([
                    'role', 'last_read_at', 'is_muted',
                    'joined_at', 'left_at',
                ])
                ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

}
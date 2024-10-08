<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vocabulary extends Model
{
    use HasFactory;

		protected $table = 'vocabulary';

    protected $fillable = [
			'telegraph_chat_id',
			'word',
			'translation',
			'is_learned'
		];
}

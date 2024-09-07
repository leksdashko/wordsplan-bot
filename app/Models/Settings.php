<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

		protected $fillable = [
			'telegraph_chat_id',
			'mode',
			'app_lang',
			'user_lang',
			'learning_lang'
		];
}

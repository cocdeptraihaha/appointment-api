<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Contact
 * 
 * @property string $id
 * @property string|null $avatar
 * @property string $first_name
 * @property string|null $last_name
 * @property string|null $email
 * @property string|null $phone_number
 * 
 * @property Collection|Appointment[] $appointments
 *
 * @package App\Models
 */
class Contact extends Model
{
	protected $table = 'contacts';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'id',
		'avatar',
		'first_name',
		'last_name',
		'email',
		'phone_number'
	];

	public function appointments()
	{
		return $this->hasMany(Appointment::class);
	}
}

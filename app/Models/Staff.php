<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Staff
 * 
 * @property string $id
 * @property string|null $avatar
 * @property string $name
 * 
 * @property Collection|Appointment[] $appointments
 * @property Collection|Service[] $services
 *
 * @package App\Models
 */
class Staff extends Model
{
	protected $table = 'staff';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'id',
		'avatar',
		'name'
	];

	public function appointments()
	{
		return $this->hasMany(Appointment::class);
	}

	public function services()
	{
		return $this->belongsToMany(Service::class, 'staff_services');
	}
}

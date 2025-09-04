<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Service
 * 
 * @property string $id
 * @property string $name
 * 
 * @property Collection|Appointment[] $appointments
 * @property Collection|Staff[] $staff
 *
 * @package App\Models
 */
class Service extends Model
{
	protected $table = 'services';
	public $incrementing = false;
	public $timestamps = false;

	protected $fillable = [
		'id',
		'name'
	];

	public function appointments()
	{
		return $this->belongsToMany(Appointment::class, 'appointment_services');
	}

	public function staff()
	{
		return $this->belongsToMany(Staff::class, 'staff_services');
	}
}

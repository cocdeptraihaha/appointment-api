<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AppointmentType
 * 
 * @property string $id
 * @property string $label
 * @property string|null $color
 * @property string|null $deleted_at
 * 
 * @property Collection|Appointment[] $appointments
 *
 * @package App\Models
 */
class AppointmentType extends Model
{
	use SoftDeletes;
	protected $table = 'appointment_types';
	public $incrementing = false;
	public $timestamps = true;

	protected $fillable = [
		'id',
		'label',
		'color'
	];

	public function appointments()
	{
		return $this->hasMany(Appointment::class, 'type_id');
	}
}

<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Appointment
 * 
 * @property string $id
 * @property string $title
 * @property string|null $type_id
 * @property string|null $contact_id
 * @property string|null $staff_id
 * @property int $start_time
 * @property int $end_time
 * 
 * @property AppointmentType|null $appointment_type
 * @property Contact|null $contact
 * @property Staff|null $staff
 * @property Collection|Service[] $services
 *
 * @package App\Models
 */
class Appointment extends Model
{
	protected $table = 'appointments';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'start_time' => 'int',
		'end_time' => 'int'
	];

	protected $fillable = [
		'id',
		'title',
		'type_id',
		'contact_id',
		'staff_id',
		'start_time',
		'end_time'
	];

	public function appointment_type()
	{
		return $this->belongsTo(AppointmentType::class, 'type_id');
	}

	public function contact()
	{
		return $this->belongsTo(Contact::class);
	}

	public function staff()
	{
		return $this->belongsTo(Staff::class);
	}

	public function services()
	{
		return $this->belongsToMany(Service::class, 'appointment_services');
	}
}

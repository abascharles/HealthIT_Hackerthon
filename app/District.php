<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
	protected $table = 'districts';
	public $timestamps = false;
	protected $guarded = ['id', '_token', 'name'];

	/*$sql = "
		ALTER TABLE `districts`
		ADD `subcounty_person1` varchar(100) COLLATE 'latin1_swedish_ci' NULL AFTER `comment`,
		ADD `subcounty_position1` varchar(100) COLLATE 'latin1_swedish_ci' NULL AFTER `subcounty_person1`,
		ADD `subcounty_email1` varchar(100) COLLATE 'latin1_swedish_ci' NULL AFTER `subcounty_position1`,
		ADD `subcounty_person2` varchar(100) COLLATE 'latin1_swedish_ci' NULL AFTER `subcounty_email1`,
		ADD `subcounty_position2` varchar(100) COLLATE 'latin1_swedish_ci' NULL AFTER `subcounty_person2`,
		ADD `subcounty_email2` varchar(100) COLLATE 'latin1_swedish_ci' NULL AFTER `subcounty_position2`;
	";*/
}

<?php

namespace kissj\Participant\Ist;

use kissj\User\User;
use LeanMapper\Entity;

/**
 * @property int            $id
 * @property string|null    $firstName,
 * @property string|null    $lastName,
 * @property string|null    $allergies,
 * @property \DateTime|null $birthDate m:passThru(dateFromString|dateToString)
 * @property string|null    $birthPlace,
 * @property string|null    $country,
 * @property string|null    $gender,
 * @property string|null    $permanentResidence,
 * @property string|null    $scoutUnit,
 * @property string|null    $telephoneNumber,
 * @property string|null    $email,
 * @property string|null    $foodPreferences,
 * @property string|null    $cardPassportNumber,
 * @property string|null    $notes
 *
 * @property string|null    $workPreferences,
 * @property string|null    $skills,
 * @property string|null    $languages,
 * @property \DateTime|null      $arrivalDate, m:passThru(dateFromString|dateToString)
 * @property \DateTime|null      $leavingDate, m:passThru(dateFromString|dateToString)
 * @property string|null         $carRegistrationPlate
 *
 * @property boolean        $finished
 * @property User           $user m:hasOne
 */
class Ist extends Entity {
	
	public function dateToString(\DateTime $val): string {
		return $val->format(DATE_ISO8601);
	}
	
	public function dateFromString(string $val): string {
		return new \DateTime($val);
	}
}
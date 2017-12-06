<?php

namespace kissj\User;


class RoleService {
	
	/** @var RoleRepository */
	private $roleRepository;
	
	private $possibleRoles;
	private $eventName;
	private $statuses;
	
	public function __construct(array $possibleRoles,
								RoleRepository $roleRepository,
								string $eventName) {
		$this->possibleRoles = $possibleRoles;
		$this->roleRepository = $roleRepository;
		$this->eventName = $eventName;
		$this->statuses = [
			'open',
			'closed',
			'aprooved',
			'paid'];
	}
	
	
	// ROLES
	
	public function isUserRoleNameValid(string $role): bool {
		return in_array($role, $this->possibleRoles);
	}
	
	public function getRole(?User $user, string $event = 'cej2018'): ?Role {
		if (is_null($user)) {
			return null;
		} else {
			return $this->roleRepository->findOneBy(['user' => $user]);
		}
	}
	
	public function addRole(User $user, string $roleName) {
		$role = new Role();
		$role->name = $roleName;
		$role->user = $user;
		$role->event = $this->eventName;
		$role->status = $this->getFirstStatus($roleName);
		$this->roleRepository->persist($role);
	}
	
	
	// STATUSES
	
	// for rendering
	
	public function getHelpForRole(?Role $role): ?string {
		if (is_null($role)) {
			return null;
		} else {
			switch ($role->status) {
				case 'open':
					return 'Vyplň všechny údaje o sobě a potom klikni na Uzavřít registraci dole';
				case 'closed':
					return 'Tvoje registrace čeká na schválení (schvalovat začneme od 1.1.2018). Pokud to trvá moc dlouho, ozvi se nám na mail cej2018@skaut.cz';
				case 'aprooved':
					return 'Tvoje registrace byla přijata! Teď nadchází placení. Tvoje platební údaje jsou níže';
				case 'paid':
					return 'Registraci máš vyplněnou, odevzdanou, přijatou i zaplacenou. Těšíme se na tebe na akci!';
				default:
					throw new \Exception('Unknown role '.$role->status);
			}
		}
	}
	
	
	// for Payment class
	
	public function setPaid(User $user) {
		// TODO implement
		
		// search for corrent Role with $this->eventName;
		
		// check if Role has status one before approoved with getPreviousStatus
		
		// set paid to Role
	}
	
	
	// for User class
	
	public function getFirstStatus($role): string {
		// TODO enhance for different roles
		return $this->statuses[0];
	}
	
	public function getNextStatus(string $status): string {
		if (!$this->isStatusValid($status)) {
			throw new \Exception('Unknown status "'.$status.'"');
		}
		if ($this->isStatusLast($status)) {
			throw new \Exception('Last role possible');
		}
		
		$key = array_search($status, $this->statuses);
		
		return $this->statuses[$key + 1];
	}
	
	private function getPreviousStatus(string $status): string {
		if (!$this->isStatusValid($status)) {
			throw new \Exception('Unknown status "'.$status.'"');
		}
		if ($this->isStatusFirst($status)) {
			throw new \Exception('First role possible');
		}
		
		$key = array_search($status, $this->statuses);
		
		return $this->statuses[$key - 1];
	}
	
	private function isStatusValid(string $status): bool {
		return in_array($status, $this->statuses);
	}
	
	private function isStatusLast(string $status): bool {
		return $status == end($this->statuses);
	}
	
	private function isStatusFirst(string $status): bool {
		return $status == $this->statuses[0];
	}
}
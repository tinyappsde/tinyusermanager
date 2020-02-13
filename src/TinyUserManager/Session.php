<?php

namespace TinyUserManager;

use Exception;
use TinyUserManager\Helpers\Database;

class Session {

	/**
	 * User that is logged in
	 *
	 * @var User|null
	 */
	private ?User $user = null;

	public function __construct() {
		if (!empty($_SESSION['SUM_user_id'])) {
			$this->user = UserManager::getUser($_SESSION['SUM_user_id']);
		}
	}

	/**
	 * Login and create a new session if successful, returns false otherwise
	 *
	 * @param string $email
	 * @param string $password
	 * @param bool $confirmedOnly
	 * @return boolean
	 */
	public function login(string $email, string $password, bool $confirmedOnly = false): bool {
		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		if ($user = UserManager::findUser($email)) {
			if (password_verify($password, $user->getPasswordHash())) {
				if ($confirmedOnly && !$user->isEmailConfirmed()) {
					return false;
				}
				$this->user = $user;
				$_SESSION['SUM_user_id'] = $user->getId();
				return true;
			}
		}

		return false;
	}

	/**
	 * Get user that is logged in
	 *
	 * @return  User|null
	 */
	public function getUser(): ?User {
		return $this->user;
	}

	/**
	 * Returns true if a user is logged in
	 *
	 * @return boolean
	 */
	public function loggedIn(): bool {
		return !empty($this->user);
	}

	/**
	 * Set user that is logged in
	 *
	 * @param  User  $user  User that is logged in
	 */
	public function setUser(User $user) {
		$this->user = $user;
	}
}

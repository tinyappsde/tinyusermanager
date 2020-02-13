<?php

namespace TinyUserManager;

use DateTime;
use Exception;
use TinyUserManager\Helpers\Database;

class UserManager {

	/**
	 * Find a user for a given email address
	 *
	 * @param string $email
	 * @return User|null
	 */
	public static function findUser(string $email): ?User {
		$email = strtolower(trim($email));
		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		$stmt = $db->prepare('SELECT * FROM `sum_users` WHERE `email`=:email');
		$stmt->execute([':email' => $email]);

		if ($result = $stmt->fetchObject()) {
			return User::fromRow($result);
		}

		return null;
	}

	/**
	 * Get a user by ID
	 *
	 * @param integer $id
	 * @return User|null
	 */
	public static function getUser(int $id): ?User {
		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		$stmt = $db->prepare('SELECT * FROM `sum_users` WHERE `id`=:id');
		$stmt->execute([':id' => $id]);

		if ($result = $stmt->fetchObject()) {
			return User::fromRow($result);
		}

		return null;
	}

	/**
	 * Update a user in the database
	 *
	 * @param User $user
	 * @return boolean
	 */
	public static function updateUser(User $user): bool {
		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		$computedSetPreparation = '';
		$values = [
			':id' => $user->getId(),
			':email' => $user->getEmail()
		];

		foreach ($user->getFields() as $key => $value) {
			if (preg_match('/[a-z_]+/', $key) !== 1) {
				continue;
			}
			$computedSetPreparation .= ', `' . $key . '`=:' . $key;
			$values[':' . $key] = $value;
		}

		if ($stmt = $db->prepare('UPDATE `sum_users` SET `email`=:email' . $computedSetPreparation . ' WHERE `id`=:id')) {
			return $stmt->execute($values);
		}

		throw new Exception('invalid_fields');
	}

	/**
	 * Register a new user, returns a new User object when successful
	 *
	 * @param string $email
	 * @param string $password
	 * @param object|null $fields
	 * @return User|null
	 */
	public static function createUser(string $email, string $password, ?array $fields = null, $skipConfirmation = false): ?User {

		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

		try {
			$fieldsQueryPart = '';
			$values = [];

			if (!empty($fields) && $computed = self::computeFieldsQueryPart((object) $fields)) {
				$fieldsQueryPart = $computed['query_part'];
				$values = $computed['values'];
			}

			if ($skipConfirmation) {
				$fieldsQueryPart .= ', confirmed=true';
			}

			if (!$stmt = $db->prepare('INSERT INTO `sum_users` SET `email`=:email, `password`=:password' . $fieldsQueryPart)) {
				throw new Exception('db_error');
			}

			$values[':email'] = $email;
			$values[':password'] = $passwordHash;

			$stmt->execute($values);

			if ($stmt->rowCount() === 1) {
				return new User($db->lastInsertId(), $email, $skipConfirmation, (object) $fields, $passwordHash, new DateTime('now'), new DateTime('now'));
			}

			return null;
		} catch (Exception $e) {
			var_dump($e->getMessage());
			throw new Exception('error_creating_user');
		}
	}

	/**
	 * Internal method used for computing the SQL query parts for custom fields (only field names matching the /[a-z_]+/ regex pattern allowed)
	 *
	 * @param object $fields
	 * @return array
	 */
	private static function computeFieldsQueryPart(object $fields): array {
		$computedSetPreparation = '';
		$values = [];

		foreach ($fields as $key => $value) {
			if (preg_match('/[a-z_]+/', $key) !== 1) {
				throw new Exception('malformed_field_key');
			}
			if (in_array($key, ['id', 'email', 'password'])) {
				continue;
			}
			$computedSetPreparation .= ', `' . $key . '`=:' . $key;
			$values[':' . $key] = $value;
		}

		return [
			'query_part' => $computedSetPreparation,
			'values' => $values
		];
	}
}

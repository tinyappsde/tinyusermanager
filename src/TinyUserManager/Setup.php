<?php

namespace TinyUserManager;

use Exception;
use TinyUserManager\Helpers\Database;

class Setup {

	/**
	 * Creates all tables
	 *
	 * @return boolean
	 */
	public static function createTables(): bool {
		if (!self::createUserTable()) {
			throw new Exception('Error while creating user table. Is it already existing?');
		}
		if (!self::createConfirmationTable()) {
			throw new Exception('Error while creating the confirmation tokens table. Is it already existing?');
		}
		if (self::createPasswordForgotTable()) {
			throw new Exception('Error while creating the password forgot tokens table. Is it already existing?');
		}
		return true;
	}

	/**
	 * Creates the user table (if not existing)
	 *
	 * @return boolean
	 */
	public static function createUserTable(): bool {
		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		$stmt = $db->prepare(file_get_contents(__DIR__ . '/sql/tiny_users.sql'));
		return $stmt && $stmt->execute();
	}

	/**
	 * Creates the confirmation tokens table (if not existing)
	 *
	 * @return boolean
	 */
	public static function createConfirmationTable(): bool {
		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		$stmt = $db->prepare(file_get_contents(__DIR__ . '/sql/tiny_confirmation_tokens.sql'));
		return $stmt && $stmt->execute();
	}

	/**
	 * Creates the password forgot  table (if not existing)
	 *
	 * @return boolean
	 */
	public static function createPasswordForgotTable(): bool {
		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		$stmt = $db->prepare(file_get_contents(__DIR__ . '/sql/tiny_password_forgot_tokens.sql'));
		return $stmt && $stmt->execute();
	}
}

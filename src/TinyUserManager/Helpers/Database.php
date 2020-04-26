<?php

namespace TinyUserManager\Helpers;

use Exception;
use PDO;

class Database {

	/**
	 * PDO instance
	 *
	 * @var PDO
	 */
	protected static ?PDO $db = null;

	protected static string $tablePrefix = 'tiny_';

	/**
	 * Establish a new database connection and set the PDO instance
	 *
	 * @param string $host
	 * @param string $dbName
	 * @param string $user
	 * @param string $password
	 * @param string $charset
	 * @return void
	 */
	public static function conn(string $host = '127.0.0.1', string $dbName = 'tinyusermanager', string $user = 'root', string $password = 'root', string $charset = 'utf8mb4', string $tablePrefix = 'tiny_') {
		try {
			self::$db = new PDO('mysql:host=' . $host . ';dbname=' . $dbName . ';charset=' . $charset, $user, $password);
			self::$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

			self::$tablePrefix = $tablePrefix;
		} catch (Exception $e) {
			throw new Exception('db_connection_error');
		}
	}

	/**
	 * Returns the PDO object instance
	 *
	 * @return PDO|null
	 */
	public static function getPDO(): ?PDO {
		return self::$db;
	}

	/**
	 * Set a custom table prefix
	 *
	 * @param string $tablePrefix
	 * @return void
	 */
	public static function setTablePrefix(string $tablePrefix) {
		self::$tablePrefix = $tablePrefix;
	}

	/**
	 * Get the table prefix
	 *
	 * @return string
	 */
	public static function getPrefix(): string {
		return self::$tablePrefix;
	}

}

<?php

namespace TinyUserManager;

use DateTime;

class User {

	/**
	 * User ID
	 *
	 * @var int|null
	 */
	private ?int $id;

	/**
	 * Email address
	 *
	 * @var string
	 */
	private string $email;

	/**
	 * Email confirmation status
	 *
	 * @var bool|null
	 */
	private ?bool $emailConfirmed;

	/**
	 * Custom user fields/values
	 *
	 * @var object|null
	 */
	private ?object $fields;

	/**
	 * Password hash
	 *
	 * @var string|null
	 */
	private ?string $passwordHash;

	/**
	 * User creation date
	 *
	 * @var DateTime|null
	 */
	private ?DateTime $createdDate;

	/**
	 * User modification date
	 *
	 * @var int|null
	 */
	private ?DateTime $updatedDate;

	/**
	 * Create a new User object
	 *
	 * @param integer|null $id
	 * @param string $email
	 * @param object|null $fields
	 * @param string|null $passwordHash
	 * @param DateTime|null $createdDate
	 * @param DateTime|null $updatedDate
	 */
	public function __construct(?int $id, string $email, ?bool $emailConfirmed, ?object $fields = null, ?string $passwordHash = null, ?DateTime $createdDate = null, ?DateTime $updatedDate = null) {
		$this->id = $id;
		$this->email = $email;
		$this->emailConfirmed = $emailConfirmed;
		$this->fields = $fields;
		$this->passwordHash = $passwordHash;
		$this->createdDate = $createdDate;
		$this->updatedDate = $updatedDate;
	}

	/**
	 * Create a new User object from a database row
	 *
	 * @param object $row
	 * @return void
	 */
	public static function fromRow(object $row) {

		$fields = clone $row;
		unset($fields->id);
		unset($fields->email);
		unset($fields->confirmed);
		unset($fields->password);
		unset($fields->created);
		unset($fields->updated);

		return new self(
			$row->id,
			$row->email,
			$row->confirmed,
			$fields,
			$row->password,
			new DateTime($row->created),
			new DateTime($row->updated)
		);
	}

	/**
	 * Get the user's ID
	 *
	 * @return integer|null
	 */
	public function getId(): ?int {
		return $this->id;
	}

	/**
	 * Set the user's ID
	 *
	 * @param integer|null $id
	 * @return void
	 */
	public function setId(?int $id) {
		$this->id = $id;
	}

	/**
	 * Get the user's email address
	 *
	 * @return string
	 */
	public function getEmail(): string {
		return $this->email;
	}

	/**
	 * Set the users email confirmation status
	 *
	 * @param bool $emailConfirmed
	 * @return void
	 */
	public function setEmailConfirmed(bool $emailConfirmed) {
		$this->emailConfirmed = $emailConfirmed;
	}

	/**
	 * Get the users email confirmation status
	 *
	 * @return bool|null
	 */
	public function isEmailConfirmed(): ?bool {
		return $this->emailConfirmed;
	}

	/**
	 * Set the user's email
	 *
	 * @param string $email
	 * @return void
	 */
	public function setEmail(string $email)
	{
		$this->email = $email;
	}

	/**
	 * Get all (custom) user fields
	 *
	 * @return object|null
	 */
	public function getFields(): ?object {
		return $this->fields;
	}

	/**
	 * Get a specific field by its key
	 *
	 * @param string $name
	 * @return void
	 */
	public function getField(string $name) {
		return $this->fields->$name ?? null;
	}

	/**
	 * Set the fields object
	 *
	 * @param object|null $fields
	 * @return void
	 */
	public function setFields(?object $fields) {
		$this->fields = $fields;
	}

	/**
	 * Set the field value for a given key
	 *
	 * @param string $key
	 * @param [type] $value
	 * @return void
	 */
	public function setField(string $key, $value) {
		$this->fields->$key = $value;
	}

	/**
	 * Get the users password hash
	 *
	 * @return string|null
	 */
	public function getPasswordHash(): ?string {
		return $this->passwordHash;
	}

	/**
	 * Set the users password hash
	 *
	 * @param string|null $passwordHash
	 * @return void
	 */
	public function setPasswordHash(?string $passwordHash) {
		$this->passwordHash = $passwordHash;
	}

	/**
	 * Get the users creation date
	 *
	 * @return DateTime|null
	 */
	public function getCreatedDate(): ?DateTime {
		return $this->createdDate;
	}

	/**
	 * Get the users last update date
	 *
	 * @return DateTime|null
	 */
	public function getUpdatedDate(): ?DateTime {
		return $this->updatedDate;
	}

}

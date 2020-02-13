<?php

namespace TinyUserManager\Configs;

class EmailConfig {

	/**
	 * the address emails are sent from
	 *
	 * @var string
	 */
	private $from;

	/**
	 * the name of the sender
	 *
	 * @var string
	 */
	private $fromName;

	/**
	 * use smtp for sending
	 *
	 * @var bool
	 */
	private $isSmtp;

	/**
	 * the email server's hostname
	 *
	 * @var string
	 */
	private $host;

	/**
	 * the email server's SMTP port
	 *
	 * @var int
	 */
	private $port = 587;

	/**
	 * enable/disable smtp authentication
	 *
	 * @var bool
	 */
	private $smtpAuth = true;

	/**
	 * SMTP username
	 *
	 * @var string
	 */
	private $username;

	/**
	 * SMTP password
	 *
	 * @var string
	 */
	private $password;

	/**
	 * Encryption
	 *
	 * @var string
	 */
	private $smtpSecure = 'tls';

	/**
	 * Email Charset
	 *
	 * @var string
	 */
	private string $charset = 'utf8';

	/**
	 * create a new sender config
	 *
	 * @param string $from
	 * @param string $fromName
	 */
	public function __construct(string $from, string $fromName = '') {
		$this->from = $from;
		$this->fromName = $fromName;
	}

	/**
	 * Get the address emails are sent from
	 *
	 * @return  string
	 */
	public function getFrom() {
		return $this->from;
	}

	/**
	 * Set the address emails are sent from
	 *
	 * @param  string  $from  the address emails are sent from
	 *
	 * @return  self
	 */
	public function setFrom(string $from) {
		$this->from = $from;

		return $this;
	}

	/**
	 * Get the name of the sender
	 *
	 * @return  string
	 */
	public function getFromName() {
		return $this->fromName;
	}

	/**
	 * Set the name of the sender
	 *
	 * @param  string  $fromName  the name of the sender
	 *
	 * @return  self
	 */
	public function setFromName(string $fromName) {
		$this->fromName = $fromName;

		return $this;
	}

	/**
	 * Get the email server's hostname
	 *
	 * @return  string
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * Set the email server's hostname
	 *
	 * @param  string  $host  the email server's hostname
	 */
	public function setHost(string $host) {
		$this->host = $host;
	}

	/**
	 * Get the email server's SMTP port
	 *
	 * @return  int
	 */
	public function getPort() {
		return $this->port;
	}

	/**
	 * Set the email server's SMTP port
	 *
	 * @param  int  $port  the email server's SMTP port
	 *
	 * @return  self
	 */
	public function setPort(int $port) {
		$this->port = $port;

		return $this;
	}

	/**
	 * Get enable/disable smtp authentication
	 *
	 * @return  bool
	 */
	public function getSmtpAuth()
	{
		return $this->smtpAuth;
	}

	/**
	 * Set enable/disable smtp authentication
	 *
	 * @param  bool  $smtpAuth  enable/disable smtp authentication
	 */
	public function setSmtpAuth(bool $smtpAuth) {
		$this->smtpAuth = $smtpAuth;
	}

	/**
	 * Get sMTP username
	 *
	 * @return  string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * Set sMTP username
	 *
	 * @param  string  $username  SMTP username
	 */
	public function setUsername(string $username) {
		$this->username = $username;
	}

	/**
	 * Get sMTP password
	 *
	 * @return  string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * Set sMTP password
	 *
	 * @param  string  $password  SMTP password
	 */
	public function setPassword(string $password) {
		$this->password = $password;
	}

	/**
	 * Get encryption
	 *
	 * @return  string
	 */
	public function getSmtpSecure() {
		return $this->smtpSecure;
	}

	/**
	 * Set encryption
	 *
	 * @param  string  $smtpSecure  Encryption
	 */
	public function setSmtpSecure(string $smtpSecure) {
		$this->smtpSecure = $smtpSecure;
	}

	/**
	 * Get use smtp for sending
	 *
	 * @return  bool
	 */
	public function isSmtp() {
		return $this->isSmtp;
	}

	/**
	 * Enable usage of an SMTP connection
	 *
	 * @return void
	 */
	public function useSmtp() {
		$this->isSmtp = true;
	}

	/**
	 * Get email Charset
	 *
	 * @return  string
	 */
	public function getCharset() {
		return $this->charset;
	}

	/**
	 * Set email Charset
	 *
	 * @param  string  $charset  Email Charset
	 */
	public function setCharset(string $charset) {
		$this->charset = $charset;
	}
}

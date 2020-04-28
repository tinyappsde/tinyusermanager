<?php

namespace TinyUserManager;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use TinyUserManager\Configs\EmailConfig;
use TinyUserManager\Helpers\Database;

class PasswordResetHandler {

	/**
	 * Generate a confirmation token and send confirmation email to a given user
	 *
	 * @param User $user
	 * @param EmailConfig $emailConfig
	 * @param string $emailSubject
	 * @param string $emailTemplate
	 * @return boolean
	 *
	 * @throws Exception
	 */
	public static function sendConfirmationEmail(User $user, EmailConfig $emailConfig, string $emailSubject, string $emailTemplate): bool {

		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		try {
			$token = bin2hex(random_bytes(16));
			$tokenHash = password_hash($token, PASSWORD_BCRYPT, ['cost' => 8]);
		} catch (Exception $e) {
			throw new Exception('error_generating_token');
		}

		$db->beginTransaction();

		$stmt = $db->prepare('INSERT INTO `' . Database::getPrefix() . 'password_forgot_tokens` SET `user_id`=:id, `token`=:token ON DUPLICATE KEY UPDATE `token`=:tokenupdate');
		$stmt->execute([':id' => $user->getId(), ':token' => $tokenHash, ':tokenupdate' => $tokenHash]);

		if ($stmt->rowCount() >= 1) {
			$mail = new PHPMailer();

			$mail->CharSet = $emailConfig->getCharset();
			$mail->isHTML(true);

			if ($emailConfig->isSmtp()) {
				$mail->IsSMTP();
				$mail->Host = $emailConfig->getHost();
				$mail->Port = $emailConfig->getPort();
				$mail->SMTPAuth = $emailConfig->getSmtpAuth();
				$mail->Username = $emailConfig->getUsername();
				$mail->Password = $emailConfig->getPassword();
				$mail->SMTPSecure = $emailConfig->getSmtpSecure();
			}

			if (empty($emailConfig->getFrom())) {
				throw new \Exception('no_sender_address');
			}

			$mail->setFrom($emailConfig->getFrom(), $emailConfig->getFromName());
			$mail->Subject = $emailSubject ?: 'Please confirm to reset your password';

			$emailBody = str_replace('%token%', $token, $emailTemplate);
			$emailBody = str_replace('%uid%', $user->getId(), $emailBody);

			$mail->addAddress($user->getEmail());
			$mail->Body = $emailBody;
			if ($mail->send()) {
				$db->commit();
				return true;
			} else {
				$db->rollBack();
				throw new Exception('email_sending_error');
			}
		}

		throw new Exception('db_error');
	}

	/**
	 * Check a password reset confirmation token
	 *
	 * @param User $user
	 * @param string $token
	 * @return boolean
	 *
	 * @throws Exception
	 */
	public static function confirmPasswordForgotToken(User $user, string $token): bool {

		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		$stmt = $db->prepare('SELECT * FROM `' . Database::getPrefix() . 'password_forgot_tokens` WHERE `user_id`=:id');
		$stmt->execute([':id' => $user->getId()]);

		if ($result = $stmt->fetchObject()) {
			if (password_verify($token, $result->token)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Updates the password for a given user
	 *
	 * @param User $user
	 * @param string $newPassword
	 * @return boolean
	 */
	public static function setNewPassword(User $user, string $newPassword): bool {

		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		$passwordHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

		$stmt = $db->prepare('UPDATE `' . Database::getPrefix() . 'users` SET `password`=:password WHERE `id`=:id');
		if ($stmt->execute([':id' => $user->getId(), ':password' => $passwordHash])) {
			$stmt2 = $db->prepare('DELETE FROM `' . Database::getPrefix() . 'password_forgot_tokens` WHERE `id`=:id');
			if ($stmt->rowCount() !== 1) {
				throw new Exception('error_updating_user');
			}
			if (!$stmt2->execute([':id' => $user->getId()])) {
				throw new Exception('error_clearing_forgot_token_table');
			}

			return true;
		}

		throw new Exception('db_error');
	}
}

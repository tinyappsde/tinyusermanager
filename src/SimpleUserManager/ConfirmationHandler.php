<?php

namespace SimpleUserManager;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use SimpleUserManager\Configs\EmailConfig;
use SimpleUserManager\Helpers\Database;

class ConfirmationHandler {

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

		$stmt = $db->prepare('INSERT INTO `sum_confirmation_tokens` SET `user_id`=:id, `token`=:token ON DUPLICATE KEY UPDATE `token`=:tokenupdate');
		$stmt->execute([':id' => $user->getId(), ':token' => $tokenHash, ':tokenupdate' => $tokenHash]);

		if ($stmt->rowCount() === 1) {
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
			$mail->Subject = $emailSubject ?: 'Please activate your account';

			$emailBody = str_replace('%token%', $token, $emailTemplate);
			$emailBody = str_replace('%uid%', $user->getId(), $emailBody);

			$mail->addAddress($user->getEmail());
			$mail->Body = $emailBody;
			if ($mail->send()) {
				$db->commit();
				return true;
			} else {
				var_dump($mail->ErrorInfo);
				$db->rollBack();
				throw new Exception('email_sending_error');
			}
		}

		throw new Exception('db_error');
	}

	/**
	 * Check a confirmation token and activate the user, returns false if no matching token was found
	 *
	 * @param User $user
	 * @param string $token
	 * @return boolean
	 *
	 * @throws Exception
	 */
	public static function confirmUser(User $user, string $token): bool {

		if (!$db = Database::getPDO()) {
			throw new Exception('no_db_connection');
		}

		$stmt = $db->prepare('SELECT * FROM `sum_confirmation_tokens` WHERE `user_id`=:id');
		$stmt->execute([':id' => $user->getId()]);

		if ($result = $stmt->fetchObject()) {
			if (password_verify($token, $result->token)) {
				$stmt2 = $db->prepare('UPDATE `sum_users` SET `confirmed`=true WHERE `id`=:id');
				$stmt2->execute([':id' => $user->getId()]);
				$stmt3 = $db->prepare('DELETE FROM `sum_confirmation_tokens` WHERE `user_id`=:id');
				$stmt3->execute([':id' => $user->getId()]);

				if ($stmt2->rowCount() === 1) {
					return true;
				}

				throw new Exception('db_error');
			}
		}

		return false;
	}
}

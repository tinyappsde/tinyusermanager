# TinyUserManager [Work in Progress]
Easy-to-use PHP-library for user management

## Setup
1. Install with composer: `composer require tinyapps/tinyusermanager`
2. Setup database connection
For example you can create a db-config.php:
```php
<?php

return [
  'host' => '127.0.0.1',
  'db' => 'your_db',
  'user' => 'db_user',
  'password' => 'db_password'
];
```
And use the config file for establishing the connection:
```php
$dbConfig = require __DIR__ . '/path-to/db-config.php';

TinyUserManager\Helpers\Database::conn(
  $dbConfig['host'],
  $dbConfig['db'],
  $dbConfig['user'],
  $dbConfig['password'],
  'utf8mb4'
);
```
3. Setup the db tables using `TinyUserManager\Setup::createTables();`
4. (Optionally) customize the user table by adding additional fields/columns

## Code Examples
Please note, that `Database::conn()` must be called in every script before the usage of the TinyUserManager features that require a database connection. When using sessions, it is also required to have a php session started (`session_start()`).
A documentation will be added soon.
### Login
```php
$session = new TinyUserManager\Session();
if ($session->login('john.doe@example.com', 'opensesame')) {
  // login succeded
} else {
  // wrong credentials
}
```
### Check if a user is signed in
```php
if ($session->loggedIn()) {
  // user is logged in
  $user = $session->getUser();
}
```
### Registration
The code snippet below includes a custom field "phone" that has been added to the users table before.
```php
if ($user = TinyUserManager\UserManager::createUser('john.doe@example.com', 'opensesame', ['phone' => '+49 123 45678'])) {
  // User has been created
}
```
### Send the confirmation email
```php
$emailConfig = new TinyUserManager\EmailConfig('no-reply@example.com', 'Example');

TinyUserManager\ConfirmationHandler::sendConfirmationEmail($user, $emailConfig, 'Please confirm your registration', '<p><a href="https://example.com/activate/%uid%/%token%">Activate account</a></p>');
```
Optionally, if you want to use smtp, you can customize the `$emailConfig`:
```php
$emailConfig->useSmtp();
$emailConfig->setHost('mail.example.com');
$emailConfig->setUsername('no-reply@example.com');
$emailConfig->setPassword('your_email_password');
$emailConfig->setPort(465);
$emailConfig->setSmtpSecure('ssl');
```
To activate an account from the activation email:
```php
$user = TinyUserManager\UserManager::getUser($userId);
if (TinyUserManager\ConfirmationHandler::confirmUser($user, $token)) {
  // confirmed
}
```
### Update user details
```php
$user->setField('phone', '+49 123 456789');
TinyUserManager\UserManager::updateUser($user);
```
### Password reset
Send the password reset confirmation email:
```php
TinyUserManager\PasswordResetHandler::sendConfirmationEmail(
	$user,
	$emailConfig,
	$emailSubject,
	'<a href="https://example.com/reset/?uid=%uid%&token=%token%">Reset password</a>',
);
```
Confirm a password reset token:
```php
$user = TinyUserManager\UserManager::getUser($userId);
if (TinyUserManager\PasswordResetHandler::confirmPasswordForgotToken($user, $token)) {
  // valid
}
```
Set a new password (confirm the token again before!)
```php
TinyUserManager\PasswordResetHandler::setNewPassword($user, $newPassword);
```

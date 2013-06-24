<?php

/*!
 * This file is part of {@link https://github.com/MovLib MovLib}.
 *
 * Copyright © 2013-present {@link http://movlib.org/ MovLib}.
 *
 * MovLib is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * MovLib is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with MovLib.
 * If not, see {@link http://www.gnu.org/licenses/ gnu.org/licenses}.
 */
namespace MovLib\Model;

use \Memcached;
use \MovLib\Exception\ErrorException;
use \MovLib\Exception\UserException;
use \MovLib\Exception\SessionException;
use \MovLib\Model\AbstractModel;
use \MovLib\Utility\Crypt;
use \MovLib\Utility\DelayedMethodCalls;
use \MovLib\Utility\Image;

/**
 * Retrieve user specific data from the database.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class UserModel extends AbstractModel {


  // ------------------------------------------------------------------------------------------------------------------- Constants


  /**
   * Load the user from session.
   *
   * @var string
   */
  const FROM_SESSION = "session";

  /**
   * Load the user from ID.
   *
   * @var string
   */
  const FROM_ID = "user_id";

  /**
   * Load the user from name.
   *
   * @var string
   */
  const FROM_NAME = "name";

  /**
   * Load the user from mail.
   *
   * @var string
   */
  const FROM_MAIL = "mail";

  /**
   * Maximum length an email address can have.
   *
   * This length must be the same as it is defined in the database table. We redefine this here in order to validate the
   * length of the email address before attempting to insert it into our database. Be sure to count the strings length
   * with <code>mb_strlen()</code> because the length is defined per character and not per byte.
   *
   * @var int
   */
  const MAIL_MAX_LENGTH = 254;

  /**
   * Maximum length a username can have.
   *
   * This length must be the same as it is defined in the database table. We redefine this here in order to validate the
   * length of the chosen username before attempting to insert it into our database. Be sure to count the strings length
   * with <code>mb_strlen()</code> because the length is defined per character and not per byte.
   *
   * @var int
   */
  const NAME_MAX_LENGTH = 40;


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The CSRF token for this user's session.
   *
   * @var null|string
   */
  public $csrfToken;

  /**
   * The user's unique ID, defaults to zero (anonymous user).
   *
   * @var int
   */
  public $id;

  /**
   * The user's unique name if logged in, otherwise the user's IP address will be used as name.
   *
   * @var string
   */
  public $name;

  /**
   * The user's unique mail if logged in.
   *
   * @var string
   */
  public $mail;

  /**
   * The user's login status.
   *
   * <tt>TRUE</tt> if the user is logged in, otherwise <tt>FALSE</tt>.
   *
   * @var boolean
   */
  public $isLoggedIn = false;

  /**
   * Unix timestamp of the time when the user was created.
   *
   * @var int
   */
  public $timestampCreated;

  /**
   * Unix timestamp of the time the user last accessed MovLib.
   *
   * @var int
   */
  public $timestampLastAccess;

  /**
   * Unix timestamp of the time the user last logged in.
   *
   * @var int
   */
  public $timestampLastLogin;

  /**
   * Flag defining if the user's personal data is private or not.
   *
   * @var boolean
   */
  public $private;

  /**
   * Flag defining if the user's profile is deactivated or deleted.
   *
   * @var boolean
   */
  public $deleted;

  /**
   * PHP timezone string of the user's timezone.
   *
   * @see timezone_identifiers_list()
   * @see \DateTimeZone::listIdentifiers()
   * @var string
   */
  public $timezone;

  /**
   * The user's unique country ID.
   *
   * @var int
   */
  public $countryId;

  /**
   * The user's preferred system language's ID.
   *
   * @var int
   */
  public $languageId;

  /**
   * The file extension of the user's uploaded avatar.
   *
   * @var null|string
   */
  public $avatarExt;

  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new user model object.
   *
   * @param string $from
   *   [Optional] Defines how the object should be filled with data. Possible <var>$from</var> values are defined as
   *   class constants, see <var>FROM_*</var>.
   * @param mixed $value
   *   [Optional] Should contain the data to identify the user upon loading, see description of <var>$from</var>.
   * @throws \MovLib\Exception\ErrorException
   * @throws \MovLib\Exception\DatabaseException
   * @throws \MovLib\Exception\UserException
   *   If no user could be found for the given <var>$value</var> (if <var>$value</var> is not <tt>NULL</tt>).
   */
  public function __construct($from = null, $value = null) {
    switch ($from) {
      case self::FROM_SESSION:
        if (!($value = $this->sessionLoad())) {
          break;
        }
        $from = self::FROM_ID;

      case self::FROM_ID:
        $type = "d";
        break;

      case self::FROM_MAIL:
        $type = "s";
        break;

      case self::FROM_NAME:
        $type = "s";
        break;
    }
    if (isset($type) && $value !== null) {
      // @todo How about anon IP users?
      try {
        foreach ($this->select(
          "SELECT
            `user_id` AS `id`,
            `name`,
            `mail`,
            UNIX_TIMESTAMP(`created`) AS `timestampCreated`,
            UNIX_TIMESTAMP(`access`) AS `timestampLastAccess`,
            UNIX_TIMESTAMP(`login`) AS `timestampLastLogin`,
            `private`,
            `deleted`,
            `timezone`,
            `avatar_ext` AS `avatarExt`,
            `country_id` AS `countryId`,
            `language_id` AS `languageId`
          FROM `users`
            WHERE `{$from}` = ?
          LIMIT 1", $type, [ $value ]
        )[0] as $name => $value) {
          $this->{$name} = $value;
        }
        settype($this->private, "boolean");
        settype($this->deleted, "boolean");
      } catch (ErrorException $e) {
        throw new UserException("Could not find user for {$from} '{$value}'!", $e);
      }
    }
    // If we have no ID until this point, create anon user.
    if (!isset($this->id)) {
      $this->id = 0;
      // @todo It is important to configure our servers that they will always submit this key with the user's IP address
      //       especially if we start using proxies. It's no problem to alter the submitted FastCGI parameters (something
      //       we are doing heavily already).
      $this->name = $_SERVER["REMOTE_ADDR"];
    }
  }


  // ------------------------------------------------------------------------------------------------------------------- Public Methods


  /**
   * Check if a user with the given value exists.
   *
   * @param string $column
   *   The column name against which the value should be checked. Use the <var>FROM_*</var> class constants.
   * @param mixed $value
   *   The user attribute to check.
   * @param string $type
   *   [Optional] The datatype of the column in the database, defaults to string.
   * @return boolean
   *   <tt>TRUE</tt> if a user exists with the given value, otherwise <tt>FALSE</tt>.
   */
  public function exists($column, $value, $type = "s") {
    return !empty($this->select("SELECT `user_id` FROM `users` WHERE `{$column}` = ? LIMIT 1", $type, [ $value ]));
  }

  /**
   * Get the absolute URI to the user's avatar.
   *
   * <b>IMPORTANT!</b> Please note that images are not created on demand. If we'd do that we'd be open for easy DDoS
   * attacks that would kill our servers in no time. If you need a new image style, generate all images beforehands and
   * ensure that the user presenter creates the style directly after a new avatar was uploaded.
   *
   * @see \MovLib\Utility\Image::STYLE_150
   * @param int $style
   *   The image style, use the <var>STYLE_*</var> class constants of the image utility class.
   * @return boolean|string
   *   <tt>FALSE</tt> if the image doesn't exist, otherwise the absolute URI to the image is returned.
   */
  public function getAvatar($style = Image::STYLE_150) {
    if ($this->avatarExt) {
      if ($this->avatarExt === "svg" && is_file("{$_SERVER["HOME"]}/uploads/user/avatar-{$this->id}.svg")) {
        return "https://{$_SERVER["SERVER_NAME"]}/uploads/user/avatar-{$this->id}.svg";
      }
      elseif (is_file("{$_SERVER["HOME"]}/uploads/user/avatar-{$this->id}.{$style}.{$this->avatarExt}")) {
        return "https://{$_SERVER["SERVER_NAME"]}/uploads/user/avatar-{$this->id}.{$style}.{$this->avatarExt}";
      }
    }
    return false;
  }

  public function getAvatarHtml($style = Image::STYLE_150, $id = null) {

  }

  /**
   * Get the user's preferred ISO 639-1 alpha-2 language code.
   *
   * @staticvar string $languageCode
   *   Used to cache the lookup.
   * @return null|string
   *   The user's language code or <tt>NULL</tt> if the user has none.
   */
  public function getLanguageCode() {
    static $languageCode = null;
    try {
      if ($languageCode === null) {
        $languageCode = $this->select(
          "SELECT `iso_alpha-2` FROM `languages` WHERE `language_id` = ? LIMIT 1",
          "i",
          $this->languageId
        )[0]["iso_alpha-2"];
      }
      return $languageCode;
    } catch (ErrorException $e) {
      return null;
    }
  }

  /**
   * Pre-register a new user account.
   *
   * The pre-registration step is to put the provided information into our temporary database where we can pull it out
   * after the user clicked the link in the mail we've sent him.
   *
   * @param string $hash
   *   The activation hash used in the activation link for identification.
   * @param string $name
   *   The valid name of the new user.
   * @param string $mail
   *   The valid mail of the new user.
   */
  public function preRegister($hash, $name, $mail) {
    // @todo Catch exceptions, log and maybe even send a mail to the user?
    $this->prepareAndBind(
      "INSERT INTO `tmp` (`key`, `dyn_data`) VALUES (?, COLUMN_CREATE('name', ?, 'mail', ?, 'time', NOW() + 0))",
      "sss",
      [ $hash, $name, $mail ]
    )->execute()->close();
  }

  /**
   * Register a new user account.
   *
   * After the user clicked the activation link for the account we finally are able to creat an account for her/him. The
   * validation process is something the model does not care about, this is handled by the presenter, who's also
   * responsible to display the correct error messages. This method simply inserts the new data. Note that the object
   * this method is called on will automatically became the user that was just registered. Think of it like passing
   * the variable by reference. So if you call this on the global user object, the formerly anonymous global user is
   * now the registered new user. This is the desired behavior during our registration process because we directly
   * want to display the password settings page within the user's account.
   *
   * @param string $name
   *   The valid unique user's name.
   * @param string $mail
   *   The valid unique user's mail.
   * @param string $pass
   *   The unhashed user's password.
   * @return $this
   * @throws \MovLib\Exception\DatabaseException
   *   If inserting the data into our database failed.
   * @throws \MovLib\Exception\SessionException
   *   If starting the session failed. This should not be catched, this failure is related to Memcached being
   *   unavailable or full. Something that we cannot recover at runtime.
   */
  public function register($name, $mail, $pass) {
    global $i18n;
    $this->name = $name;
    $this->mail = $mail;
    $this->deleted = false;
    $this->timestampCreated = $this->timestampLastAccess = $this->timestampLastLogin = time();
    $this->prepareAndBind(
      "INSERT INTO `users`
        (`name`, `mail`, `pass`, `created`, `login`, `timezone`, `init`, `dyn_data`, `language_id`)
      VALUES
        (?, ?, ?, NOW(), NOW(), 'UTC', ?, '', {$i18n->getLanguageId()})",
      "ssss", [ $name, $mail, password_hash($pass, PASSWORD_BCRYPT), $mail ]
    )->execute();
    $this->id = $this->stmt->insert_id;
    return $this->close()->sessionStart();
  }

  /**
   * Add reset password request to our temporary database table.
   *
   * @param string $hash
   *   The password reset hash used in the password reset link for identification.
   * @param string $mail
   *   The valid mail of the user.
   */
  public function preResetPassword($hash, $mail) {
    // @todo Catch exceptions, log and maybe even send a mail to the user?
    $this->prepareAndBind(
      "INSERT INTO `tmp` (`key`, `dyn_data`) VALUES (?, COLUMN_CREATE('mail', ?, 'time', NOW() + 0))",
      "ss",
      [ $hash, $mail ]
    )->execute()->close();
  }

  /**
   * Select the data that was previously stored from the temporary database.
   *
   * @param string $hash
   *   The user submitted hash to identify the reset password request.
   * @return null|array
   *   <tt>NULL</tt> if no record was found for the hash, otherwise an associative array with the following keys:
   *   <em>name</em>, <em>mail</em>, and <em>time</em>. The name might be <tt>NULL</tt>, depending on the data that
   *   was stored previously (e.g. reset password requests do not have the name).
   */
  public function selectTmpData($hash) {
    try {
      return $this->select(
        "SELECT
          COLUMN_GET(`dyn_data`, 'name' AS CHAR(" . self::NAME_MAX_LENGTH . ")) AS `name`,
          COLUMN_GET(`dyn_data`, 'mail' AS CHAR(" . self::MAIL_MAX_LENGTH . ")) AS `mail`,
          COLUMN_GET(`dyn_data`, 'time' AS UNSIGNED) AS `time`
        FROM `tmp`
          WHERE `key` = ?
        LIMIT 1", "s", [ $hash ]
      )[0];
    } catch (ErrorException $e) {
      return null;
    }
  }

  /**
   * Removes the given session ID from our session database.
   *
   * @param string $sessionId
   *   The session ID that should be removed.
   * @return $this
   */
  public function sessionDelete($sessionId) {
    // Fetch all configured Memcached servers from the global configuration and split them by the delimiter.
    $servers = explode(",", ini_get("session.save_path"));
    // Build the array as expected by Memcached::addServers().
    $c = count($servers);
    for ($i = 0; $i < $c; ++$i) {
      $servers[$i] = explode(":", $servers[$i]);
      // The port is mandatory!
      if (!isset($servers[$i][1])) {
        $servers[$i][1] = 0;
      }
    }
    $memcached = new Memcached();
    $memcached->addServers($servers);
    $memcached->delete(ini_get("memcached.sess_prefix") . $sessionId);
    return $this;
  }

  /**
   * Destroy the current session completely and log the user out.
   *
   * @return $this
   */
  public function sessionDestroy() {
    if (session_status() === PHP_SESSION_ACTIVE) {
      // Remove the cookie.
      $cookieParams = session_get_cookie_params();
      setcookie(session_name(), "", time() - 42000, $cookieParams["path"], $cookieParams["domain"], $cookieParams["secure"], $cookieParams["httponly"]);
      // Remove the session ID from our database.
      DelayedMethodCalls::stack($this, "sessionDelete", [ session_id() ]);
      // Remove all data associated with this session.
      session_destroy();
      // The user is no longer logged in.
      $this->isLoggedIn = false;
    }
    return $this;
  }

  /**
   * Initializes the session and starts a session if needed.
   *
   * @return null|int
   *   Returns the user ID stored in the session or <tt>NULL</tt> if no valid session was found.
   * @throws \MovLib\Exception\SessionException
   *   If starting the session failed. This should not be catched, this failure is related to Memcached being
   *   unavailable or full. Something that we cannot recover at runtime.
   */
  public function sessionLoad() {
    if (!empty($_COOKIE["MOVSID"]) && session_status() === PHP_SESSION_NONE) {
      if (session_start() === false) {
        throw new SessionException("Could not start session.");
      }
      if (!isset($_SESSION["TTL"]) || !empty($_SESSION["TTL"]) || $_SESSION["TTL"] < time()) {
        session_regenerate_id(true);
        $_SESSION["TTL"] = time() + ini_get("session.gc_maxlifetime");
      }
      if (isset($_SESSION["UID"]) && !empty($_SESSION["UID"])) {
        $this->csrfToken = isset($_SESSION["CSRF"]) ? $_SESSION["CSRF"] : Crypt::randomHash();
        $this->isLoggedIn = true;
        return $_SESSION["UID"];
      }
      $this->sessionDestroy();
    }
  }

  /**
   * Forcefully starts a new session for a logged in user, invalidating any previously set data and regenerating the
   * session ID. This should be called after a user has successfully logged in.
   *
   * @return $this
   * @throws \MovLib\Exception\SessionException
   *   If starting the session failed. This should not be catched, this failure is related to Memcached being
   *   unavailable or full. Something that we cannot recover at runtime.
   */
  public function sessionStart() {
    $sessionStatus = session_status();
    if ($sessionStatus === PHP_SESSION_ACTIVE) {
      $this->sessionDestroy();
    }
    if (session_start() === true) {
      session_regenerate_id(true);
      $_SESSION["CSRF"] = $this->csrfToken = Crypt::randomHash();
      $_SESSION["TTL"] = time() + ini_get("session.gc_maxlifetime");
      $_SESSION["UID"] = $this->id;
      $this->isLoggedIn = true;
    }
    else {
      new SessionException("Could not start session.");
    }
    return $this;
  }

  /**
   * Validate the user submitted password against the stored hash of the current user.
   *
   * This will either log the user in if she/he isn't logged in yet or regenerate the session ID.
   *
   * @param string $name
   *   Value of the name attribute of the input element from the form that was submitted.
   * @return boolean
   *   <tt>TRUE</tt> if the password was valid, otherwise <tt>FALSE</tt>.
   * @throws \MovLib\Exception\SessionException
   *   If starting the session failed. This should not be catched, this failure is related to Memcached being
   *   unavailable or full. Something that we cannot recover at runtime.
   */
  public function validatePassword($name = "pass") {
    $pass = $this->selectAll("SELECT `pass` FROM `users` WHERE `user_id` = {$this->id} LIMIT 1")[0]["pass"];
    if (!isset($_POST[$name]) || password_verify($_POST[$name], $pass) === false) {
      return false;
    }
    session_status() === PHP_SESSION_ACTIVE ? session_regenerate_id(true) : $this->sessionStart();
    return true;
  }


  // ------------------------------------------------------------------------------------------------------------------- Public Static Methods


  /**
   * Validate the given username.
   *
   * <b>Usage example:</b>
   * <pre>if ($error = UserModel::validateName($name)) {
   *   throw new Exception($error);
   * }</pre>
   *
   * @link http://api.drupal.org/api/drupal/core!modules!user!user.module/function/user_validate_name/8
   * @global \MovLib\Model\I18nModel $i18n
   *   The global i18n model instance.
   * @param string $name
   *   The username to validate.
   * @return null|string
   *   A translated string containing the error message if something is wrong with the given name, otherwise <tt>NULL</tt>.
   */
  public static function validateName($name) {
    global $i18n;
    if (empty($name)) {
      return $i18n->t("You must enter a username.");
    }
    if (substr($name, 0, 1) === " ") {
      return $i18n->t("The username cannot begin with a space.");
    }
    if (substr($name, -1) === " ") {
      return $i18n->t("The username cannot end with a space.");
    }
    if (strpos($name, "  ") !== false) {
      return $i18n->t("The username cannot contain multiple spaces in a row.");
    }
    if (preg_match("/[^\x{80}-\x{F7} a-z0-9@_.\'-]/i", $name) === 1) {
      return $i18n->t("The username contains an illegal character.");
    }
    if (preg_match(
      "/[\x{80}-\x{A0}" .   // Non-printable ISO-8859-1 + NBSP
      "\x{AD}" .            // Soft-hyphen
      "\x{2000}-\x{200F}" . // Various space characters
      "\x{2028}-\x{202F}" . // Bidirectional text overrides
      "\x{205F}-\x{206F}" . // Various text hinting characters
      "\x{FEFF}" .          // Byte order mark
      "\x{FF01}-\x{FF60}" . // Full-width latin
      "\x{FFF9}-\x{FFFD}" . // Replacement characters
      "\x{0}-\x{1F}]/u",    // NULL byte and control characters
      $name
    ) === 1) {
      return $i18n->t("The username contains an illegal character.");
    }
    if (mb_strlen($name) > self::NAME_MAX_LENGTH) {
      return $i18n->t("The username {0} is too long: it must be {1,number,integer} characters or less.", [ $name, self::NAME_MAX_LENGTH ]);
    }
    // @todo The blacklist content must be translated along with the routes.
    $blacklist = json_decode(file_get_contents(__DIR__ . "/UserModelBlacklist.json"));
    $c = count($blacklist);
    for ($i = 0; $i < $c; ++$i) {
      if ($name === $blacklist[$i]) {
        return $i18n->t("The username contains a system reserved word, please choose another one.");
      }
    }
  }

}

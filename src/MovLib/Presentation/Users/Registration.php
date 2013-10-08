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
namespace MovLib\Presentation\Users;

use \MovLib\Data\Delayed\Mailer;
use \MovLib\Data\User;
use \MovLib\Exception\RedirectException;
use \MovLib\Presentation\Email\Users\Registration as RegistrationEmail;
use \MovLib\Presentation\Email\Users\RegistrationEmailExists;
use \MovLib\Presentation\Partial\Alert;
use \MovLib\Presentation\Partial\Form;
use \MovLib\Presentation\Partial\FormElement\InputCheckbox;
use \MovLib\Presentation\Partial\FormElement\InputEmail;
use \MovLib\Presentation\Partial\FormElement\InputSubmit;
use \MovLib\Presentation\Partial\FormElement\InputText;

/**
 * User registration presentation.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class Registration extends \MovLib\Presentation\Page {
  use \MovLib\Presentation\Profile\TraitProfile;


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * Flag indicating if this registration attempt was accepted or not.
   *
   * @var boolean
   */
  private $accepted = false;

  /**
   * The input email form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\InputEmail
   */
  private $email;

  /**
   * The page's form.
   *
   * @var \MovLib\Presentation\Partial\Form
   */
  private $form;

  /**
   * The input checkbox for accepting the terms of service and privacy policy.
   *
   * @var \MovLib\Presentation\Partial\FormElement\InputCheckbox
   */
  private $terms;

  /**
   * The input text form element for the username.
   *
   * @var \MovLib\Presentation\Partial\FormElement\AbstractInput
   */
  private $username;


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * Instantiate new user registration presentation.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Data\Session $session
   */
  public function __construct() {
    global $i18n, $session;

    // If the user is logged in, no need for registration.
    if ($session->isAuthenticated === true) {
      throw new RedirectException($i18n->r("/my"), 302);
    }

    // Start rendering the page.
    $this->init($i18n->t("Registration"))->user = new User();

    $this->email = new InputEmail("email", $i18n->t("Email Address"), [
      "autofocus",
      "placeholder" => $i18n->t("Enter your email address"),
    ], "<a href='{$i18n->r("/users/login")}'>{$i18n->t("Already have an account?")}</a>", false);

    $this->username = (new InputText("username", $i18n->t("Username"), [
      "maxlength" => User::MAX_LENGTH_NAME,
      "placeholder" => $i18n->t("Enter your desired username"),
    ]))->required();

    $this->terms = (new InputCheckbox("terms", $i18n->t("I accept the {0}Terms of Use{2} and the {1}Privacy Policy{2}.", [
      "<a href='{$i18n->r("/terms-of-use")}'>", "<a href='{$i18n->t("/privacy-policy")}'>", "</a>"
    ])))->required();

    $this->form = new Form($this, [ $this->email, $this->username, $this->terms ]);
    $this->form->attributes["action"] = $_SERVER["PATH_INFO"];
    $this->form->attributes["class"]  = "span span--6 offset--3";

    $this->form->actionElements[] = new InputSubmit([
      "title" => $i18n->t("Click here to sign up after you filled out all fields"),
      "value" => $i18n->t("Sign Up"),
    ]);

    if (isset($_GET["token"])) {
      $this->validateToken();
    }
  }

  /**
   * @inheritdoc
   */
  protected function getContent() {
    global $i18n;
    if ($this->accepted === true) {
      return "<div class='container'><small>{$i18n->t(
        "Mistyped something? No problem, simply {0}go back{1} and fill out the form again.",
        [ "<a href='{$_SERVER["PATH_INFO"]}'>", "</a>" ]
      )}</small></div>";
    }
    return "<div class='container'><div class='row'>{$this->form}</div></div>"
    ;
  }

  /**
   * Validation callback after auto-validation of form has succeeded.
   *
   * The redirect exception is thrown if the supplied data is valid. The user will be redirected to her or his personal
   * dashboard.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Data\Session $session
   * @return this
   * @throws \MovLib\Exception\RedirectException
   */
  public function validate() {
    global $i18n;
    $errors = null;

    if ($this->terms->value !== true) {
      $this->checkErrors([ $i18n->t("You have to agree to the {0}Privacy Policy{2} and {1}Terms of Use{2} to sign up.", [
        "<a href='{$i18n->r("/privacy-policy")}'>", "<a href='{$i18n->r("/terms-of-use")}'>", "</a>"
      ]) ]);
      return $this;
    }

    // We want to validate the original string!
    $this->username->value = $_POST[$this->username->id];

    if (substr($this->username->value, 0, 1) == " ") {
      $errors[] = $i18n->t("The username cannot begin with a space.");
    }

    if (substr($this->username->value, -1) == " ") {
      $errors[] = $i18n->t("The username cannot end with a space.");
    }

    if (strpos($this->username->value, "  ") !== false) {
      $errors[] = $i18n->t("The username cannot contain multiple spaces in a row.");
    }

    if (strpos($this->username->value, "/") !== false) {
      $errors[] = $i18n->t("The username cannot contain slashes.");
    }

    if (mb_strlen($this->username->value) > User::MAX_LENGTH_NAME) {
      $errors[] = $i18n->t("The username is too long: it must be {0,number,integer} characters or less.", [ User::MAX_LENGTH_NAME ]);
    }

    if (!$errors && $this->user->checkName($this->username->value) === true) {
      $errors[] = $i18n->t("The username {0} is already taken, please choose another one.", [ $this->placeholder($this->username->value) ]);
    }

    if ($this->checkErrors($errors) === false) {
      // Don't tell the user who's trying to register that we already have this email, otherwise it would be possible
      // to find out which emails we have in our system. Instead we send a message to the user this email belongs to.
      if ($this->user->checkEmail($this->email->value) === true) {
        Mailer::stack(new RegistrationEmailExists($this->email->value));
      }
      // If this is a vliad new registration generate the authentication token and insert the submitted data into our
      // temporary database, and of course send out the email with the token.
      else {
        $this->user->name  = $this->username->value;
        $this->user->email = $this->email->value;
        Mailer::stack(new RegistrationEmail($this->user));
      }

      // Settings this to true ensures that the user isn't going to see the form again. Check getContent()!
      $this->accepted    = true;

      $success           = new Alert($i18n->t("An email with further instructions has been sent to {0}.", [ $this->placeholder($this->email->value) ]));
      $success->title    = $i18n->t("Registration Successful");
      $success->severity = Alert::SEVERITY_INFO;
      $this->alerts     .= $success;
    }

    return $this;
  }

  /**
   * Validate the submitted authentication token, register, sign in and redirect to password settings.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Data\Session $session
   * @return this
   */
  public function validateToken() {
    global $i18n, $session;
    $errors = null;
    $user = new User();

    if (($data = $user->validateToken($errors)) && isset($data["name"]) && isset($data["email"])) {
      $this->username->attributes["value"] = $data["name"];
      $this->email->attributes["value"]    = $data["email"];

      if ($user->checkName($data["name"]) === true) {
        $this->username->invalid();
        $errors[] = $i18n->t("Unfortunately in the meantime someone took your desired username {0}, please choose another one.", [ $this->placeholder($data["name"]) ]);
      }

      if ($user->checkEmail($data["email"]) === true) {
        $this->email->invalid();
        $errors[] = $i18n->t("Seems like you already signed up with this email address, did you {0}forget your password?{1}", [
          "<a href='{$i18n->r("/user/reset-password")}'>", "</a>"
        ]);
      }
    }

    if ($this->checkErrors($errors) === false) {
      $rawPassword = User::getRandomPassword();
      $user->register($data["name"], $data["email"], $rawPassword);
      $session->authenticate($data["email"], $rawPassword);
      $_SESSION["password"] = $rawPassword;
      throw new RedirectException($i18n->r("/profile/password-settings"), 302);
    }

    return $this;
  }

}

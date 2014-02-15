<?php

/*!
 * This file is part of {@link https://github.com/MovLib MovLib}.
 *
 * Copyright © 2013-present {@link https://movlib.org/ MovLib}.
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
namespace MovLib\Presentation\Profile;

use \MovLib\Data\DateTimeZone;
use \MovLib\Presentation\Partial\Alert;
use \MovLib\Presentation\Partial\Country;
use \MovLib\Presentation\Partial\Currency;
use \MovLib\Presentation\Partial\Form;
use \MovLib\Presentation\Partial\FormElement\InputCheckbox;
use \MovLib\Presentation\Partial\FormElement\InputDate;
use \MovLib\Presentation\Partial\FormElement\InputHTML;
use \MovLib\Presentation\Partial\FormElement\InputImage;
use \MovLib\Presentation\Partial\FormElement\InputSubmit;
use \MovLib\Presentation\Partial\FormElement\InputText;
use \MovLib\Presentation\Partial\FormElement\InputURL;
use \MovLib\Presentation\Partial\FormElement\RadioGroup;
use \MovLib\Presentation\Partial\FormElement\Select;
use \MovLib\Presentation\Redirect\SeeOther as SeeOtherRedirect;

/**
 * Allows the user to manage his personalized settings.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class AccountSettings extends \MovLib\Presentation\Profile\Show {
  use \MovLib\Presentation\TraitForm;


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The user's avatar input file form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\InputImage
   */
  protected $avatar;

  /**
   * The user's birthday input date form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\InputDate
   */
  protected $birthday;

  /**
   * The user's country select form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\Select
   */
  protected $country;

  /**
   * The user's currency select form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\Select
   */
  protected $currency;

  /**
   * The user's language radio group form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\RadioGroup
   */
  protected $language;

  /**
   * The user's private input checkbox form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\InputCheckbox
   */
  protected $private;

  /**
   * The user's about me input HTML form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\InputHTML
   */
  protected $aboutMe;

  /**
   * The user's real name input text form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\InputText
   */
  protected $realName;

  /**
   * The user's sex input radio form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\RadioGroup
   */
  protected $sex;

  /**
   * The user's timezone select form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\Select
   */
  protected $timezone;

  /**
   * The user's website input url form element.
   *
   * @var \MovLib\Presentation\Partial\FormElement\InputURL
   */
  protected $website;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new user account settings presentation.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Kernel $kernel
   * @global \MovLib\Data\Session $session
   * @throws \MovLib\Presentation\Error\Unauthorized
   */
  public function __construct() {
    global $i18n, $kernel, $session;

    // Disallow caching of account settings.
    session_cache_limiter("nocache");

    $session->checkAuthorization($i18n->t("You need to sign in to access the danger zone."));
    $session->checkAuthorizationTimestamp($i18n->t("Please sign in again to verify the legitimacy of this request."));

    $this->init($i18n->t("Account Settings"), "/profile/account-settings", [[ $i18n->r("/profile"), $i18n->t("Profile") ]]);

    if (isset($_GET["delete_avatar"])) {
      $this->user->deleteAvatar()->commit();
      $kernel->alerts .= new Alert(
        $i18n->t("Your avatar image was deleted successfully"),
        $i18n->t("Avatar Deleted Successfully"),
        Alert::SEVERITY_SUCCESS
      );
      throw new SeeOtherRedirect($kernel->requestPath);
    }

    $this->realName = new InputText("real_name", $i18n->t("Real Name"), [
      "placeholder" => $i18n->t("Entery our real name"),
      "value"       => $this->user->realName,
    ]);

    $this->avatar = new InputImage("avatar", $i18n->t("Avatar"), $this->user);

    $this->sex = new RadioGroup("sex", $i18n->t("Sex"), [
      2 => $i18n->t("Female"),
      1 => $i18n->t("Male"),
      0 => $i18n->t("Unknown"),
    ], $this->user->sex, $i18n->t("Your sex will be displayed on your profile page and is used to create demographic evaluations."));

    $birthdayMax = date("Y-m-d", strtotime("-6 years", $_SERVER["REQUEST_TIME"]));
    $birthdayMin = date("Y-m-d", strtotime("-120 years", $_SERVER["REQUEST_TIME"]));
    $this->birthday = new InputDate("birthday", $i18n->t("Date of Birth"), [
      "max"   => $birthdayMax,
      "min"   => $birthdayMin,
      "title" => $i18n->t("The date must be between {0} (120 years) and {1} (6 years)", [
        $i18n->formatDate($birthdayMin, $this->user->timeZoneIdentifier, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE),
        $i18n->formatDate($birthdayMax, $this->user->timeZoneIdentifier, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::NONE)
      ]),
      "value" => $this->user->birthday,
    ], $i18n->t("Your birthday will be displayed on your profile page and is used to create demographic evaluations."));
    $this->birthday->setHelp($i18n->t("If your browser does not support the datepicker, please use the format {0}", [ $i18n->t("yyyy-mm-dd") ]));

    $this->aboutMe  = new InputHTML("about_me", $i18n->t("About Me"), $this->user->aboutMe, [
      "placeholder" => $i18n->t("Tell others about yourself, what do you do, what do you like, …"),
    ]);
    $this->aboutMe
      ->allowBlockqoutes()
      ->allowExternalLinks()
      ->allowHeadings()
      ->allowImages()
      ->allowLists()
    ;

    $systemLanguages = [];
    foreach ($kernel->systemLanguages as $systemLanguageCode => $systemLanguageLocale) {
      $systemLanguages[$systemLanguageCode] = \Locale::getDisplayLanguage($systemLanguageCode, $i18n->locale);
    }
    $i18n->getCollator()->asort($systemLanguages);
    $this->language = new RadioGroup("language", $i18n->t("System Language"), $systemLanguages, $this->user->systemLanguageCode);

    $this->country  = new Select("country", $i18n->t("Country"), Country::getCountries(), $this->user->countryCode);
    $this->timezone = new Select("time_zone_id", $i18n->t("Time Zone"), DateTimeZone::getTranslatedIdentifiers(), $this->user->timeZoneIdentifier);
    $this->currency = new Select("currency", $i18n->t("Currency"), Currency::getCurrencies(), $this->user->currencyCode);
    $this->website  = new InputURL("website", $i18n->t("Website"), [ "data-allow-external" => true, "value" => $this->user->website ]);
    $this->private  = new InputCheckbox("private", $i18n->t("Keep my data private!"), [ "value" => $this->user->private ], $i18n->t(
      "Check the following box if you’d like to hide your private data on your profile page. Your data will only be " .
      "used by MovLib for anonymous demographical evaluation of usage statistics and ratings. By providing basic data " .
      "like sex and country, scientists around the world are enabled to research the human interests in movies more " .
      "closely. Of course your real name won’t be used for anything!"
    ));

    $this->form = new Form($this, [
      $this->realName,
      $this->avatar,
      $this->sex,
      $this->birthday,
      $this->aboutMe,
      $this->language,
      $this->country,
      $this->timezone,
      $this->currency,
      $this->website,
      $this->private,
    ]);
    $this->form->multipart();

    $this->form->actionElements[] = new InputSubmit($i18n->t("Save"), [ "class" => "btn btn-large btn-success" ]);

    // Display delete button if the user just uploaded a new avatar or one is already present.
    if ($this->user->imageExists === true) {
      $this->avatar->inputFileAfter = $this->a("?delete_avatar=true", $i18n->t("Delete"), [ "class" => "btn btn-danger"]);
    }
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * @inhertidoc
   * @global \MovLib\Data\I18n $i18n
   */
  protected function getBreadcrumbs() {
    global $i18n;
    return [[ $i18n->r("/profile"), $i18n->t("Profile") ]];
  }

  /**
   * @inheritdoc
   */
  protected function getPageContent() {
    return $this->form;
  }

  /**
   * @inheritdoc
   * @global \MovLib\Data\I18n $i18n
   */
  protected function valid() {
    global $i18n;
    if ($this->avatar->path) {
      $this->user->upload($this->avatar->path, $this->avatar->extension, $this->avatar->height, $this->avatar->width);
    }
    $this->user->birthday           = $this->birthday->value;
    $this->user->countryCode        = $this->country->value;
    $this->user->currencyCode       = $this->currency->value;
    $this->user->private            = $this->private->value;
    $this->user->aboutMe            = $this->aboutMe->value;
    $this->user->realName           = $this->realName->value;
    $this->user->sex                = $this->sex->value;
    $this->user->systemLanguageCode = $this->language->value;
    $this->user->timeZoneIdentifier = $this->timezone->value;
    $this->user->website            = $this->website->value;
    $this->user->commit();
    $this->alerts                  .= new Alert(
      $i18n->t("Your account settings were updated successfully."),
      $i18n->t("Account Settings Updated Successfully"),
      Alert::SEVERITY_SUCCESS
    );
    return $this;
  }

}

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
namespace MovLib\Presentation\User;

use \MovLib\Exception\Client\ErrorNotFoundException;
use \MovLib\Exception\Client\RedirectPermanentException;
use \MovLib\Exception\UserException;
use \MovLib\Data\User;

/**
 * Description of Show
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class Show extends \MovLib\Presentation\AbstractSecondaryNavigationPage {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The user we are currently displaying.
   *
   * @var \MovLib\Data\User
   */
  protected $user;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new user presentation.
   *
   * @global \MovLib\Data\I18n $i18n
   * @throws \MovLib\Exception\NotFoundException
   * @throws \MovLib\Exception\Client\RedirectPermanentException
   */
  public function __construct() {
    global $i18n;
    try {
      $this->user = new User(User::FROM_NAME, $_SERVER["USER_NAME"]);
      if (($nameLower = mb_strtolower($this->user->name)) != $_SERVER["USER_NAME"]) {
        throw new RedirectPermanentException($i18n->r("/user/{0}", [ $nameLower ]));
      }
      $this->init($this->checkPlain($this->user->name));
    }
    catch (UserException $e) {
      throw new ErrorNotFoundException("No user with this name.");
    }
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * @inheritdoc
   */
  protected function getBreadcrumbs() {
    global $i18n;
    return [[ $i18n->r("/users"), $i18n->t("Users") ]];
  }

  /**
   * @inheritdoc
   */
  protected function getPageContent(){
    global $i18n;
    return "<pre>" . print_r($this->user, true) . "</pre>";
  }

  /**
   * @inheritdoc
   */
  protected function getSecondaryNavigationMenuitems() {
    global $i18n;
    return [
      [
        $i18n->r("/user/{0}", [ $_SERVER["USER_NAME"] ]),
        $this->checkPlain($this->user->name),
        [ "class" => "separator" ],
      ],
      [
        $i18n->r("/user/{0}/collection", [ $_SERVER["USER_NAME"] ]),
        $i18n->t("Collection"),
      ],
      [
        $i18n->r("/user/{0}/contact", [ $_SERVER["USER_NAME"] ]),
        $i18n->t("Contact"),
      ],
    ];
  }

}

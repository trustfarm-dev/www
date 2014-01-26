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

use \MovLib\Data\User\Full as FullUser;

/**
 * User account summary for logged in user's.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Show extends \MovLib\Presentation\Page {
  use \MovLib\Presentation\TraitSidebar;


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The user we are currently displaying.
   *
   * @var \MovLib\Data\User\Full
   */
  protected $user;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new user show presentation.
   *
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Data\Session $session
   * @throws \MovLib\Presentation\Error\Unauthorized
   */
  public function __construct() {
    global $i18n, $session;
    $session->checkAuthorization($i18n->t("You must be signed in to view your profile."));
    $this->init($i18n->t("Profile"), "/profile");
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * @inheritdoc
   */
  protected function getPageContent() {
    global $kernel, $i18n, $session;
    $var = [];
    $delayedMethods = new \ReflectionProperty($kernel, "delayedMethods");
    $delayedMethods->setAccessible(true);
    $delayedMethods = $delayedMethods->getValue($kernel);
    foreach ([ $this->user, $session, $kernel, $delayedMethods ] as $obj) {
      ob_start();
      var_dump($obj);
      $var[] = $kernel->htmlEncode(ob_get_clean());
    }
    return
      "<h2>{$i18n->t("Your Account Summary")}</h2>" .
      "<div class='r'>" .
        "<dl class='dl--horizontal s s7'>" .
          "<dt>{$i18n->t("Username")}</dt><dd>{$this->user->name}</dd>" .
          "<dt>{$i18n->t("User ID")}</dt><dd>{$this->user->id}</dd>" .
          "<dt>{$i18n->t("Edits")}</dt><dd>{$this->user->edits}</dd>" .
          "<dt>{$i18n->t("Reputation")}</dt><dd>{$this->user->reputation}</dd>" .
          "<dt>{$i18n->t("Email Address")}</dt><dd>{$this->user->email}</dd>" .
          "<dt>{$i18n->t("Joined")}</dt><dd>{$i18n->formatDate($this->user->created, $this->user->timeZoneIdentifier)}</dd>" .
          "<dt>{$i18n->t("Last visit")}</dt><dd>{$i18n->formatDate($this->user->access, $this->user->timeZoneIdentifier)}</dd>" .
        "</dl>" .
        "<div class='s s2'>{$this->getImage($this->user->getStyle(), $i18n->r("/user/{0}", [ $this->user->filename ]))}</div>" .
      "</div>"
    ;
  }

  /**
   * Initialize profile page.
   *
   * This automatically calls {@see initPage()}, {@see initBreadcrumb()}, {@see initLanguageLinks()}, {@see initSidebar()},
   * and instantiates a {@see \MovLib\Data\User\Full} object (available via {@see Show::$user}).
   *
   * @param string $title
   *   The translated profile page's title.
   * @param string $route
   *   The route key of this profile page.
   * @param array $breadcrumbs [optional]
   *   Numeric array containing additional breadcrumbs to put between home and the current page.
   * @return this
   */
  protected function init($title, $route, array $breadcrumbs = []) {
    global $i18n, $session;
    $this->initPage($title);
    $this->initBreadcrumb($breadcrumbs);
    $this->initLanguageLinks($route);

    $sidebar = [
      [ $i18n->r("/profile"), $i18n->t("Profile"), [ "class" => "ico ico-user" ] ],
      [ $i18n->r("/profile/messages"), $i18n->t("Messages"), [ "class" => "ico ico-email" ] ],
      [ $i18n->r("/profile/collection"), $i18n->t("Collection"), [ "class" => "ico ico-release" ] ],
      [ $i18n->r("/profile/wantlist"), $i18n->t("Wantlist"), [ "class" => "ico ico-heart" ] ],
      [ $i18n->r("/profile/lists"), $i18n->t("Lists"), [ "class" => "ico ico-ul" ] ],
      [ $i18n->r("/profile/watchlist"), $i18n->t("Watchlist"), [ "class" => "separator ico ico-view" ] ],
      [ $i18n->r("/profile/account-settings"), $i18n->t("Account"), [ "class" => "ico ico-settings" ] ],
      [ $i18n->r("/profile/notification-settings"), $i18n->t("Notifications"), [ "class" => "ico ico-notification" ] ],
      [ $i18n->r("/profile/email-settings"), $i18n->t("Email"), [ "class" => "ico ico-email" ] ],
      [ $i18n->r("/profile/password-settings"), $i18n->t("Password"), [ "class" => "ico ico-lock" ] ],
      [ $i18n->r("/profile/danger-zone"), $i18n->t("Danger Zone"), [ "class" => "ico ico-alert" ] ],
    ];
    $this->initSidebar($sidebar);

    $this->user = new FullUser(FullUser::FROM_ID, $session->userId);

    return $this;
  }

}

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
namespace MovLib\Presentation;

use \MovLib\Core\Intl;
use \MovLib\Partial\Navigation;

/**
 * The global language selection page.
 *
 * The language selection page is displayed if a user accesses MovLib without any language code specific subdomain. We
 * don't know what language the user might prefer and we don't want to guess (like many others are doing it, e.g. Google
 * and they're doing a really awful job there). Freedom of joice is the motto. This page is different than most other
 * MovLib page's and therefor we extend the abstract page and not the reference implementation.
 *
 * @route false
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
final class LanguageSelection extends \MovLib\Presentation\AbstractPresenter {

  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "LanguageSelection";
  // @codingStandardsIgnoreEnd

  /**
   * {@inheritdoc}
   */
  public function init() {
    $this->initPage($this->intl->t("Language Selection"));
    $this->next("//{$this->intl->code}.{$this->config->hostname}/");
    $this->stylesheets[] = "language-selection";
  }

  /**
   * {@inheritdoc}
   */
  public function getContent() {
    $prerender = $menuitems = null;
    foreach (Intl::$systemLanguages as $code => $locale) {
      $href = "//{$code}.{$this->config->hostname}/";
      // Doesn't validate, but the browsers like it. Please note that Chrome doesn't prerender more than one URL and
      // no HTTPS pages; there's nothing we can do about that. But it works great in Gecko and IE.
      $prerender  .= "<link rel='prefetch' href='{$href}'><link rel='prerender' href='{$href}'>";
      $menuitems[] = [ $href, \Locale::getDisplayLanguage($locale, $code), [ "lang" => $code ] ];
    }

    $navigation = new Navigation($this, $this->intl->t("Available Languages"), $menuitems, [ "class" => "well well-lg" ]);
    $navigation->glue = " / ";

    return "{$prerender}<p>{$this->intl->t("Please select your preferred language from the following list.")}</p>{$navigation}";
  }

  /**
   * {@inheritdoc}
   */
  public function getFooter() {
    return
      "<footer id='f'><div class='c'><div class='r'><p>{$this->intl->t(
        "Is your language missing from our list? Help us translate {sitename} to your language. More information can " .
        "be found at {0}our translation portal{1}.",
        [ "<a href='{$this->intl->r("/localize")}'>", "</a>", "sitename" => $this->config->sitename ]
      )}</p></div></div></footer>"
    ;
  }

  /**
   * {@inheritdoc}
   */
  public function getHeader() {}

  /**
   * {@inheritdoc}
   */
  public function getMainContent($content) {
    $this->response->setAlerts($this);
    return
      "<main class='{$this->id}-content' id='m' role='main'><div class='c'>" .
        "<h1 class='cf'>" .
          "<img alt='' height='192' src='{$this->fs->getExternalURL("asset://img/logo/vector.svg")}' width='192'>" .
          "<span>{$this->config->sitename}{$this->intl->t(
            "{0}The {1}free{2} movie library.{3}",
            [ "<small>", "<em>", "</em>", "</small>" ]
          )}</span>" .
        "</h1>{$this->getAlertNoScript()}{$this->alerts}{$content}" .
      "</div></main>"
    ;
  }

}

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

use \MovLib\Component\Collator;
use \MovLib\Partial\Navigation\Breadcrumb;

/**
 * Default page class with no content.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractPresenter extends \MovLib\Core\Presentation\DependencyInjectionBase {

  // @codingStandardsIgnoreStart
  /**
   * Short class name.
   *
   * @var string
   */
  const name = "AbstractPresenter";
  // @codingStandardsIgnoreEnd


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * Contains all alert messages of the current page.
   *
   * @var string
   */
  public $alerts;

  /**
   * Contains the CSS classes of the body element.
   *
   * @var string
   */
  protected $bodyClasses;

  /**
   * The presentation's breadcrumb navigation.
   *
   * @var \MovLib\Partial\Navigation\Breadcrumb
   */
  protected $breadcrumb;

  /**
   * The title used for the current page in the breadcrumb, defaults to the current title if not given.
   *
   * @deprecated
   * @var string
   */
  protected $breadcrumbTitle;

  /**
   * HTML that should be included after the page's content.
   *
   * @var string
   */
  protected $contentAfter;

  /**
   * HTML that should be included before the page's content.
   *
   * @var string
   */
  protected $contentBefore;

  /**
   * Additional elements for the <code><head></code> element.
   *
   * @var string
   */
  public $headElements;

  /**
   * HTML that should be included after the page's heading.
   *
   * @var string
   */
  protected $headingAfter;

  /**
   * HTML that should be included before the page's heading.
   *
   * @var string
   */
  protected $headingBefore;

  /**
   * The itemprop value for the page's heading.
   *
   * @var string
   */
  protected $headingSchemaProperty;

  /**
   * The page's unique ID.
   *
   * In order to identify a page in HTML, CSS, and JavaScript we have to use unique IDs. The unique ID of a page is
   * automatically generated from it's namespace and set after calling the <code>AbstractPage::init()</code>-method. The
   * unique page's ID is generated by removing the common part from the namespace (specifically the
   * <code>"MovLib\Presentation\"</code> string), replacing all backslashes with dashes, and finally lower-casing the
   * complete string. The unique ID for this page that would be generated with this class (if it wouldn't be abstract)
   * would be <code>"abstractpage"</code>.
   *
   * @see \MovLib\Presentation\AbstractPresenter::init()
   * @var string
   */
  public $id;

  /**
   * Settings to pass along with this presentation.
   *
   * @var array
   */
  public $javascriptSettings = [];

  /**
   * Numeric array containing all JavaScript module names that should be loaded with this presentation.
   *
   * @var array
   */
  public $javascripts = [];

  /**
   * The page's translated routes.
   *
   * <b>NOTE</b><br>
   * Must be public because it's used in the {@see \MovLib\Exception\ClientException\UnauthorizedException} to set the
   * language links.
   *
   * @var array
   */
  public $languageLinks;

  /**
   * Contains the namespace parts as array.
   *
   * @var array
   */
  protected $namespace;

  /**
   * The page's title used in the header.
   *
   * @var string
   */
  protected $pageTitle;

  /**
   * The type name of the schema of this presentation's content.
   *
   * @link http://schema.org/docs/schemas.html
   * @var string
   */
  protected $schemaType;

  /**
   * Additional stylesheets for this presentation.
   *
   * @var array
   */
  public $stylesheets = [];

  /**
   * The page's title.
   *
   * @var string
   */
  public $title;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  // @devStart
  // @codeCoverageIgnoreStart
  //
  // The constructor is final for all presenters!
  final public function __construct(\MovLib\Core\HTTP\Container $container) {
    parent::__construct($container);
  }
  // @codeCoverageIgnoreEnd
  // @devEnd


  // ------------------------------------------------------------------------------------------------------------------- Abstract Methods


  /**
   * Get the presentation.
   *
   * @return string
   *   The presentation.
   */
  abstract public function getContent();

  /**
   * Initialize the presentation.
   *
   * @return this
   */
  abstract public function init();


  // ------------------------------------------------------------------------------------------------------------------- Init Methods


  /**
   * Initialize the page's breadcrumb.
   *
   * @deprecated
   * @param array $breadcrumbs [optional]
   *   Numeric array containing additional breadcrumbs to put between home and the current page.
   * @return this
   */
  protected function initBreadcrumb(array $breadcrumbs = []) {
    $this->breadcrumb->addCrumbs($breadcrumbs);
    return $this;
  }

  /**
   * Initialize the language links for the current page.
   *
   * @param string $route
   *   The key of this route.
   * @param mixed $args [optional]
   *   The route arguments, defaults to no arguments.
   * @param boolean $plural [optional]
   *   Set to <code>TRUE</code> if the current page has a plural route, defaults to <code>FALSE</code>.
   * @param array $queries [optional]
   *   Array of key value pairs that should be appended as query string to the route. Note that the keys have to be in
   *   the default locale because they are translated like everything else.
   * @return this
   */
  final protected function initLanguageLinks($route, $args = null, $plural = false, array $queries = null) {
    $this->languageLinks = [ $route, $args, $plural, $queries ];
    return $this;
  }

  /**
   * Initialize the page.
   *
   * @param string $headTitle
   *   The presenter's <code><title></code> title.
   * @param string $pageTitle [optional]
   *   The presenter's <code><h1></code> title.
   * @param string $breadcrumbTitle [optional]
   *   The presenter's title for the breadcrumb's entry of the current presentation.
   * @return this
   */
  protected function initPage($headTitle, $pageTitle = null, $breadcrumbTitle = null) {
    // The substr() removes the \MovLib\Presentation\ part!
    $className         = strtolower(substr(get_class($this), 20));
    $this->namespace   = explode("\\", $className);
    array_pop($this->namespace); // The last element is the name of the class and not part of the namespace.
    $this->bodyClasses = strtr($className, "\\", " ");
    $this->id          = strtr($className, "\\", "-");
    $this->title       = $headTitle;
    $this->pageTitle   = $pageTitle ?: $headTitle;
    $this->breadcrumb  = new Breadcrumb($this->container, $breadcrumbTitle ?: $headTitle);
    return $this;
  }


  // ------------------------------------------------------------------------------------------------------------------- Alert Methods


  /**
   * Add alert message to the current presenter.
   *
   * @link http://www.w3.org/TR/wai-aria/roles#alert
   * @link http://www.w3.org/TR/wai-aria/states_and_properties#aria-live
   * @link https://developer.mozilla.org/en-US/docs/Web/Accessibility/ARIA/ARIA_Live_Regions
   * @param string $title
   *   The alert's translated title.
   * @param string $message
   *   The alert's translated message.
   * @param string $live
   *   The alert's ARIA-Live value, one of <code>"off"</code> (default and equals <code>NULL</code>),
   *   <code>"polite"</code>, or <code>"assertive"</code>.
   * @param string $type
   *   The alert's type, one of <code>NULL</code> (default), <code>"error"</code>, <code>"info"</code>,
   *   <code>"success"</code>, or <code>"warning"</code>.
   * @param string $role
   *   The alert's role, one of <code>"alert"</code>, <code>"log"</code>, or <code>"status"</code>.
   * @return this
   */
  final protected function getAlert($title, $message, $live, $type, $role) {
    // @devStart
    // @codeCoverageIgnoreStart
    $aria = [ null, "off", "polite", "assertive" ];
    assert(in_array($live, $aria), "The ARIA Live value must be one of '" . implode("', '", $aria) . "' if given.");
    $types = [ null, "error", "info", "success", "warning" ];
    assert(in_array($type, $types), "The type value must be one of '" . implode("', '", $types) . "' if given!");
    $roles = [ "alert", "log", "status" ];
    assert(in_array($role, $roles), "The role valut must be one of '" . implode("', '", $roles) . "' if given!");
    // @codeCoverageIgnoreEnd
    // @devEnd
    $live && ($live = " aria-live='{$live}'");
    $type && ($type = " alert-{$type}");
    return "<div{$live} class='alert{$type}' role='{$role}'><div class='c'><h2>{$title}</h2>{$message}</div></div>";
  }

  /**
   * Get the no script warning alert.
   *
   * @return string
   *   The no script warning alert.
   */
  final protected function getAlertNoScript() {
    return "<noscript>{$this->getAlert(
      $this->intl->t("JavaScript Disabled"),
      $this->intl->t("Please activate JavaScript in your browser to experience our website with all its features."),
      "polite",
      "warning",
      "log"
    )}</noscript>";
  }

  /**
   * Add alert to presentation.
   *
   * @param string $title
   *   The alert's translated title.
   * @param string $message [optional]
   *   The alert's translated message.
   * @return this
   */
  final public function alert($title, $message = null) {
    $this->alerts .= $this->getAlert($title, $message, "polite", null, "log");
    return $this;
  }

  /**
   * Add error alert to presentation.
   *
   * @param string $title
   *   The alert's translated title.
   * @param string $message [optional]
   *   The alert's translated message.
   * @return this
   */
  final public function alertError($title, $message = null) {
    $this->alerts .= $this->getAlert($title, $message, "assertive", "error", "alert");
    return $this;
  }

  /**
   * Add info alert to presentation.
   *
   * @param string $title
   *   The alert's translated title.
   * @param string $message [optional]
   *   The alert's translated message.
   * @return this
   */
  final public function alertInfo($title, $message = null) {
    $this->alerts .= $this->getAlert($title, $message, "polite", "info", "status");
    return $this;
  }

  /**
   * Add success alert to presentation.
   *
   * @param string $title
   *   The alert's translated title.
   * @param string $message [optional]
   *   The alert's translated message.
   * @return this
   */
  final public function alertSuccess($title, $message = null) {
    $this->alerts .= $this->getAlert($title, $message, "polite", "success", "status");
    return $this;
  }

  /**
   * Add warning alert to presentation.
   *
   * @param string $title
   *   The alert's translated title.
   * @param string $message [optional]
   *   The alert's translated message.
   * @return this
   */
  final public function alertWarning($title, $message = null) {
    $this->alerts .= $this->getAlert($title, $message, "assertive", "warning", "alert");
    return $this;
  }


  // ------------------------------------------------------------------------------------------------------------------- Layout Methods


  /**
   * Get the reference footer.
   *
   * @return string
   *   The reference footer.
   */
  public function getFooter() {
    $languageLinks = null;
    $teamOffset    = " o4";

    if ($this->languageLinks) {
      $teamOffset = null;
      list($routeKey, $args, $plural, $queries) = $this->languageLinks;
      $languages = $this->intl->getTranslations("languages");
      // @devStart
      // @codeCoverageIgnoreStart
      if (empty($languages)) {
        throw new \LogicException("Language translations are empty, please execute `movinstall seed-languages`.");
      }
      // @codeCoverageIgnoreEnd
      // @devEnd

      // Format the current language's link right away.
      $languageLinks[$languages[$this->intl->languageCode]->name] =
        "<a class='active' href='#' title='{$this->intl->t("You’re currently viewing this page.")}'>" .
          $languages[$this->intl->languageCode]->name .
        "</a>"
      ;

      // Remove the current language from the available locales.
      $locales = $this->intl->systemLocales;
      unset($locales[$this->intl->languageCode]);

      // Translate the rest of the available languages.
      foreach ($locales as $code => $locale) {
        $route = $this->intl->r($routeKey, $args, $locale);
        if ($queries) {
          array_walk($queries, function (&$value, $key) {
            $value = rawurlencode($this->intl->r($key)) . "=" . rawurlencode($value);
          });
          $route .= "?" . implode("&amp;", $queries);
        }
        $languageLinks[$languages[$code]->name] =
          "<a href='//{$code}.{$this->config->hostname}{$route}' lang='{$code}'>{$this->intl->t(
            "{0} ({1})",
            [ $languages[$code]->name, $languages[$code]->native ]
          )}</a>"
        ;
      }

      // The language links shall be sorted in the client's native language.
      (new Collator($this->intl->locale))->ksort($languageLinks);

      // Put the section together.
      $languageLinks =
        "<section class='last s s4'>" .
          "<div class='popup'>" .
            "<div class='content'>" .
              "<h2>{$this->intl->t("Choose your language")}</h2>" .
              "<small>{$this->intl->t(
                "Is your language missing in our list? {0}Help us translate {sitename}.{1}",
                [ "<a href='{$this->intl->r("/localize")}'>", "</a>", "sitename" => $this->config->sitename ]
              )}</small>" .
              implode(" ", $languageLinks) .
            "</div>" .
            "<a class='ico ico-languages' id='f-language' tabindex='0'>{$this->intl->t("Language")}: {$languages[$this->intl->languageCode]->name}</a>" .
          "</div>" .
        "</section>"
      ;
    }

    return
      "<footer id='f' role='contentinfo'>" .
        "<h1 class='vh'>{$this->intl->t("Infos all around {sitename}", [ "sitename" => $this->config->sitename ])}</h1>" .
        "<div class='c'><div class='r'>" .
          "<section class='s s12'>" .
            "<h3 class='vh'>{$this->intl->t("Copyright and licensing information")}</h3>" .
            "<p id='f-copyright'><span class='ico ico-cc'></span> <span class='ico ico-cc-zero'></span> {$this->intl->t(
              "Database data is available under the {0}Creative Commons — CC0 1.0 Universal{1} license.",
              [ "<a href='https://creativecommons.org/publicdomain/zero/1.0/deed.{$this->intl->languageCode}' rel='license'>", "</a>" ]
            )}<br>{$this->intl->t(
              "Additional terms may apply for third-party content, please refer to any license or copyright information that is additionaly stated."
            )}</p>" .
          "</section>" .
          "<section id='f-logos' class='s s12 tac'>" .
            "<h3 class='vh'>{$this->intl->t("Sponsors and external resources")}</h3>" .
            "<a class='no-link' href='http://www.fh-salzburg.ac.at/' target='_blank'>" .
              "<img alt='Fachhochschule Salzburg' height='30' src='{$this->fs->getExternalURL("asset://img/footer/fachhochschule-salzburg.svg")}' width='48'>" .
            "</a>" .
            "<a class='no-link' href='https://github.com/MovLib' target='_blank'>" .
              "<img alt='GitHub' height='30' src='{$this->fs->getExternalURL("asset://img/footer/github.svg")}' width='48'>" .
            "</a>" .
          "</section>" .
          $languageLinks .
          "<section id='f-team' class='last{$teamOffset} s s4 tac'><h3>{$this->a($this->intl->r("/team"), $this->intl->t("Made with {love} in Austria", [
            "love" => "<span class='ico ico-heart'></span><span class='vh'>{$this->intl->t("love")}</span>"
          ]))}</h3></section>" .
          "<section class='last s s4 tar'>" .
            "<h3 class='vh'>{$this->intl->t("Legal Links")}</h3>" .
            "{$this->a($this->intl->r("/impressum"), $this->intl->t("Impressum"))} · " .
            "{$this->a($this->intl->r("/privacy-policy"), $this->intl->t("Privacy Policy"))} · " .
            "{$this->a($this->intl->r("/terms-of-use"), $this->intl->t("Terms of Use"))}" .
          "</section>" .
          // @devStart
          // @codeCoverageIgnoreStart
          "<section class='last s s12 tac'>" .
            "<h3 class='vh'>{$this->intl->t("Dev Links")}</h3>" .
            "<a href='http://www.google.com/webmasters/tools/richsnippets?q={$this->request->scheme}://{$this->request->hostname}{$this->request->uri}'>Rich Snippets</a> · " .
            "<a href='http://www.w3.org/2012/pyRdfa/extract?validate=yes&amp;uri={$this->request->scheme}://{$this->request->hostname}{$this->request->uri}'>RDFa</a> · " .
            "<a href='http://validator.w3.org/check?uri={$this->request->scheme}://{$this->request->hostname}{$this->request->uri}'>Validator</a> · " .
            "<a href='http://gsnedders.html5.org/outliner/process.py?url={$this->request->scheme}://{$this->request->hostname}{$this->request->uri}'>Outliner</a>" .
          "</section>" .
          // @codeCoverageIgnoreEnd
          // @devEnd
        "</div>" .
      "</div></footer>"
    ;
  }

  /**
   * Get the reference header, including logo, navigations and search form.
   *
   * @return string
   *   The reference header.
   */
  public function getHeader() {
    $exploreNavigation =
      "<ul class='o1 sm2 no-list'>" .
        "<li>{$this->a($this->intl->r("/movies"), $this->intl->t("Movies"), [ "class" => "ico ico-movie" ])}</li>" .
        "<li>{$this->a($this->intl->r("/series"), $this->intl->tp(-1, "Series"), [ "class" => "ico ico-series" ])}</li>" .
        "<li>{$this->a($this->intl->r("/releases"), $this->intl->t("Releases"), [ "class" => "ico ico-release" ])}</li>" .
        "<li>{$this->a($this->intl->r("/persons"), $this->intl->t("Persons"), [ "class" => "ico ico-person" ])}</li>" .
        "<li>{$this->a($this->intl->r("/companies"), $this->intl->t("Companies"), [ "class" => "ico ico-company" ])}</li>" .
        "<li>{$this->a($this->intl->r("/awards"), $this->intl->t("Awards"), [ "class" => "ico ico-award" ])}</li>" .
        "<li>{$this->a($this->intl->r("/events"), $this->intl->t("Events"), [ "class" => "ico ico-event" ])}</li>" .
        "<li>{$this->a($this->intl->r("/genres"), $this->intl->t("Genres"), [ "class" => "ico ico-genre" ])}</li>" .
        "<li>{$this->a($this->intl->r("/jobs"), $this->intl->t("Jobs"), [ "class" => "ico ico-job" ])}</li>" .
        "<li class='separator'>{$this->a($this->intl->r("/help"), $this->intl->t("Help"), [ "class" => "ico ico-help" ])}</li>" .
      "</ul>"
    ;

    $marketplaceNavigation =
      "<ul class='o1 sm2 no-list'>" .
        "<li>{$this->checkBackLater("Marketplace")}</li>" .
      "</ul>"
    ;

    $communityNavigation =
      "<ul class='o1 sm2 no-list'>" .
        "<li>{$this->a($this->intl->r("/users"), $this->intl->t("Users"), [ "class" => "ico ico-person" ])}</li>" .
        "<li class='separator'>{$this->a($this->intl->r("/deletion-requests"), $this->intl->t("Deletion Requests"), [ "class" => "ico ico-delete" ])}</li>" .
      "</ul>"
    ;

    if ($this->session->isAuthenticated === true) {
      $avatar   = $this->img($this->session->imageGetStyle(), [], false);
      $userIcon = "<div class='clicker ico ico-settings authenticated'>{$avatar}<span class='badge'>2</span></div>";
      $userNavigation =
        "<ul class='o1 sm2 no-list'>" .
          "<li>{$this->a($this->intl->r("/profile/messages"), $this->intl->t("Messages"), [ "class" => "ico ico-email" ])}</li>" .
          "<li>{$this->a($this->intl->r("/profile/collection"), $this->intl->t("Collection"), [ "class" => "ico ico-release" ])}</li>" .
          "<li>{$this->a($this->intl->r("/profile/wantlist"), $this->intl->t("Wantlist"), [ "class" => "ico ico-heart" ])}</li>" .
          "<li>{$this->a($this->intl->r("/profile/lists"), $this->intl->t("Lists"), [ "class" => "ico ico-ul" ])}</li>" .
          "<li>{$this->a($this->intl->r("/profile/watchlist"), $this->intl->t("Watchlist"), [ "class" => "ico ico-view" ])}</li>" .
          "<li class='separator'>{$this->a($this->intl->r("/profile"), $this->intl->t("Profile"), [ "class" => "ico ico-user" ])}</li>" .
          "<li>{$this->a($this->intl->r("/profile/account-settings"), $this->intl->t("Settings"), [ "class" => "ico ico-settings" ])}</li>" .
          "<li class='separator name'>{$this->session->userName}</li>" .
          "<li>{$this->a($this->intl->r("/profile/sign-out"), $this->intl->t("Sign Out"), [ "class" => "danger" ])}</li>" .
        "</ul>" .
        "<a class='no-link' href='{$this->intl->r("/profile")}'>{$avatar}</a>";
    }
    else {
      $userIcon = "<div class='btn btn-inverse clicker ico ico-user-add'></div>";
      $userNavigation =
        "<ul class='o1 sm2 no-list'>" .
          "<li>{$this->a($this->intl->r("/profile/sign-in"), $this->intl->t("Sign In"))}</li>" .
          "<li>{$this->a($this->intl->r("/profile/join"), $this->intl->t("Join"))}</li>" .
          "<li>{$this->a($this->intl->r("/profile/reset-password"), $this->intl->t("Forgot Password"))}</li>" .
        "</ul>"
      ;
    }

    $searchQuery = $this->request->filterInput(INPUT_GET, "q", FILTER_SANITIZE_STRING, FILTER_REQUIRE_SCALAR | FILTER_FLAG_STRIP_LOW);

    return
      // No skip-to-content link! We have proper headings, semantic HTML5 elements and proper ARIA landmarks!
      "<header id='h' role='banner'><div class='c'><div class='r'>" .
        // Only one <h1> per page? No problem according to Google https://www.youtube.com/watch?v=GIn5qJKU8VM plus HTML5
        // wants us to use multiple <h1>s for multiple sections, so here we go. The header is always the MovLib header.
        "<h1 class='s s3'>{$this->a(
          "/",
          "<img alt='' height='42' src='{$this->fs->getExternalURL("asset://img/logo/vector.svg")}' width='42'> {$this->config->sitename}",
          [ "id" => "l", "title" => $this->intl->t("Go back to the home page.") ]
        )}</h1>" .
        "<div class='s s9'>" .
          "<nav aria-expanded='false' aria-haspopup='true' class='expander main-nav' id='explore-nav' role='navigation' tabindex='0'>" .
            "<h2 class='visible clicker'>{$this->intl->t("Explore")}</h2>" .
            "<div class='concealed s sm3'>" .
              $exploreNavigation .
            "</div>" .
          "</nav>" .
          "<nav aria-expanded='false' aria-haspopup='true' class='expander main-nav' id='marketplace-nav' role='navigation' tabindex='0'>" .
            "<h2 class='visible clicker'>{$this->intl->t("Marketplace")}</h2>" .
            "<div class='concealed s sm3'>" .
              $marketplaceNavigation .
            "</div>" .
          "</nav>" .
          "<nav aria-expanded='false' aria-haspopup='true' class='expander main-nav' id='community-nav' role='navigation' tabindex='0'>" .
            "<h2 class='visible clicker'>{$this->intl->t("Community")}</h2>" .
            "<div class='concealed s sm3'>" .
              $communityNavigation .
            "</div>" .
          "</nav>" .
          "<form action='{$this->intl->r("/search")}' class='s' id='s' method='get' role='search'>" .
            "<input name='i' type='hidden' value='movies-series-releases-persons'>" .
            "<button class='ico ico-search' tabindex='2' type='submit'><span class='vh'>{$this->intl->t(
              "Start searching for the entered keyword."
            )}</span></button>" .
            "<input name='q' required tabindex='1' title='{$this->intl->t(
              "Enter the search term you wish to search for and hit enter."
            )}' type='search' value='{$searchQuery}'>" .
          "</form>" .
          "<nav aria-expanded='false' aria-haspopup='true' class='expander main-nav' id='user-nav' role='navigation' tabindex='0'>" .
            "<h2 class='vh'>{$this->intl->t("User Navigation")}</h2>{$userIcon}" .
            "<div class='concealed s sm3'>{$userNavigation}</div>" .
          "</nav>" .
        "</div>" .
      "</div></div></header>"
    ;
  }

  /**
   * Get the head title.
   *
   * Formats the title of this page for the <code><title></code>-element. A special separator string is used before
   * appending the sitename.
   *
   * @return string
   *   The head title.
   */
  protected function getHeadTitle() {
    return $this->intl->t("{page_title} — {sitename}", [ "page_title" => $this->title, "sitename" => $this->config->sitename ]);
  }

  /**
   * Get string representation of the current page.
   *
   * Any HTML page needs the HTML header and the wrapping <code><body></code>-element. Therefor the most basic variation
   * is to only print exactly these elements.
   *
   * Short note on why we are using a method called <code>getPresentation()</code> and not the platform provided
   * <code>getPresentation()</code> magic method: any <code>getPresentation()</code>-method can't throw an execption, which is a
   * huge problem in the way we are dealing with errors. Everything throws an exception if something goes wrong and if
   * something goes wrong during the rendering process one wouldn't get any stacktrace, instead a generic
   * <i>getPresentation() must not throw an exception</i> message would be displayed (fatal error of course, so you get
   * nothing).
   *
   * @param string $content
   *   The presentation's content, usually from {@see AbstractPresenter::getContent()} but exception may call other
   *   methods to provide the content.
   * @return string
   */
  public function getPresentation($content) {
    // Allow the presentation to alter presentation in getContent() method.
    $content = $this->getMainContent($content);

    // Build a link for each stylesheet of this page.
    $stylesheets = null;
    $i = count($this->stylesheets);
    while ($i--) {
      $stylesheets .= "<link href='{$this->fs->getExternalURL("asset://css/module/{$this->stylesheets[$i]}.css")}' rel='stylesheet'>";
    }

    // Apply additional CSS class if the current request is made from a signed in user.
    if ($this->session->isAuthenticated === true) {
      $this->bodyClasses .= " authenticated";
    }

    // Build the JavaScript settings JSON.
    $this->javascriptSettings["hostnameStatic"] = $this->config->hostnameStatic;
    $c = count($this->javascripts);
    for ($i = 0; $i < $c; ++$i) {
      $this->javascriptSettings["modules"][$this->javascripts[$i]] = $this->fs->getExternalURL("asset://js/module/{$this->javascripts[$i]}.js");
    }
    $jsSettings = json_encode($this->javascriptSettings, JSON_UNESCAPED_UNICODE);

    $htmlAttr = " dir='{$this->intl->direction}' id='nojs' lang='{$this->intl->languageCode}' prefix='og: http://ogp.me/ns#'";
    $logo256  = $this->fs->getExternalURL("asset://img/logo/256.png");
    $title    = $this->getHeadTitle();

    return
      "<!doctype html>" .
      "<!--[if IE 9 ]><html class='ie9'{$htmlAttr}><![endif]-->" .
      "<!--[if (gt IE 9)|!(IE)]><!--><html{$htmlAttr}><!--<![endif]-->" .
      "<head>" .
        "<title>{$title}</title>" .
        // Include the global styles and any presentation specific ones.
        "<link href='{$this->fs->getExternalURL("asset://css/MovLib.css")}' rel='stylesheet'>{$stylesheets}" .
        // Yes, we could create these in a loop, but why should we implement a loop for static data? To be honest, I
        // generated it with a loop and simply copied the output here.
        "<link href='{$this->fs->getExternalURL("asset://img/logo/vector.svg")}' rel='icon' type='image/svg+xml'>" .
        "<link href='{$logo256}' rel='icon' sizes='256x256' type='image/png'>" .
        "<link href='{$this->fs->getExternalURL("asset://img/logo/128.png")}' rel='icon' sizes='128x128' type='image/png'>" .
        "<link href='{$this->fs->getExternalURL("asset://img/logo/64.png")}' rel='icon' sizes='64x64' type='image/png'>" .
        "<link href='{$this->fs->getExternalURL("asset://img/logo/32.png")}' rel='icon' sizes='32x32' type='image/png'>" .
        "<link href='{$this->fs->getExternalURL("asset://img/logo/24.png")}' rel='icon' sizes='24x24' type='image/png'>" .
        "<link href='{$this->fs->getExternalURL("asset://img/logo/16.png")}' rel='icon' sizes='16x16' type='image/png'>" .
        "<link href='https://plus.google.com/115387876584819891316?rel=publisher' property='publisher'>" .
        "<meta property='og:description' content='{$this->intl->t("The free online movie database that anyone can edit.")}'>" .
        "<meta property='og:image' content='{$this->request->scheme}:{$logo256}'>" .
        "<meta property='og:site_name' content='{$this->config->sitename}'>" .
        "<meta property='og:title' content='{$title}'>" .
        "<meta property='og:type' content='website'>" .
        "<meta property='og:url' content='{$this->request->scheme}://{$this->config->hostname}{$this->request->uri}'>" .
        "<meta name='application-name' content='{$this->config->sitename}'>" .
        "<meta name='msapplication-tooltip' content='{$this->intl->t("The free movie library.")}'>" .
        "<meta name='msapplication-starturl' content='{$this->request->scheme}://{$this->config->hostname}/'>" .
        "<meta name='msapplication-navbutton-color' content='#ffffff'>" .
        // @todo Add opensearch tag (rel="search").
        $this->headElements .
      "</head>" .
      "<body id='{$this->id}' class='{$this->bodyClasses}' vocab='http://schema.org/'>" .
        "{$this->getHeader()}{$content}{$this->getFooter()}" .
        "<script id='jss' type='application/json'>{$jsSettings}</script>" .
        "<script async src='{$this->fs->getExternalURL("asset://js/MovLib.js")}'></script>"
    ;
  }

  /**
   * Get the wrapped content, including heading.
   *
   * @param string $content
   *   The presentation's content.
   * @return string
   *   The presentation's content wrapped with the main tag and header.
   */
  public function getMainContent($content) {
    // The schema for the complete page content.
    $schema = null;
    if ($this->schemaType) {
      $schema = " typeof='{$this->schemaType}'";
    }

    $this->response->setAlerts($this);

    // Render the page's main element (note that we still include the ARIA role "main" at this point because not all
    // user agents support the new HTML5 element yet).
    return
      "<main id='m' role='main'{$schema}>" .
        "<header id='header'>" .
          "<div class='c'>{$this->breadcrumb}{$this->getMainHeading()}</div>" .
          "{$this->getAlertNoScript()}{$this->alerts}" .
        "</header>" .
        "{$this->contentBefore}{$content}{$this->contentAfter}" .
      "</main>"
    ;
  }

  /**
   * Get the presentation's main <code><header></code> content.
   *
   * @todo Concrete classes should overwrite this method and implement their own special heading instead of setting
   *       some properties that have to be known upfront.
   * @return string
   *   The presentation's main <code><header></code> content.
   */
  protected function getMainHeading() {
    // Allow presenter's to set a title with HTML.
    $title = $this->pageTitle ?: $this->title;

    // The schema property of the heading.
    $headingprop = null;
    if ($this->headingSchemaProperty) {
      $headingprop = " property='{$this->headingSchemaProperty}'";
    }

    return "{$this->headingBefore}<h1{$headingprop}>{$title}</h1>{$this->headingAfter}";
  }

  /**
   * Add next route to <code><head></code>.
   *
   * @param string $route
   *   The route that's next.
   * @return this
   */
  final protected function next($route) {
    $this->headElements .= "<link rel='next' href='{$route}'>";
    return $this;
  }

  /**
   * Add prefetch/prerender route to <code><head></code>.
   *
   * @param string $route
   *   The route to prefetch/prerender.
   * @return this
   */
  final protected function prefetch($route) {
    $this->headElements .= "<link rel='prefetch' href='{$route}'><link rel='prerender' href='{$route}'>";
    return $this;
  }

  /**
   * Add previous route to <code><head></code>.
   *
   * @param string $route
   *   The route that's previous.
   * @return this
   */
  final protected function prev($route) {
    $this->headElements .= "<link rel='prev' href='{$route}'>";
    return $this;
  }

}

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

/**
 * Abstract base class for all presentation classes.
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractPage extends \MovLib\Presentation\AbstractBase {


  // ------------------------------------------------------------------------------------------------------------------- Properties


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
   * @see \MovLib\Presentation\AbstractPage::init()
   * @var string
   */
  public $id;

  /**
   * Contains the namespace parts as array.
   *
   * @var array
   */
  protected $namespace;

  /**
   * Contains the CSS classes of the body element.
   *
   * @var array
   */
  protected $bodyClasses;

  /**
   * The page's title used in the header.
   *
   * @var string
   */
  protected $pageTitle;

  /**
   * Numeric array containing all stylesheets within the <code>assets/css/layout</code> path on the server.
   *
   * These stylesheets will be delivered with any request for any page, they represent the most basic styles for our
   * complete website and we want them to be fetched as early as possible, so that the user's browser can cache them
   * till the end of time (or we generate a new one).
   *
   * @var array
   */
  protected $stylesheets = [
    "base.css",
    "layout/grid.css",
    "layout/typography.css",
    "layout/forms.css",
    "layout/generic.css",
    "layout/header.css",
    "layout/content.css",
    "layout/secondary-navigation.css",
    "layout/footer.css",
    "layout/icons.css",
    "layout/alert.css",
    "layout/buttons.css",
  ];

  /**
   * The page's title.
   *
   * @var string
   */
  protected $title;


  // ------------------------------------------------------------------------------------------------------------------- Protected Methods


  /**
   * Get the head title.
   *
   * Formats the title of this page for the <code><title></code>-element. A special separator string is used before
   * appending the sitename.
   *
   * @global \MovLib\Data\I18n $i18n
   * @return string
   *   The head title.
   */
  protected function getHeadTitle() {
    global $i18n;
    return "{$this->title}{$i18n->t(" — ", null, [ "comment" =>
      "The em dash is used as separator character in the header title to denote the source of the document (like in " .
      "a quote the author), this should be translated to the equivalent character in your language. More information " .
      "on this specific character can be found at <a href='//en.wikipedia.org/wiki/Dash#Em_dash'>Wikipedia</a>."
    ])}MovLib";
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
   * @global \MovLib\Kernel $kernel
   * @global \MovLib\Data\I18n $i18n
   * @global \MovLib\Data\User\Session $session
   * @return string
   */
  public function getPresentation() {
    global $kernel, $i18n, $session;

    // Build a link for each stylesheet of this page.
    $stylesheets = "";
    $c = count($this->stylesheets);
    for ($i = 0; $i < $c; ++$i) {
      $stylesheets .= "<link rel='stylesheet' href='//{$kernel->domainStatic}/asset/css/{$this->stylesheets[$i]}'>";
    }

    // Apply additional CSS class if the current request is made from a signed in user.
    if ($session->isAuthenticated === true) {
      $this->bodyClasses .= " authenticated";
    }

    return
      "<!doctype html>" .
      "<html dir='{$i18n->direction}' id='nojs' lang='{$i18n->languageCode}'>" .
        "<head>" .
          // @todo RFC: META-Charset
          //
          // The meta-charset is only needed if a document is not sending appropriate HTTP headers. So for instance if
          // someone saves a page to disc. The question is, do we really need support for such situations? Older (not
          // supported) browsers like IE8 have problems with more than one charset declaration (experimental page speed
          // rule says so) and it is simply redundant in the web context. Our HTTP header already told the browser that
          // this page is completely in UTF-8 (same is true for any other text based content our server is going to
          // deliver). The bytes we save here are of course irrevelant, it's the redundancy that bugs me.
          //
          //"<meta charset='utf-8'>" .
          "<title>{$this->getHeadTitle()}</title>" .
          $stylesheets .
          // Yes, we could create these in a loop, but why should we implement a loop for static data? Do be honest, I
          // generated it with a loop and simply copied the output here.
          "<link rel='icon' type='image/svg+xml' href='//{$kernel->domainStatic}/asset/img/logo/vector.svg'>" .
          "<link rel='icon' type='image/png' sizes='256x256' href='//{$kernel->domainStatic}/asset/img/logo/256.png'>" .
          "<link rel='icon' type='image/png' sizes='128x128' href='//{$kernel->domainStatic}/asset/img/logo/128.png'>" .
          "<link rel='icon' type='image/png' sizes='64x64' href='//{$kernel->domainStatic}/asset/img/logo/64.png'>" .
          "<link rel='icon' type='image/png' sizes='32x32' href='//{$kernel->domainStatic}/asset/img/logo/32.png'>" .
          "<link rel='icon' type='image/png' sizes='24x24' href='//{$kernel->domainStatic}/asset/img/logo/24.png'>" .
          "<link rel='icon' type='image/png' sizes='16x16' href='//{$kernel->domainStatic}/asset/img/logo/16.png'>" .
          // @todo Add opensearch tag (rel="search").
          "<meta name='viewport' content='width=device-width,initial-scale=1.0'>" .
        "</head>" .
        // @todo Drop the {$this->id}-body class!
        "<body id='{$this->id}' class='{$this->bodyClasses}'>"
      // Please note that there is no need to include the closing body- nor html-tag with the HTML5 doc-type! We abuse
      // this for our inheritance and other classes can overwrite and extend the most default getPresentation() method
      // while retaining the global header.
    ;
  }

  /**
   * Initialize this presentation.
   *
   * @param string $title
   *   The already translated title of this page.
   * @return this
   */
  protected function init($title) {
    // The substr() removes the \MovLib\Presentation\ part!
    $className         = strtolower(substr(get_class($this), 20));
    $this->namespace   = explode("\\", $className);
    $this->bodyClasses = strtr($className, "\\", " ");
    $this->id          = strtr($className, "\\", "-");
    $this->title       = $title;
    return $this;
  }

}

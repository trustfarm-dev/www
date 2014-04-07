<?php

/* !
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
namespace MovLib\Core\Presentation;

/**
 * @todo Description of DependencyInjectionBase
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class DependencyInjectionBase extends \MovLib\Core\Presentation\Base {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * Active global config instance.
   *
   * @var \MovLib\Core\Config
   */
  protected $config;

  /**
   * The active dependency injection container.
   *
   * @var \MovLib\Core\HTTP\DIContainerHTTP
   */
  protected $diContainerHTTP;

  /**
   * Active file system instance.
   *
   * @var \MovLib\Core\FileSystem
   */
  protected $fs;

  /**
   * Active intl instance.
   *
   * @var \MovLib\Core\Intl
   */
  protected $intl;

  /**
   * Active kernel instance.
   *
   * @var \MovLib\Core\Kernel
   */
  protected $kernel;

  /**
   * The active log instance.
   *
   * @var \MovLib\Core\Log
   */
  protected $log;

  /**
   * Active HTTP session instance.
   *
   * @var \MovLib\Core\HTTP\Session
   */
  protected $session;

  /**
   * Active HTTP request instance.
   *
   * @var \MovLib\Core\HTTP\Request
   */
  protected $request;

  /**
   * Active HTTP response instance.
   *
   * @var \MovLib\Core\HTTP\Response
   */
  protected $response;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new presentation object.
   *
   * @param \MovLib\Core\HTTP\DIContainerHTTP $diContainerHTTP
   *   HTTP dependency injection container.
   */
  public function __construct(\MovLib\Core\HTTP\DIContainerHTTP $diContainerHTTP) {
    $this->diContainerHTTP = $diContainerHTTP;
    $this->config          = $diContainerHTTP->config;
    $this->fs              = $diContainerHTTP->fs;
    $this->intl            = $diContainerHTTP->intl;
    $this->kernel          = $diContainerHTTP->kernel;
    $this->log             = $diContainerHTTP->log;
    $this->request         = $diContainerHTTP->request;
    $this->response        = $diContainerHTTP->response;
    $this->session         = $diContainerHTTP->session;
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * Generate an internal link.
   *
   * This method should be used if you link to a page, but can't predict or know if this might be the page the user is
   * currently viewing. We don't want any links within a document to itself, but there are various reasons why you might
   * need that. Please use common sense. In general you should simply create the anchor element instead of calling this
   * method.
   *
   * @link http://www.w3.org/TR/html5/text-level-semantics.html#the-a-element
   * @link http://www.nngroup.com/articles/avoid-within-page-links/ Avoid Within-Page Links
   * @param string $route
   *   The original English route.
   * @param string $text
   *   The translated text that should appear as link on the page.
   * @param array $attributes [optional]
   *   Additional attributes that should be applied to the link element.
   * @param boolean $ignoreQuery [optional]
   *   Whether to ignore the query string while checking if the link should be marked active or not. Default is to
   *   ignore the query string.
   * @return string
   *   The internal link ready for print.
   */
  final public function a($route, $text, array $attributes = null, $ignoreQuery = true) {
    // We don't want any links to the current page (as per W3C recommendation). We also have to ensure that the anchors
    // aren't tabbed to, therefor we completely remove the href attribute. While we're at it we also remove the title
    // attribute because it doesn't add any value for screen readers without any target (plus the user is actually on
    // this very page).
    if ($route == $this->request->uri) {
      // Remove all attributes which aren't allowed on an anchor with empty href attribute.
      $unset = [ "download", "href", "hreflang", "rel", "target", "type" ];
      for ($i = 0; $i < 6; ++$i) {
        if (isset($attributes[$unset[$i]])) {
          unset($attributes[$unset[$i]]);
        }
      }
      // Ensure that this anchor is still "tabable".
      $attributes["tabindex"] = "0";
      $attributes["title"]    = $this->intl->t("You’re currently viewing this page.");
      $this->addClass("active", $attributes);
    }
    else {
      // We also have to mark the current anchor as active if the caller requested that we ignore the query part of the
      // URI (default behaviour of this method). We keep the title attribute in this case as it's a clickable link.
      if ($ignoreQuery === true && $route == $this->request->path) {
        $this->addClass("active", $attributes);
      }

      // Add the route to the anchor element.
      $attributes["href"] = $route{0} == "#" ? $route : $this->fs->urlEncodePath($route);
    }

    // Put it all together.
    return "<a{$this->expandTagAttributes($attributes)}>{$text}</a>";
  }

  /**
   * Format the given weblinks.
   *
   * @param array $weblinks
   *   The weblinks to format.
   * @return null|string
   *   The formatted weblinks, <code>NULL</code> if there are no weblinks to format.
   */
  final public function formatWeblinks(array $weblinks) {
    if (empty($weblinks)) {
      return;
    }
    $formatted = null;
    $c = count($weblinks);
    for ($i = 0; $i < $c; ++$i) {
      if ($formatted) {
        $formatted .= trim($this->intl->t("{0}, {1}"), "{}01");
      }
      $weblink = str_replace("www.", "", parse_url($weblinks[$i], PHP_URL_HOST));
      $formatted .= "<a href='{$weblinks[$i]}' target='_blank'>{$weblink}</a>";
    }
    return $formatted;
  }

  /**
   * Get global <code>lang</code> attribute for any HTML tag if language differs from current display language.
   *
   * @param string $lang
   *   The ISO alpha-2 language code of the entity you want to display and have compared to the current language.
   * @return null|string
   *   <code>NULL</code> if given <var>$lang</var> matches current display language, otherwise the global <code>lang</code>
   *   attribute ready for print (e.g. <code>" lang='de'"</code>).
   */
  final public function lang($lang) {
    if ($lang != $this->intl->languageCode) {
      return " lang='{$this->htmlEncode($lang)}'";
    }
  }

}

<?php

/*!
 *  This file is part of {@link https://github.com/MovLib MovLib}.
 *
 *  Copyright © 2013-present {@link http://movlib.org/ MovLib}.
 *
 *  MovLib is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public
 *  License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 *  version.
 *
 *  MovLib is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 *  of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License along with MovLib.
 *  If not, see {@link http://www.gnu.org/licenses/ gnu.org/licenses}.
 */

namespace MovLib\Presentation\Person\Photo;

use \MovLib\Data\Image\PersonImage;

/**
 * Image details presentation for a person's photo.
 *
 * @author Markus Deutschl <mdeutschl.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Photo extends \MovLib\Presentation\Person\Photo\AbstractBase {

  /**
   * Instantiate new Person Photo presentation.
   *
   * @global \MovLib\Kernel $kernel
   * @global \MovLib\Data\I18n $i18n
   * @throws \MovLib\Exception\DatabaseException
   * @throws \MovLib\Presentation\Redirect\SeeOther
   */
  public function __construct() {
    global $i18n, $kernel;

    // Try to load person data.
    $this->person = new Person($_SERVER["PERSON_ID"]);

    $routeArgs = [ $this->person->id ];

    // Redirect to upload page, if there is no photo.
    if ($this->person->displayPhoto->imageExists === false) {
      throw new SeeOther($this->person->displayPhoto->route);
    }

    // Initialize breadcrumbs.
    $this->initBreadcrumb([
      [ $i18n->rp("/persons"), $i18n->t("Persons") ],
      [ $this->person->route, $this->person->name ],
    ]);

    // Initialize sidebar navigation.
    $this->sidebarInit([
        [ $i18n->r("/person/{0}/photo", $routeArgs), $i18n->t("View"), [ "class" => "ico ico-view" ] ],
        [ $i18n->r("/person/{0}/photo/edit", $routeArgs), $i18n->t("Edit"), [ "class" => "ico ico-edit" ] ],
        [ $i18n->r("/person/{0}/photo/history", $routeArgs), $i18n->t("History"), [ "class" => "ico ico-history" ] ],
        [ $i18n->r("/person/{0}/photo/delete", $routeArgs), $i18n->t("Delete"), [ "class" => "ico ico-delete" ] ],
    ]);

    // Initialize language links.
    $this->initLanguageLinks("/person/{0}/photo", $routeArgs);

    // Initialize page titles.
    $title = $i18n->t("Photo of {person_name}");
    $search = "{person_name}";
    $this->initPage(str_replace($search, $this->person->name, $title));
    $this->pageTitle = str_replace(
      $search,
      "<span itemscope itemtype='http://schema.org/Person'><a href='{$this->person->route}' itemprop='url'><span itemprop='name'>{$this->person->name}</span></a></span>",
      $title
    );

    // Initialize CSS class, schema and stylesheet.
    $this->bodyClasses    .= " imagedetails";
    $this->schemaType      = "ImageObject";
    $kernel->stylesheets[] = "imagedetails";
  }

  /**
   * @inheritdoc
   */
  protected function getPageContent() {
    global $i18n;
    // @todo: No photo -> display upload link and no sidebar.
    return
    "<meta itemprop='representativeOfPage' content='true'>" .
        TraitDeletionRequest::getDeletionRequestedAlert($this->image->deletionId) .
        "<div class='r wrapper'>" .
          "<div class='s s8 tac image'>{$this->getImage(
            $$this->person->displayPhoto->getStyle(PersonImage::STYLE_SPAN_02),
            $this->image->getURL(),
            [ "itemprop" => "thumbnailUrl" ],
            [ "itemprop" => "contentUrl", "target" => "_blank" ]
          )}</div>" .
          "<dl class='s s4 description'>" .
            $dl .
            "<dt>{$i18n->t("Provided by")}</dt><dd><a href='{$uploader->route}' itemprop='creator provider'>{$uploader->name}</a></dd>" .
            "<dt>{$i18n->t("Dimensions")}</dt><dd>{$i18n->t("{width} × {height}", [
              "width"  => "<span itemprop='width'>{$this->image->width}&nbsp;<abbr title='{$i18n->t("Pixel")}'>px</abbr></span>",
              "height" => "<span itemprop='height'>{$this->image->height}&nbsp;<abbr title='{$i18n->t("Pixel")}'>px</abbr></span>",
            ])}</dd>" .
            "<dt>{$i18n->t("File size")}</dt><dd itemprop='contentSize'>{$i18n->t("{0,number} {1}", $this->formatBytes($this->image->filesize))}</dd>" .
            "<dt>{$i18n->t("Upload on")}</dt><dd>{$dateTime}</dd>" .
            "<dt>{$i18n->t("Buy this {image_name}", [ "image_name" => $this->image->name ])}</dt>{$offers}" .
          "</dl>" .
        "</div>" .
      "</div>"
    ;
  }
}

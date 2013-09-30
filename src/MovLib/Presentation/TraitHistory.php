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
namespace MovLib\Presentation;

use \IntlDateFormatter;
use \MovLib\Data\Users;
use \MovLib\Presentation\Partial\Lists;

/**
 * Description of AbstractHistory
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
trait TraitHistory {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The history model to display.
   *
   * @var \MovLib\Data\History\AbstractHistory
   */
  protected $historyModel;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  // ------------------------------------------------------------------------------------------------------------------- Methods

  /**
   * @inheritdoc
   */
  protected function init($title) {
    $this->stylesheets[] = "modules/history.css";
    return parent::init($title);
  }

  private function getDiff() {
    return "DIFFFFF {$_SERVER["REVISION_HASH"]}";
  }

  /**
   * Returns diff between two commits of one file as styled HTML.
   *
   * @param string $head
   *   Hash of git commit (newer one).
   * @param sting $ref
   *   Hash of git commit (older one).
   * @param string $filename
   *   Name of file in repository.
   * @return string
   *   Returns diff of one file as styled HTML.
   */
  private function getDiffAsHTML($head, $ref, $filename) {
    $diff = $this->historyModel->getDiff($head, $ref, $filename);

    $html = "";
    $c = count($diff);
    // the first 5 lines are the header, nothing to do with it.
    for ($i = 5; $i < $c; ++$i) {
      if ($diff[$i][0] == " ") {
        $html .= substr($diff[$i], 1);
      }
      elseif ($diff[$i][0] == "+") {
        $tmp = substr($diff[$i], 1);
        $html .= "<span class='green'>{$tmp}</span>";
      }
      elseif ($diff[$i][0] == "-") {
        $tmp = substr($diff[$i], 1);
        $html .= "<span class='red'>{$tmp}</span>";
      }
    }
    return $html;
  }

  /**
   * Helper function to build revision history.
   */
  private function getRevisionHistory() {
    global $i18n;
    $commits = $this->historyModel->getLastCommits();
    $userIds = [];

    $c = count($commits);
    for ($i = 0; $i < $c; ++$i) {
      $userIds[] = $commits[$i]["author_id"];
    }

    $users = (new Users())->getUsers($userIds);

    $html =
      "<div id='revision-history'>" .
        "<h2>{$i18n->t("Revision history")}</h2>";

    $revisions = [];
    for ($i = 0; $i < $c; ++$i) {
      $authorName = $users[$commits[$i]["author_id"]]["name"];
      $revisions[$i] =
        "{$i18n->formatDate($commits[$i]["timestamp"], null, IntlDateFormatter::MEDIUM, IntlDateFormatter::MEDIUM)} by " .
        $this->a($i18n->r("/user/{0}", [ $authorName ]), $i18n->t($authorName), [
            "title" => $i18n->t("Profile of {0}", [ $authorName ])
        ]) .
        ": {$commits[$i]["subject"]} " .
        $this->a($i18n->r("/movie/{0}", [ $_SERVER["MOVIE_ID"] ]) . "/diff/{$commits[$i]["hash"]}", $i18n->t("Show diff"));

      $changedFiles = $this->historyModel->getChangedFiles($commits[$i]["hash"], "{$commits[$i]["hash"]}^1");
      $revisions[$i] .= (new Lists($changedFiles, ""))->toHtmlList();
    }

    $html .=
        (new Lists($revisions, ""))->toHtmlList() .
      "</div>";

    return $html;
  }

}
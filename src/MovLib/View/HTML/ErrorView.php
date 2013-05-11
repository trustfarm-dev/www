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
namespace MovLib\View\HTML;

use \Exception;
use \MovLib\Entity\Language;
use \MovLib\Utility\String;
use \MovLib\View\HTML\AbstractView;

/**
 * The error view is presented to the user if something terrible happens.
 *
 * @todo Create seperate CSS for stacktrace and load on demand.
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class ErrorView extends AbstractView {

  /**
   * An error view expects the complete exception object to be passed along.
   *
   * @param \MovLib\Entity\Language
   *   The currently active language entity instance.
   * @param \Exception $exception
   *   The exception that caused the error.
   */
  public function __construct(Language $language, Exception $exception) {
    parent::__construct($language, "Error");
    $this->setAlert(
      "<p>" . __("This shouldn’t have happened, but it did, an error occured while trying to handle your request.") . "</p>" .
      "<p>" . __("The error was logged and reported to the system administrators, it should be fixed in no time.") . "</p>" .
      "<p>" . __("Please try again in a few minutes.") . "</p>",
      __("We’re sorry but something went terribly wrong!"),
      "error",
      true
    );
    if (error_reporting() !== 0) {
      $this->setAlert(
        "<div style='margin:20px 0 0;padding:3px;background:#d8d8d8;border-radius:3px;font:12px/18px consolas,monospace'>" .
          "<div style='padding:5px 10px;height:33px;background-color:#eaeaea;background-image:linear-gradient(#fafafa,#eaeaea);border-bottom:1px solid #d8d8d8;color:#555;text-shadow:0 1px 0 #fff;line-height:25px'>" .
            "<i class='icon icon-attention'></i> {$exception->getMessage()}" .
          "</div>" .
          "<table style='margin:0;padding:0;width:100%;background:#fff;border-collapse:collapse;font:12px/24px consolas,monospace;color:#000'>" .
            $this->formatStacktrace($exception->getTrace()) .
          "</table>" .
        "</div>" .
        "<p class='centered'><small>Debug information is only available if error reporting is turned on!</small></p>",
        "Stacktrace",
        "info",
        true
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getBodyClass() {
    return "error";
  }

  /**
   * {@inheritdoc}
   */
  public function getRenderedContent() {
    return "";
  }

  /**
   * Format the given stacktrace.
   *
   * @param array $stacktrace
   *   The stacktrace of the exception.
   * @return string
   *   The formatted stacktrace.
   */
  private function formatStacktrace(array $stacktrace) {
    $output = "";
    $count = count($stacktrace) - 1;
    $highlight = ";background-color:#ffc";
    for ($i = 0; $i <= $count; ++$i) {
      $style = "";
      if ($i === 0) {
        $style .= ";padding-top:5px";
      } elseif ($i === $count) {
        $style .= ";padding-bottom:5px";
      }
      if (isset($stacktrace[$i]["args"]) === true || empty($stacktrace[$i]["args"]) === false) {
        foreach ($stacktrace[$i]["args"] as $delta => $arg) {
          $suffix = "";
          if (is_array($arg)) {
            $suffix = "(" . count($arg) . ")";
          }
          $title = String::checkPlain(print_r($arg, true));
          $type = gettype($arg);
          $stacktrace[$i]["args"][$delta] = "<var title='{$title}'>{$type}{$suffix}</var>";
        }
        $stacktrace[$i]["args"] = "(" . implode(", ", $stacktrace[$i]["args"]) . ")";
      } else {
        $stacktrace[$i]["args"] = "";
      }
      foreach ([ "line", "class", "type", "function", "file" ] as $s) {
        if (isset($stacktrace[$i][$s]) === false) {
          $stacktrace[$i][$s] = "";
        }
      }
      $output .=
        "<tr>" .
          "<td class='flush-right' style='padding:0 8px;width:1%;border-right:1px solid #e5e5e5;color:rgba(0,0,0,0.3){$style}'>" .
            $stacktrace[$i]["line"] .
          "</td>" .
          "<td style='padding:0{$style}'>" .
            "<div style='padding:0 10px{$highlight}'>" .
              $stacktrace[$i]["class"] .
              $stacktrace[$i]["type"] .
              "<span style='color:#00f'>{$stacktrace[$i]["function"]}</span>" .
              $stacktrace[$i]["args"] .
              "<span class='pull-right' style='color:rgba(0,0,0,0.3)'>" .
                str_replace($_SERVER["DOCUMENT_ROOT"], "", $stacktrace[$i]["file"]) .
              "</span>" .
            "</div>" .
          "</td>" .
        "</tr>"
      ;
      $highlight = "";
    }
    return $output;
  }

}

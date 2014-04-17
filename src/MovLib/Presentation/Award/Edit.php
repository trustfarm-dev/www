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
namespace MovLib\Presentation\Award;

use \MovLib\Data\Award\Award;
use \MovLib\Partial\Form;
use \MovLib\Partial\FormElement\InputText;
use \MovLib\Partial\FormElement\InputWikipedia;
use \MovLib\Partial\FormElement\TextareaHTML;
use \MovLib\Partial\FormElement\TextareaLineArray;
use \MovLib\Partial\FormElement\TextareaLineURLArray;

/**
 * Allows editing of a award's information.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Edit extends \MovLib\Presentation\AbstractEditPresenter {
  use \MovLib\Presentation\Award\AwardTrait;

  /**
   * {@inheritdoc}
   */
  public function init() {
    $this->entity = new Award($this->diContainerHTTP, $_SERVER["AWARD_ID"]);
    $pageTitle    = $this->intl->t("Edit {0}", [ $this->entity->name ]);
    return $this
      ->initPage($pageTitle, $pageTitle, $this->intl->t("Edit"))
      ->initEdit($this->entity, $this->intl->t("Awards"), $this->getSidebarItems())
    ;
  }

  /**
   * {@inheritdoc}
   */
   public function getContent() {
    return (new Form($this->diContainerHTTP))
      ->addElement(new InputText($this->diContainerHTTP, "name", $this->intl->t("Name"), $this->entity->name, [
        "#help-popup" => $this->intl->t("The name of the award."),
        "placeholder" => $this->intl->t("Enter the award’s name."),
        "autofocus"   => true,
        "required"    => true,
      ]))
      ->addElement(new TextareaLineArray($this->diContainerHTTP, "aliases", $this->intl->t("Alternative Names (line by line)"), $this->entity->aliases, [
        "#help-popup" => $this->intl->t("The alternative names of the award, line by line."),
        "placeholder" => $this->intl->t("Enter the award’s alternative names here, line by line."),
      ]))
      ->addElement(new TextareaHTML($this->diContainerHTTP, "description", $this->intl->t("Description"), $this->entity->description, [
        "#help-popup" => $this->intl->t("Description of the job."),
        "placeholder" => $this->intl->t("Describe the job."),
      ], [ "blockquote", "external", "headings", "lists", ]))
      ->addElement(new InputWikipedia($this->diContainerHTTP, "wikipedia", $this->intl->t("Wikipedia"), $this->entity->wikipedia, [
        "#help-popup"         => $this->intl->t("Link to a corresponding Wikipedia Page."),
        "placeholder"         => $this->intl->t("Enter the job’s corresponding Wikipedia link."),
        "data-allow-external" => "true",
      ]))
      ->addElement(new TextareaLineURLArray($this->diContainerHTTP, "links", $this->intl->t("Weblinks (line by line)"), $this->entity->links, [
        "#help-popup" => $this->intl->t("Weblinks relatet to the award, line by line."),
        "placeholder" => $this->intl->t("Enter the award’s related weblinks, line by line."),
      ]))
      ->addAction($this->intl->t("Update"), [ "class" => "btn btn-large btn-success" ])
      ->init([ $this, "valid" ])
    ;
  }

}

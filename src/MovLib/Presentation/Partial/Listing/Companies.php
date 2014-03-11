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
namespace MovLib\Presentation\Partial\Listing;

use \MovLib\Data\Company\Company;
use \MovLib\Presentation\Partial\Date;

/**
 * Special images list for company instances.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
class Companies extends \MovLib\Presentation\Partial\Listing\AbstractListing {


  // ------------------------------------------------------------------------------------------------------------------- Properties


  /**
   * The span size for a single company's description.
   *
   * @var integer
   */
  protected $descriptionSpan;

  /**
   * The company photo's style.
   *
   * @var integer
   */
  public $imageStyle = Company::STYLE_SPAN_01;

  /**
   * Show additional information or not.
   *
   * @var boolean
   */
  protected $showAdditionalInfo;


  // ------------------------------------------------------------------------------------------------------------------- Magic Methods


  /**
   * Instantiate new special companies listing.
   *
   * @param \mysqli_result $listItems
   *   The mysqli result object containing the company.
   * @param string $noItemsText
   *   {@inheritdoc}
   * @param array $listItemsAttributes
   *   {@inheritdoc}
   * @param array $attributes
   *   {@inheritdoc}
   * @param integer $spanSize [optional]
   *   The span size the list items should reserve, defaults to <code>5</code>
   * @param boolean $showAdditionalInfo [optional]
   *   Show additional information or not, defaults to <code>FALSE</code>.
   */
  public function __construct($listItems, $noItemsText = "", array $listItemsAttributes = null, array $attributes = null, $spanSize = 5, $showAdditionalInfo = false) {
    parent::__construct($listItems, $noItemsText, $attributes);
    $this->addClass("hover-list no-list", $this->attributes);
    $this->listItemsAttributes = $listItemsAttributes;
    $this->addClass("hover-item r", $this->listItemsAttributes);
    $this->descriptionSpan                 = --$spanSize;
    $this->listItemsAttributes["typeof"] = "http://schema.org/Corporation";
    $this->showAdditionalInfo              = $showAdditionalInfo;
  }


  // ------------------------------------------------------------------------------------------------------------------- Methods


  /**
   * @inheritdoc
   */
  protected function render() {
    global $i18n;
    $list = null;
    try {
      /* @var $company \MovLib\Data\Company\Company */
      while ($company = $this->listItems->fetch_object("\\MovLib\\Data\\Company\\Company")) {
        $additionalInfo = null;
        if ($this->showAdditionalInfo === true) {
          $companyDates = null;
          if ($company->foundingDate || $company->defunctDate) {
            if ($company->foundingDate) {
              $companyDates .= (new Date($company->foundingDate))->format([ "property" => "foundingDate", "title" => $i18n->t("Founding Date") ]);
            }
            else {
              $companyDates .= $i18n->t("{0}unknown{1}", [ "<em title='{$i18n->t("Founding Date")}'>", "</em>" ]);
            }

            if ($company->defunctDate) {
              $companyDates .= " – " . (new Date($company->defunctDate))->format([ "title" => $i18n->t("Defunct Date") ]);
            }

            $companyDates = "<br>{$companyDates}";
          }

          if ($companyDates) {
            $additionalInfo = "<span class='small'>{$companyDates}</span>";
          }
        }

        $list .=
          "<li{$this->expandTagAttributes($this->listItemsAttributes)}>" .
            $this->getImage($company->getStyle($this->imageStyle), $company->route, [ "property" => "image" ], [ "class" => "s s1" ]) .
            "<span class='s s{$this->descriptionSpan}'><a href='{$company->route}' property='name url'>{$company->name}</a>{$additionalInfo}</span>" .
          "</li>"
        ;
      }
      if (!$list) {
        return $this->noItemsText;
      }
      return "<ol{$this->expandTagAttributes($this->attributes)}>{$list}</ol>";
    }
    catch (\Exception $e) {
      return $e->getMessage();
    }
  }

}
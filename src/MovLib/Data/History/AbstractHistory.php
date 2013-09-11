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
namespace MovLib\Data\History;

use \MovLib\Exception\ErrorException;
use \MovLib\Exception\FileSystemException;
use \MovLib\Exception\HistoryException;
use \ReflectionClass;

/**
 * Contains methods to manage histories.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
abstract class AbstractHistory extends \MovLib\Data\Database {

  /**
   * The instance's id.
   * @var int
   */
  protected $id;

  /**
   * The instance's model short name.
   * @var string
   */
  protected $type;

  /**
   * The path to the repository
   * @var string
   */
  protected $path;

  /**
   * Constructor which must be called by all child classes
   *
   * @param int $id
   *  The id of the instance to be versioned
   * @throws HistoryException
   *  If no instance with given id is found
   */
  public function __construct($id) {
    $this->type = $this->getShortName();
    $this->id = $id;
    $this->path = "{$_SERVER["DOCUMENT_ROOT"]}/history/{$this->type}/{$this->id}";
  }

  /**
   * Abstract method which should write files to repository.
   */
  abstract protected function writeFiles();

  /**
   * Get the model's short class name (e.g. <em>movie</em> for <em>MovLib\Data\History\Movie</em>).
   *
   * The short name is the name of the current instance of this class without the namespace lowercased.
   *
   * @return string
   *   The short name of the class (lowercase) without the namespace.
   */
  public function getShortName() {
    return strtolower((new ReflectionClass($this))->getShortName());
  }

  /**
   * Creates a new folder, makes a git repository out of it, writes files and commits them.
   */
  public function init() {
    $this->createRepository();
    $this->writeFiles();
    $this->commit("initial commit");
  }

  /**
   * Create a folder on the filesystem and make it a git repository.
   *
   * @throws HistoryException
   *  If something went wrong with "git init"
   */
  public function createRepository() {
    $output = array();
    $returnVar = null;

    if (!is_dir(($this->path))) {
      mkdir($this->path, 0777, true);
    }

    exec("cd {$this->path} && git init", $output, $returnVar);
    if ($returnVar != 0) {
      throw new HistoryException("Error creating repository");
    }
  }

  /**
   * Checks in all changes and commits them
   *
   * @param int $author_id
   *  The user id of the author
   * @param string $message
   *  The commit message
   *
   * @throws HistoryException
   *  If something went wrong during commit
   */
  public function commit($author_id, $message) {
    $output = array();
    $returnVar = null;

    exec("cd {$this->path} && git add -A && git commit --author='{$author_id} <>' -m '{$message}'", $output, $returnVar);
    if ($returnVar != 0) {
      throw new HistoryException("Error commiting changes");
    }
  }

  /**
   * Returns diff between two commits of one file as styled HTML
   *
   * @param string $head
   *  Hash of git commit (newer one)
   * @param sting $ref
   *  Hash of git commit (older one)
   * @param string $filename
   *  Name of file in repository
   *
   * @throws HistoryException
   *  If something went wrong with "git diff"
   *
   * @return string
   *  Returns diff of one file as styled HTML
   */
  public function getDiffasHTML($head, $ref, $filename) {
    $output = array();
    $returnVar = null;
    $html = "";

    exec("cd {$this->path} && git diff {$ref} {$head} --word-diff='porcelain' {$filename}", $output, $returnVar);
    if ($returnVar != 0) {
      throw new HistoryException("There was an error during 'git diff'");
    }

    for ($i = 5; $i < count($output); $i++) {
      if ($output[$i][0] == " ") {
        $html .= substr($output[$i], 1);
      } else if ($output[$i][0] == "+") {
        $html .= "<span class='green'>" . substr($output[$i], 1) . "</span>";
      } else if ($output[$i][0] == "-") {
        $html .= "<span class='red'>" . substr($output[$i], 1) . "</span>";
      } else if ($output[$i][0] == "~") {
        $html .= "\n";
      }
    }

    return $html;
  }

  /**
   *
   * @param string $head
   *  Hash of git commit (newer one)
   * @param sting $ref
   *  Hash of git commit (older one)
   *
   * @throws HistoryException
   *  If something went wrong with "git diff"
   *
   * @return array
   *  Returns an array of changed files
   */
  public function getChangedFiles($head, $ref) {
    $output = array();
    $returnVar = null;
    exec("cd {$this->path} && git diff {$ref} {$head} --name-only", $output, $returnVar);
    if ($returnVar != 0) {
      throw new HistoryException("There was an error locating changed files");
    }
    return $output;
  }

  /**
   * Returns an array of associative arrays with commits
   *
   * @todo is subject safe?
   *
   * @throws HistoryException
   *  If something went wrong with "git log"
   *
   * @return associative array
   *  Returns an array of associative arrays with commits
   */
  public function getLastCommits() {
    $output = array();
    $returnVar = null;
    $format = '{"hash":"%H","author_id":%an, "timestamp":%at, "subject":"%s"}';

    exec("cd {$this->path} && git log --format='{$format}'", $output, $returnVar);
    if ($returnVar != 0) {
      throw new HistoryException("There was an error getting last commits");
    }

    foreach ($output as $key => $value) {
      $output[$key] = json_decode($value, true);
    }

    return $output;
  }

  /**
   * Write file to repository path
   *
   * @param string $filename
   *  The filename
   * @param string $content
   *  The content as string
   *
   * @throws FileSystemException
   *  If any of the actions fails (e.g. wrong permissions).
   */
  protected function writeToFile($filename, $content) {
    try {
      $fp = fopen("{$this->path}/{$filename}", 'w');
      fwrite($fp, $content);
      fclose($fp);
    } catch (ErrorException $e) {
      throw new FileSystemException("Error writing to file", $e);
    }
  }

  /**
   * Write specific columns of a related database row to a file.
   *
   * If no columns are given all columns are selected.
   *
   * @param string $relation
   *  A database relation (e.g. <em>movies_titles</em>).
   * @param array $columns
   *  Array of non dynmic columns to be written to the file.
   * @param array $dyn_columns
   *  Array of dynamic colums to be written to the file.
   */
  protected function writeRelatedRowsToFile($relation, $columns = array(), $dyn_columns = array()) {
    $tmp_dyn_columns = $dyn_columns;

    if (empty($columns) && empty($dyn_columns)) {
      $all_columns = "*";
    } else {
      foreach ($columns as $key => $value) {
        $columns[$key] = "`{$value}`";
      }
      foreach ($tmp_dyn_columns as $key => $value) {
        $tmp_dyn_columns[$key] = "COLUMN_JSON({$value}) AS `{$value}`";
      }
      $all_columns = implode(", ", array_merge($columns, $tmp_dyn_columns));
    }

    $rows = $this->select(
      "SELECT {$all_columns}
        FROM `{$relation}`
        WHERE `{$this->type}_id` = ?",
      "d",
      [$this->id]
    );

    foreach ($dyn_columns as $value) {
      $rows[0][$value] = json_decode($rows[0][$value], true);
    }

    $this->writeToFile("{$relation}", json_encode($rows));
  }

}
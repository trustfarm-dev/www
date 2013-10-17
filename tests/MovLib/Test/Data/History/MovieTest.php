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
namespace MovLib\Test\Data\History;

use \MovLib\Data\History\Movie;

/**
 * Test the Movie.
 *
 * @author Franz Torghele <ftorghele.mmt-m2012@fh-salzburg.ac.at>
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2013–present, MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link http://movlib.org/
 * @since 0.0.1-dev
 */
class MovieTest extends \MovLib\Test\TestCase {

  /** @var \mysqli */
  static $db;

  /** @var \MovLib\Data\History\Movie */
  public $movie;

  public static function setUpBeforeClass() {
    static::$db = new \mysqli();
    static::$db->real_connect();
    static::$db->select_db($GLOBALS["movlib"]["default_database"]);
  }

  public function setUp() {
    $this->movie = new Movie(2, "phpunitrepos");
    $commitHash = $this->movie->createRepository();
    static::$db->query("UPDATE `movies` SET `commit` = '{$commitHash}' WHERE `movie_id` = 2");
  }

  public static function tearDownAfterClass() {
    static::$db->close();
  }

  public function tearDown() {
    $path = "{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos";
    if(is_dir($path)) {
      exec("rm -rf {$path}");
    }
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::getShortName
   */
  public function testGetShortName() {
    $this->assertEquals("movie", $this->invoke($this->movie, "getShortName"));
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::createRepository
   */
  public function testCreateRepository() {
    $this->assertFileExists("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2");
    $this->assertFileExists("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/.git/HEAD");
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::hideRepository
   */
  public function testHideRepository() {
    $this->invoke($this->movie, "hideRepository");
    $this->assertFileExists("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/.2");
  }

  /**
   * @expectedException \MovLib\Exception\HistoryException
   * @expectedExceptionMessage Repository already hidden
   * @covers \Movlib\Data\History\AbstractHistory::hideRepository
   * @depends testHideRepository
   */
  public function testHideRepositoryIfHidden() {
    $this->invoke($this->movie, "hideRepository", [ $this->movie ]);
    $this->invoke($this->movie, "hideRepository", [ $this->movie ]);
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::unhideRepository
   */
  public function testUnhideRepository() {
    $this->invoke($this->movie, "hideRepository", [ $this->movie ]);
    $this->assertFileExists("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/.2");

    $this->invoke($this->movie, "unhideRepository", [ $this->movie ]);
    $this->assertFileExists("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2");
  }

  /**
   * @expectedException \MovLib\Exception\HistoryException
   * @expectedExceptionMessage Repository not hidden
   * @covers \Movlib\Data\History\AbstractHistory::unhideRepository
   */
  public function testUnhideRepositoryIfNotHidden() {
    $this->invoke($this->movie, "unhideRepository", [ $this->movie ]);
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::startEditing
   * @covers \Movlib\Data\History\AbstractHistory::getCommitHash
   */
  public function testStartEditing() {
    static::$db->query("UPDATE `movies` SET `commit` = 'b006169990b07af17d198f6a37efb324ced95fb3' WHERE `movie_id` = 2");
    $this->movie->startEditing();
    $this->assertEquals("b006169990b07af17d198f6a37efb324ced95fb3", $this->getProperty($this->movie, "commitHash"));
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::writeFiles
   */
  public function testWriteFiles() {
    // wrong offset name
    $this->invoke($this->movie, "writeFiles", [[ "foo" => "bar" ]]);
    $this->assertFileNotExists("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/foo");

    // offset which should be written to file directly
    $this->invoke($this->movie, "writeFiles", [[ "original_title" => "The Shawshank Redemption" ]]);
    $this->assertFileExists("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/original_title");
    $this->assertStringEqualsFile("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/original_title", "The Shawshank Redemption");

    // offset with language prefix which should be written to file directly
    $this->invoke($this->movie, "writeFiles", [[ "en_synopsis" => "A very short synopsis." ]]);
    $this->assertFileExists("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/en_synopsis");
    $this->assertStringEqualsFile("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/en_synopsis", "A very short synopsis.");

    // no file should be written if the offset is not set
    $this->assertFileNotExists("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/de_synopsis");

    // offset which should be written to file serialized
    $this->invoke($this->movie, "writeFiles", [[ "titles" => [ [ "id" => 1, "title" => "foo" ], [ "id" => 2, "title" => "bar" ] ] ]]);
    $this->assertFileExists("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/titles");
    $this->assertStringEqualsFile(
      "{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/titles",
      'a:2:{i:0;a:2:{s:2:"id";i:1;s:5:"title";s:3:"foo";}i:1;a:2:{s:2:"id";i:2;s:5:"title";s:3:"bar";}}'
    );
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::stageAllFiles
   * @covers \Movlib\Data\History\AbstractHistory::unstageFiles
   * @covers \Movlib\Data\History\AbstractHistory::resetFiles
   * @covers \Movlib\Data\History\AbstractHistory::commitFiles
   */
  public function testGitHelperMethods() {
    $path = $this->getProperty($this->movie, "path");

    // write files
    $this->invoke($this->movie, "writeFiles", [[ "original_title" => "The foobar is a lie", "year" => 2000, "runtime" => 42 ]]);

    // stage all files
    $this->invoke($this->movie, "stageAllFiles");
    $this->exec("cd {$path} && git status", $output);
    $this->assertEquals("# Changes to be committed:", $output[1]);
    $this->assertEquals("#	new file:   original_title", $output[4]);
    $this->assertEquals("#	new file:   runtime", $output[5]);
    $this->assertEquals("#	new file:   year", $output[6]);

    // commit all staged files
    $this->invoke($this->movie, "commitFiles", [ "movie created" ]);
    $this->exec("cd {$path} && git status", $output);
    $this->assertEquals("nothing to commit (working directory clean)", $output[1]);

    // update files
    $this->invoke($this->movie, "writeFiles", [ "original_title" => "The foobar is not a lie", "year" => 2001, "runtime" => 42 ]);

    // stage all files
    $this->invoke($this->movie, "stageAllFiles");
    unset($output);
    $this->exec("cd {$path} && git status", $output);
    $this->assertEquals("# Changes to be committed:", $output[1]);
    $this->assertEquals("#	modified:   original_title", $output[4]);
    $this->assertEquals("#	modified:   year", $output[5]);

    $this->assertStringEqualsFile("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/year", 2001);

    // unstage year
    $this->invoke($this->movie, "unstageFiles", [ "year" ]);
    unset($output);
    $this->exec("cd {$path} && git status", $output);
    $this->assertEquals("# Changes not staged for commit:", $output[6]);
    $this->assertEquals("#	modified:   year", $output[10]);

    // reset year
    $this->invoke($this->movie, "resetFiles", [ "year" ]);
    $this->exec("cd {$path} && git status");
    $this->assertStringEqualsFile("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/year", 2000);
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::getChangedFiles
   * @covers \Movlib\Data\History\AbstractHistory::getDirtyFiles
   */
  public function testGetChangedFiles() {
    $this->invoke($this->movie, "writeFiles", [ "original_title" => "The foobar is not a lie", "year" => 2001, "runtime" => 42 ]);
    $this->invoke($this->movie, "stageAllFiles");
    $this->invoke($this->movie, "commitFiles", [ "initial commit" ]);

    // with unstaged files
    $this->invoke($this->movie, "writeFiles", [ "original_title" => "The foobar is a lie", "year" => 2002, "runtime" => 42 ]);
    $this->assertEquals("original_title year", implode(" ", $this->invoke($this->movie, "getDirtyFiles")));

    // with 2 commits
    $this->invoke($this->movie, "stageAllFiles");
    $this->invoke($this->movie, "commitFiles", [ "second commit" ]);
    $this->assertEquals("original_title year", implode(" ", $this->movie->getChangedFiles("HEAD", "HEAD^1")));
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::getLastCommits
   */
  public function testGetLastCommits() {
    $stageAllFiles  = get_reflection_method($this->movie, "stageAllFiles");
    $commitFiles    = get_reflection_method($this->movie, "commitFiles");
    $getLastCommits = get_reflection_method($this->movie, "getLastCommits");
    $writeFiles     = get_reflection_method($this->movie, "writeFiles");

    $writeFiles->invoke($this->movie, [ "original_title" => "The foobar is a lie" ]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "initial commit");

    $writeFiles->invoke($this->movie, [ "year" => 2001 ]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "second commit");

    $writeFiles->invoke($this->movie, [ "runtime" => 300 ]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "third commit");

    $commits = $getLastCommits->invoke($this->movie);
    $this->assertEquals("third commit", $commits[0]["subject"]);
    $this->assertEquals("second commit", $commits[1]["subject"]);
    $this->assertEquals("initial commit", $commits[2]["subject"]);

    $oneCommit = $getLastCommits->invoke($this->movie, 1);
    $this->assertEquals("third commit", $oneCommit[0]["subject"]);
    $this->assertCount(1, $oneCommit);
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::getLastCommitHash
   * @depends testGetLastCommits
   */
  public function testGetLastCommitHash() {
    get_reflection_method($this->movie, "writeFiles")->invoke($this->movie, ["original_title" => "The foobar is a lie"]);
    get_reflection_method($this->movie, "stageAllFiles")->invoke($this->movie);
    get_reflection_method($this->movie, "commitFiles")->invoke($this->movie, "initial commit");
    $this->assertEquals(
      get_reflection_method($this->movie, "getLastCommitHash")->invoke($this->movie),
      get_reflection_method($this->movie, "getLastCommits")->invoke($this->movie)[0]["hash"]
    );
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::getArrayDiff
   * @covers \Movlib\Data\History\AbstractHistory::getArrayDiffIdCompare
   * @covers \Movlib\Data\History\AbstractHistory::getArrayDiffDeepCompare
   */
  public function testGetArrayDiff() {
    $stageAllFiles  = get_reflection_method($this->movie, "stageAllFiles");
    $commitFiles    = get_reflection_method($this->movie, "commitFiles");
    $writeFiles     = get_reflection_method($this->movie, "writeFiles");

    // cast with id 1 and 4 in "added"
    $writeFiles->invoke($this->movie, ["cast" => [["id" => 1, "roles" => "franz"], ["id" => 4, "roles" => "sebastian"]]]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "cast with id 1 and 4");

    $this->assertEquals(1, $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["added"][0]["id"]);
    $this->assertEquals(4, $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["added"][1]["id"]);

    // cast with id 2 in "added"
    $writeFiles->invoke($this->movie, ["cast" => [
      ["id" => 1, "roles" => "franz"],
      ["id" => 2, "roles" => "richard"],
      ["id" => 4, "roles" => "sebastian"]
    ]]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "added cast with id 2");

    $this->assertEquals(2, $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["added"][0]["id"]);

    // cast with id 1 in "removed"
    $writeFiles->invoke($this->movie, ["cast" => [["id" => 2, "roles" => "richard"], ["id" => 4, "roles" => "sebastian"]]]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "removed cast with id 1");

    $this->assertEquals(1, $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["removed"][0]["id"]);

    // cast with id 2 in "edited"
    $writeFiles->invoke($this->movie, ["cast" => [["id" => 2, "roles" => "markus"], ["id" => 4, "roles" => "sebastian"]]]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "edited cast with id 2");

    $this->assertEquals(2, $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["edited"][0]["id"]);
    $this->assertEquals("richard", $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["edited"][0]["old"]["roles"]);
    $this->assertEquals("markus", $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["edited"][0]["roles"]);
    $this->assertEquals(false, isset($this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["added"][0]));
    $this->assertEquals(false, isset($this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["removed"][0]));

    // cast with id 2 in "edited", 4 is "removed" and 5 is "added"
    $writeFiles->invoke($this->movie, ["cast" => [
      ["id" => 2, "roles" => "franz"],
      ["id" => 5, "roles" => "sebastian"]
    ]]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "edited cast with id 2");

    $this->assertEquals(2, $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["edited"][0]["id"]);
    $this->assertEquals("markus", $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["edited"][0]["old"]["roles"]);
    $this->assertEquals("franz", $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["edited"][0]["roles"]);
    $this->assertEquals(4, $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["removed"][0]["id"]);
    $this->assertEquals(5, $this->movie->getArrayDiff("HEAD", "HEAD~1", "cast")["added"][0]["id"]);
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::getDiff
   */
  public function testGetDiff() {
    $stageAllFiles  = get_reflection_method($this->movie, "stageAllFiles");
    $commitFiles    = get_reflection_method($this->movie, "commitFiles");
    $writeFiles     = get_reflection_method($this->movie, "writeFiles");

    $writeFiles->invoke($this->movie, ["original_title" => "The foobar is a lie"]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "initial commit");

    $writeFiles->invoke($this->movie, ["original_title" => "The bar is not a lie"]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "second commit");

    $diff = $this->movie->getDiff("HEAD", "HEAD^1", "original_title");
    $this->assertEquals(" The", $diff[5]);
    $this->assertEquals("-foobar", $diff[6]);
    $this->assertEquals("+bar", $diff[7]);
    $this->assertEquals("  is", $diff[8]);
    $this->assertEquals("+not", $diff[9]);
    $this->assertEquals("  a lie", $diff[10]);
  }

  /**
   * @expectedException \MovLib\Exception\HistoryException
   * @expectedExceptionMessage startEditing() has to be called before saveHistory()!
   * @covers \Movlib\Data\History\AbstractHistory::saveHistory
   */
  public function testSaveHistoryWithoutStartEditing() {
    $this->movie->saveHistory([], "initial commit");
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::saveHistory
   */
  public function testSaveHistory() {
    $this->movie->startEditing();
    $this->movie->saveHistory([ "original_title" => "The foobar is a lie"], "initial commit" );
    $this->assertFileExists(("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/original_title"));
  }

  /**
   * @expectedException \MovLib\Exception\HistoryException
   * @expectedExceptionMessage Someone else edited the same information about the movie!
   * @covers \Movlib\Data\History\AbstractHistory::saveHistory
   */
  public function testSaveHistoryIfSomeoneElseAlreadyChangedTheSameInformation() {
    $this->movie->startEditing();
    $this->movieUserOne = $this->movie;
    $this->movieUserTwo = clone $this->movie;

    $commitHash = $this->movieUserOne->saveHistory([ "original_title" => "The foobar is a lie" ], "initial commit");
    static::$db->query("UPDATE `movies` SET `commit` = '{$commitHash}' WHERE `movie_id` = 2");

    $this->movieUserTwo->saveHistory([ "original_title" => "The bar is not a lie" ], "initial commit");

    $this->assertStringEqualsFile("{$_SERVER["DOCUMENT_ROOT"]}/phpunitrepos/movie/2/original_title", "The foobar is a lie");
  }

  /**
   * @covers \Movlib\Data\History\AbstractHistory::getFileAtRevision
   */
  public function testGetFileAtRevision() {
    $stageAllFiles  = get_reflection_method($this->movie, "stageAllFiles");
    $commitFiles    = get_reflection_method($this->movie, "commitFiles");
    $writeFiles     = get_reflection_method($this->movie, "writeFiles");

    $writeFiles->invoke($this->movie, ["original_title" => "The foobar is a lie"]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "initial commit");

    $writeFiles->invoke($this->movie, ["original_title" => "The bar is not a lie"]);
    $stageAllFiles->invoke($this->movie);
    $commitFiles->invoke($this->movie, "second commit");

    $this->assertEquals(
      "The foobar is a lie",
      get_reflection_method($this->movie, "getFileAtRevision")->invoke($this->movie, "original_title", "HEAD^1")
    );
  }

}

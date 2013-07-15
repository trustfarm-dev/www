USE `movlib`;
BEGIN;
-- Roundhay Garden Scene
INSERT INTO `posters`
  (
    `movie_id`,
    `poster_id`,
    `user_id`,
    `country_id`,
    `filename`,
    `width`,
    `height`,
    `size`,
    `ext`,
    `created`,
    `rating`,
    `dyn_descriptions`,
    `hash`
  )
VALUES
  (
    1,
    1,
    1,
    77,
    "Roundhay-Garden-Scene.1.en",
    856,
    482,
    73462,
    "jpg",
    CURRENT_TIMESTAMP,
    0,
    '',
    'hash'
  )
;
-- The Shawshank Redemption
INSERT INTO `posters`
  (
    `movie_id`,
    `poster_id`,
    `user_id`,
    `country_id`,
    `filename`,
    `width`,
    `height`,
    `size`,
    `ext`,
    `created`,
    `rating`,
    `dyn_descriptions`,
    `hash`
  )
VALUES
  (
    2,
    1,
    1,
    233,
    "The-Shawshank-Redemption.1.en",
    269,
    395,
    66394,
    "jpg",
    CURRENT_TIMESTAMP,
    0,
    '',
    'hash'
  )
;
COMMIT;
-- Léon
INSERT INTO `posters`
  (
    `movie_id`,
    `poster_id`,
    `user_id`,
    `country_id`,
    `filename`,
    `width`,
    `height`,
    `size`,
    `ext`,
    `created`,
    `rating`,
    `dyn_descriptions`,
    `hash`
  )
VALUES
  (
    3,
    1,
    1,
    233,
    "Léon-The-Professional.1.en",
    936,
    1408,
    648028,
    "jpg",
    CURRENT_TIMESTAMP,
    0,
    '',
    'hash'
  )
;
COMMIT;

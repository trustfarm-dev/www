USE `movlib`;
BEGIN;
INSERT INTO `genres` (`name_en`, `name_de`) VALUES ("Action","Action"),
("Adventure","Abenteuer"),
("Animation","Animation"), 
("Biography","Biografie"),
("Comedy","Komödie"),
("Crime","Verbrechen"),
("Documentary","Dokumentation"),
("Drama","Drama"),
("Family","Familie"),
("Fantasy","Fantasy"),
("Film-Noir","Film-Noir"),
("History","Geschichte"),
("Horror","Horror"),
("Music","Musik"),
("Musical","Musical"),
("Mystery","Mystery"),
("Romance","Romantik"),
("Sci-Fi","Sci-Fi"),
("Short","Kurz"),
("Silent","Stumm"),
("Sport","Sport"),
("Thriller","Thriller"),
("War","Krieg"),
("Western","Western"),
("Pornography","Pornografie");
COMMIT;
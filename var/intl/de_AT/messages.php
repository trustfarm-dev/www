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

/**
 * System message translations.
 *
 * <b>NOTE</b><br>
 * We manage these translations via PHP array because we don't want to stress the database with endless string look-ups
 * and lock during all those operations. It also allows us to keep the {@see \MovLib\Core\Intl} database free which is
 * important if we encounter any database related problems and still want to translate our presentations (even if we're
 * only talking about error pages at this point).
 *
 * @author Richard Fussenegger <richard@fussenegger.info>
 * @copyright © 2014 MovLib
 * @license http://www.gnu.org/licenses/agpl.html AGPL-3.0
 * @link https://movlib.org/
 * @since 0.0.1-dev
 */
// @codeCoverageIgnoreStart
return [
  // COMING SOON PAGE >>>
  "Sign up for the {sitename} beta!" => "Melde dich jetzt für die {sitename} Beta an!",
  "The open beta is scheduled to start in June 2014." => "Die offene Beta ist für den Juni 2014 geplant.",
  "Wanna see the current alpha version of {sitename}? Go to {alpha_url}" => "Willst du die aktuelle Alpha-Version von {sitename} sehen? Gehe zu {alpha_url}",
  "Imagine {1}Wikipedia{0}, {2}Discogs{0}, {3}Last.fm{0}, {4}IMDb{0}, and {5}TheMovieDB{0} combined in a totally free and open project." => "Stell dir {1}Wikipedia{0}, {2}Discogs{0}, {3}Last.fm{0}, {4}IMDb{0} und {5}TheMovieDB{0} kombiniert in einem völlig freien und offenen Projekt vor.",
  "Thanks for signing up for the {sitename} beta {email}." => "Danke für deine Anmeldung zur {sitename} Beta {email}.",
  "Successfully Signed Up" => "Erfolgreich Angemeldet",
  // COMING SOON PAGE <<<

  // GENERICS >>>
  "“{0}”" => "„{0}”",
  "{page_title} — {sitename}" => "{page_title} – {sitename}",
  // GENERICS <<<

  // NAME AND SLOGAN >>>
  "the free movie library" => "die freie Kinemathek",
  "{0}The {1}free{2} movie library.{3}" => "{0}Die {1}freie{2} Kinemathek.{3}",
  "{sitename}, {0}the {1}free{2} movie library.{3}" => "{sitename}, {0}die {1}freie{2} Kinemathek.{3}",
  // NAME AND SLOGAN <<<

  // SYSTEM MESSAGES >>>
  "Go back to the home page." => "Gehe zurück zur Startseite.",
  "IP address or user agent string is invalid or empty." => "IP-Adresse oder User-Agent-Zeichenkette ist invalide oder leer.",
  "Please note that you have to submit your IP address and user agent string to identify yourself as being human; should you have privacy concerns read our {privacy_policy}." => "Bitte nimm zur Kenntniss, dass du deine IP-Adresse und User-Agent-Zeichenkette übermitteln musst um dich als Mensch zu identifizieren; sollte du Datenschutzbedenken haben lies unsere {privacy_policy}.",
  "You’re currently viewing this page." => "Du betrachtest diese Seite momentan.",
  "Choose your language" => "Wähle deine Sprache",
  "Language" => "Sprache",
  "Infos all around {sitename}" => "Information rund um {sitename}",
  "Copyright and licensing information" => "Urheber- und Lizenzinformationen",
  "Database data is available under the {0}Creative Commons — CC0 1.0 Universal{1} license." => "Datenbankdaten sind unter der {0}Creative Commons — CC0 1.0 Universal{1} Lizenz verfügbar.",
  "Additional terms may apply for third-party content, please refer to any license or copyright information that is additionaly stated." => "Inhalte von Dritten unterliegen möglicherweise zusätzlichen Bedingungen, bitte achte auf zusätzliche Lizenz- und Urheberinformatinen.",
  "Sponsors and external resources" => "Sponsoren und externe Ressourcen",
  "Made with {love} in Austria" => "Hergestellt mit {love} in Österreich",
  "love" => "Liebe",
  "Legal Links" => "Rechtliche Links",
  "Privacy Policy" => "Datenschutz",
  "Terms of Use" => "Nutzungsbedingungen",
  "Save" => "Speichern",
  "Change" => "Ändern",
  "Reset" => "Zurücksetzen",
  "Home" => "Startseite",
  "Movie" => "Film",
  "Movies" => "Filme",
  "Release" => "Veröffentlichung",
  "Releases" => "Veröffentlichungen",
  "Persons" => "Personen",
  "Series" => "Serien",
  "Company" => "Unternehmen",
  "Companies" => "Unternehmen",
  "Help" => "Hilfe",
  "Create New Movie" => "Neuen Film Anlegen",
  "Create New Series" => "Neue Serie Anlegen",
  "Create New Release" => "Neue Veröffentlichung Anlegen",
  "Create New Person" => "Neue Person Anlegen",
  "Create New Company" => "Neues Unternehmen Anlegen",
  "Create New Award" => "Neue Auszeichnung Anlegen",
  "Create New Genre" => "Neues Genre Anlegen",
  "Create New Job" => "Neue Tätigkeit Anlegen",
  "Create New Category" => "Neue Kategorie Anlegen",
  "Create New Event" => "Neues Event Anlegen",
  "Create Movie" => "Film Anlegen",
  "Create Series" => "Serie Anlegen",
  "Create Release" => "Veröffentlichung Anlegen",
  "Create Person" => "Person Anlegen",
  "Create Company" => "Unternehmen Anlegen",
  "Create Award" => "Auszeichnung Anlegen",
  "Create Genre" => "Genre Anlegen",
  "Create Job" => "Tätigkeit Anlegen",
  "Create Category" => "Kategorie Anlegen",
  "Create" => "Anlegen",
  "Latest Entries" => "Neueste Einträge",
  "Charts" => "Charts",
  "Profile" => "Profil",
  "Messages" => "Nachrichten",
  "Collection" => "Sammlung",
  "Wantlist" => "Wunschliste",
  "Lists" => "Listen",
  "Watchlist" => "Beobachtungsliste",
  "Account" => "Konto",
  "Notifications" => "Benachrichtungen",
  "Email" => "E-Mail",
  "Email Address" => "E-Mail-Adresse",
  "Password" => "Passwort",
  "Danger Zone" => "Gefahrenzone",
  "Sign In" => "Anmelden",
  "User" => "Benutzer",
  "Users" => "Benutzer",
  "Your profile is currently empty, {0}click here to edit{1}." => "Dein Profil ist derzeit leer, {0}klicke hier um es zu bearbeiten{1}.",
  "Joined {date} and was last seen {time}." => "Beigetreten am {date} und wurde zuletzt gesehen: {time}.",
  "Recently Rated Movies" => "Zuletzt Bewertete Filme",
  "Runtime" => "Laufzeit",
  "Join" => "Beitreten",
  "Subject" => "Betreff",
  "Send" => "Senden",
  "This will appear as subject of your message" => "Dies wird der Betreff deiner Nachricht",
  "Enter “{0}” text here …" => "Gib deinen „{0}” Text hier ein …",
  "original title" => "Originaltitel",
  "born name" => "Geburtsname",
  "No jobs available. Please go to a movie or series page and add them there." => "Keine Jobs vorhanden. Bitte gehe zu einer Film- oder Serienseite und füge diese dort hinzu.",
  "Born on {date} in {place} and would be {age} years old." => "Geboren am {date} in {place} und würde heute {age} Jahre alt sein.",
  "Born on {date} in {place} and is {age} years old." => "Geboren am {date} in {place} und ist heute {age} Jahre alt.",
  "Born on {date} and would be {age} years old." => "Geboren am {date} und würde heute {age} Jahre alt sein.",
  "Born on {date} and is {age} years old." => "Geboren am {date} und ist heute {age} Jahre alt.",
  "Born in {place}." => "Geboren in {place}.",
  "Died on {date} in {place} at the age of {age} years." => "Gestorben am {date} in {place} im Alter von {age} Jahren.",
  "Died on {date} in {place}." => "Gestorben am {date} in {place}.",
  "Died on {date} at the age of {age} years." => "Gestorben am {date} im Alter von {age} Jahren.",
  "Died on {date}." => "Gestorben am {date}.",
  "Died in {place}." => "Gestorben in {place}.",
  "Wikipedia Article" => "Wikipedia-Artikel",
  "Join {sitename}" => "{sitename} beitreten",
  "Reset Password" => "Passwort Zurücksetzen",
  "Forgot Password" => "Passwort Vergessen",
  "Forgot your password?" => "Passwort vergessen?",
  "You must be signed in to access this content." => "Du musst angemeldet sein um auf diesen Inhalt zugreifen zu können.",
  "Please sign in again to verify the legitimacy of this request." => "Bitte melde dich erneut an um die Legitimität dieser Anfrage zu bestätigen.",
  "Active Sessions" => "Aktive Sessions",
  "Delete Account" => "Konto Löschen",
  "Delete" => "Löschen",
  "Sign In Time" => "Anmeldezeit",
  "User Agent" => "User-Agent",
  "IP address" => "IP-Adresse",
  "Password Settings" => "Passworteinstellungen",
  "Email Settings" => "E-Mail-Einstellungen",
  "Your current email address is {0}" => "Deine aktuelle E-Mail-Adresse ist {0}",
  "Enter your email address" => "Gib deine E-Mail-Adresse ein",
  "New Password" => "Neues Passwort",
  "Confirm Password" => "Bestätigunspasswort",
  "Enter your new password" => "Gib dein neues Passwort ein",
  "Enter your new password again" => "Gib dein neues Passwort erneut ein",
  "Notification Settings" => "Benachrichtigungseinstellungen",
  "Check back later" => "Schau später nochmals vorbei",
  "Account Settings" => "Kontoeinstellungen",
  "Real Name" => "Bürgerlicher Name",
  "Avatar" => "Avatar",
  "Sex" => "Geschlecht",
  "Female" => "Weiblich",
  "Male" => "Männlich",
  "Unknown" => "Unbekannt",
  "Date of Birth" => "Geburtsdatum",
  "About Me" => "Über Mich",
  "System Language" => "Systemsprache",
  "Country" => "Land",
  "Time Zone" => "Zeitzone",
  "Currency" => "Währung",
  "Website" => "Website",
  "Keep my data private!" => "Halte meine Daten privat!",
  "Tip" => "Tipp",
  "{image_name} {current} of {total} from {title}" => "{image_name} {current} von {total} von {title}",
  "Explore" => "Entdecken",
  "Marketplace" => "Marktplatz",
  "My Messages" => "Meine Nachrichten",
  "My Collection" => "Meine Sammlung",
  "My Wantlist" => "Meine Wunschliste",
  "My Lists" => "Meine Listen",
  "My Watchlist" => "Meine Beobachtungsliste",
  "Settings" => "Einstellungen",
  "Sign Out" => "Abmelden",
  "My" => "Mein",
  "Latest Users" => "Neueste Benutzer",
  "Utilities" => "Werkzeuge",
  "Deletion Requests" => "Löschanträge",
  "Create New" => "Neu Anlegen",
  "Random Movie" => "Zufälliger Film",
  "Random Series" => "Zufällige Serie",
  "Random Person" => "Zufällige Person",
  "Random Company" => "Zufälliges Unternehmen",
  "More" => "Mehr",
  "Explore all genres" => "Entdecke alle Genres",
  "Explore all articles" => "Entdecke alle Artikel",
  "Do you like movies?{0}Great, so do we!" => "Du magst Filme?{0}Großartig, wir auch!",
  "My {sitename}" => "Mein {sitename}",
  "Results from {from,number,integer} to {to,number,integer} of {total,number,integer} results." => "Ergebnisse von {from,number,integer} bis {to,number,integer} von {total,number,integer} Ergebnissen.",
  "View" => "Ansehen",
  "Edit" => "Bearbeiten",
  "Discuss" => "Diskutieren",
  "History" => "Geschichte",
  "Synopsis" => "Synopsis",
  "Cast" => "Darsteller",
  "Directors" => "Regisseure",
  "Trailers" => "Trailer",
  "Reviews" => "Rezensionen",
  "No countries assigned yet, {0}add countries{1}?" => "Keine Länder zugeordnet, {0}Länder hinzufügen{1}?",
  "No genres assigned yet, {0}add genres{1}?" => "Keine Genres zugeordnet, {0}Genres hinzufügen{1}?",
  "No synopsis available, {0}write synopsis{1}?" => "Keine Synopsis vorhanden, {0}Synopsis verfassen{1}?",
  "No directors assigned yet, {0}add directors{1}?" => "Keine Regisseure zugeordnet, {0}Regisseure hinzufügen{1}?",
  "No cast assigned yet, {0}add cast{1}?" => "Keine Darsteller zugeordnet, {0}Darsteller hinzufügen{1}?",
  "Awful" => "Furchtbar",
  "Bad" => "Schlecht",
  "Okay" => "OK",
  "Fine" => "Gut",
  "Awesome" => "Großartig",
  "with {0, plural, one {one star} other {# stars}}" => "mit {0, plural, one {einem Stern} other {# Sternen}}",
  "You’re the only one who voted for this movie (yet)." => "Nur du hast diesen Film (bisher) bewertet.",
  "No one has rated this movie so far, be the first." => "Niemand hat diesen Film bisher bewertet, sei der Erste.",
  "You’re the only one who rated this movie (yet)." => "Nur du hast diesen Film (bisher) bewertet.",
  "Rated by {votes} user with {rating}." => "Bewertet von {votes} Benutzer mit {rating}.",
  "Rated by {votes} users with a {0}mean rating{1} of {rating}." => "Bewertet von {votes} Benutzern mit einer {0}Durchschnittsbewertung{1} von {rating}.",
  "View the rating demographics." => "Bewertungsdemographien ansehen.",
  "Rate this movie" => "Diesen Film bewerten",
  "Please {sign_in} or {join} to rate this movie." => "Bitte {sign_in} oder {join} um diesen Film zu bewerten.",
  "The submitted rating isn’t valid. Valid ratings range from: {min} to {max}" => "Die übermittelte Bewertung ist nicht valide. Valide Bewertung sind von {min} bis {max}.",
  "Enter the email address associated with your {sitename} account. Password reset instructions will be sent via email." => "Gib die E-Mail-Adresse deines {sitename}-Kontos ein. Instruktionen zum Zurücksetzen deines Passworts werden via E-Mail versandt.",
  "We hope to see you again soon." => "Wir hoffen dich bald wieder zu sehen.",
  "Sign Out Successfull" => "Abmeldung Erfolgreich",
  "Enter your password" => "Gib dein Passwort ein",
  "Sign Up" => "Beitreten",
  "Username" => "Benutzername",
  "Enter your desired username" => "Gib deinen gewünschten Benutzernamen ein",
  "I accept the {a1}privacy policy{a} and the {a2}terms of use{a}." => "Ich akzeptiere die {a1}Datenschutzerklärung{a} und die {a2}Nutzungsbedingungen{a}.",
  "A username must be valid UTF-8, cannot contain spaces at the beginning and end or more than one space in a row, it cannot contain any of the following characters {0} and it cannot be longer than {1,number,integer} characters." => "Ein Benutzername muss in validem UTF-8 sein, kann keine Leerzeichen am Anfang und Ende oder mehr als eines hintereinander besitzen, weiters kann er keine dieser Zeichen {0} beinhalten und nicht länger als {1,number,integer} Zeichen sein.",
  "An email address in the format [local]@[host].[tld] with a maximum of {0,number,integer} characters" => "Eine E-Mail-Adresse im Format [local]@[host].[tld] mit maximal {0,number,integer} Zeichen.",
  "Is your language missing in our list? {0}Help us translate {sitename}.{1}" => "Fehlt deine Sprache in unserer Liste? {0}Hilf uns {sitename} zu übersetzen.{1}",
  "Please select your preferred language from the following list." => "Bitte wähle deine bevorzugte Sprache aus der folgenden Liste.",
  "Is your language missing from our list? Help us translate {sitename} to your language. More information can be found at {0}our translation portal{1}." => "Fehlt deine Sprache in unserer Liste? Hilf uns {sitename} in deine Sprache zu übersetzen. Mehr Informationen findest du in {0}unserem Übersetzungsportal{1}.",
  "Internal Server Error" => "Interner Serverfehler",
  "An unexpected condition which prevented us from fulfilling the request was encountered." => "Ein unerwarteter Zustand hat uns davon abgehalten deine Anfrage zu erfüllen.",
  "This error was reported to the system administrators, it should be fixed in no time. Please try again in a few minutes." => "Dieser Fehler wurde an die System-Administratoren gemeldet und sollte schnell repariert werden. Bitte probiere es in ein paar Minuten erneut.",
  "Stacktrace for {0}" => "Stacktrace für {0}",
  "Forbidden" => "Untersagt",
  "Access to the requested page is forbidden." => "Zugriff auf die angeforderte Seite ist untersagt.",
  "Only administrators can handle deletion requests." => "Ausschließlich Administratoren können Löschanträge bearbeiten.",
  "All" => "Alle",
  "Spam" => "Spam",
  "Duplicate" => "Duplikate",
  "Other" => "Andere",
  "Great, not a single deletion request is waiting for approval." => "Gorßartig, nicht ein einziger Löschantrag wartet auf Bearbeitung.",
  "No Deletion Requests" => "Keine Löschanträge",
  "{date}: {user} has requested that {0}this content{1} should be deleted." => "{date}: {user} hat angefordert, dass {0}dieser Inhalt{1} gelöscht werden sollte.",
  "{date}: {user} has requested that {0}this content{1} should be deleted for the reason: “{reason}”" => "{date}: {user} hat angefordert, dass {0}dieser Inhalt{1} gelöscht werden sollte aus dem Grund: „{reason}”",
  "You can filter the deletion requests via the sidebar menu." => "Du kannst die Löschanträge über das seitliche Navigationsmenü filtern.",
  "Successfully Signed In" => "Erfolgreich Angemeldet",
  "Welcome back {username}!" => "Willkommen zurück {username}!",
  "Not Found" => "Nicht Gefunden",
  "The requested page could not be found." => "Die angeforderte Seite konnte nicht gefunden werden.",
  "There can be various reasons why you might see this error message. If you feel that receiving this error is a mistake please {0}contact us{1}." => "Es kann viele verschiedene Gründe für diese Fehlermeldung geben. Solltest du der Meinung sein, dass dieser Fehler nicht korrekt ist {0}kontaktiere uns{1}.",
  "{image_name} for {title}" => "{image_name} für {title}",
  "Poster" => "Poster",
  "Posters" => "Poster",
  "Lobby Card" => "Aushangbild",
  "Lobby Cards" => "Aushangbilder",
  "Backdrop" => "Hintergrund",
  "Backdrops" => "Hintegründe",
  "Description" => "Beschreibung",
  "Publishing Date" => "Veröffentlichungsdatum",
  "Upload" => "Hochladen",
  "Upload new poster for {title}" => "Neues Poster für {title} hochladen",
  "Upload new lobby card for {title}" => "Neues Aushangbild für {title} hochladen",
  "Upload new backdrop for {title}" => "Neuen Hintergrund für {title} hochladen",
  "None" => "Keines",
  "No Language" => "Keine Sprache",
  "Please Select …" => "Bitte auswählen …",
  "The image you see is only a preview, you still have to submit the form." => "Das Bild das du siehst ist ausschließlich eine Vorschau, du musst das Formular absenden um es hochzuladen.",
  "Provided by" => "Bereitgestellt von",
  "File size" => "Dateigröße",
  "Upload on" => "Hochgeladen am",
  "Dimensions" => "Dimensionen",
  "Upload New" => "Neues Hochladen",
  "Back to movie" => "Zurück zum Film",
  "Successfully Edited" => "Erfolgreich Bearbeitet",
  "previous" => "zurück",
  "next" => "weiter",
  "No Data Available" => "Keine Daten Verfügbar",
  "{sitename} has no further details about {person_name}." => "{sitename} hat keine weiteren Details zu {person_name}.",
  "Biography" => "Biographie",
  "Filmography" => "Filmographie",
  "Director" => "Regisseur",
  "Also Known As" => "Auch Bekannt Als",
  "External Links" => "Externe Links",
  "Jobs" => "Tätigkeiten",
  "Job" => "Tätigkeit",
  "Awards" => "Auszeichnungen",
  "Award" => "Auszeichnung",
  "won" => "gewonnen",
  "has won" => "gewann",
  "was nominated in" => "wurde nominiert für",
  "nominated" => "nominiert",
  "{0}x won" => "{0}x gewonnen",
  "{0}x nominated" => "{0}x nominiert",
  "Category" => "Kategorie",
  "Categories" => "Kategorien",
  "Categories of {0}" => "Kategorien von {0}",
  "Discussion" => "Diskussion",
  "Discussion of {0}" => "Diskussion über {0}",
  "Edit {0}" => "{0} bearbeiten",
  "History of {0}" => "Geschichte von {0}",
  "Delete {0}" => "{0} löschen",
  "Movies with {0}" => "Filme mit {0}",
  "Movies from {0}" => "Filme von {0}",
  "Series with {0}" => "Serien mit {0}",
  "Series from {0}" => "Serien von {0}",
  "Releases with {0}" => "Veröffentlichungen mit {0}",
  "Releases from {0}" => "Veröffentlichungen von {0}",
  "from {year_from} to {year_to}" => "vom {year_from} bis {year_to}",
  "since {year}" => "seit {year}",
  "until {year}" => "bis {year}",
  "in {0}" => "in {0}",
  "on {0}" => "am {0}",
  "Read the API documentation" => "Lese die API-Dokumentation",
  "This field is required." => "Dies ist ein Pflichtfeld.",
  "A password must contain lowercase and uppercase letters, numbers, and must be at least 8 characters long." => "Ein Passwort muss Nummern, Klein- und Großbuchstaben enthalten sowie mindestens 8 Zeichen lang sein.",
];
// @codeCoverageIgnoreEnd

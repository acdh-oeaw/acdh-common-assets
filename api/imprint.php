<?php

/**
 * The MIT License
 *
 * Copyright 2016 Austrian Centre for Digital Humanities.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 */

require_once __DIR__ . '/config.php';

if (SET_CORS_HEADERS) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');
}

$id       = filter_input(INPUT_GET, 'serviceID', FILTER_SANITIZE_NUMBER_INT);
$url      = REDMINE_API_URL . '/issues/' . $id . '.json';
$curl     = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL               => $url,
    CURLOPT_FOLLOWLOCATION    => true,
    CURLOPT_HEADER            => false,
    CURLOPT_RETURNTRANSFER    => true,
    CURLOPT_UNRESTRICTED_AUTH => true,
    CURLOPT_HTTPAUTH          => CURLAUTH_BASIC,
    CURLOPT_USERPWD           => REDMINE_USER . ':' . REDMINE_PSWD,
]);
$issue    = json_decode(curl_exec($curl));
$respCode = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
if ($respCode === 404) {
    header("HTTP/1.1 404 Not Found");
    exit("There is no corresponding Redmine issue\n");
}
if ($respCode !== 200) {
    header("HTTP/1.1 " . ($respCode ?? 500) . " Error");
    exit("Redmine response code was $respCode\n");
}
$desc = null;
if (!empty($issue) && isset($issue->issue->custom_fields) && is_array($issue->issue->custom_fields)) {
    foreach ($issue->issue->custom_fields as $i) {
        if ($i->name === 'ImprintParams') {
            $desc = @yaml_parse($i->value);
            break;
        }
    }
}
if ($desc === false) {
    header("HTTP/1.1 400 The Redmine issue does not provide a correct ImprintParams YAML");
    exit("The Redmine issue does not provide a correct ImprintParams YAML\n");
}
if (empty($desc)) {
    header("HTTP/1.1 404 The Redmine issue does not provide an ImprintParams field");
    exit("The Redmine issue does not provide an ImprintParams field\n");
}

// In case the output is prefered raw
if (filter_input(INPUT_GET, 'raw')) {
    header('Content-Type: application/json');
    exit(json_encode($desc));
}

$descDefault = [
    'language'           => ["en", "de"],
    'projectNature'      => '',
    'responsiblePersons' => '',
    'websiteAim'         => [
        "en" => "Diese Webseite widmet sich der Bereitstellung der aus diesem Projekt hervorgehenden Ergebnisse.",
        "de" => "This website is dedicated to providing information on the results emerging from this project."
    ],
    'copyrightNotice'    => [
        'de' => 'Diese Seite und ihre Inhalte sind, sofern nicht anders gekennzeichnet, unter der creative commons Lizenz <a href="http://creativecommons.org/licenses/by/4.0/">CC-BY 4.0</a> International lizensiert (Namensnennung).',
        'en' => 'This website and its content is, unless indicated otherwise, licensed under a creative commons <a href="http://creativecommons.org/licenses/by/4.0/">CC-BY 4.0</a> International license (Attribution).'
    ],
    'hasMatomo'          => true,
    'matomoNotice'       => [
        'de' => 'Wir weisen darauf hin, dass zum Zwecke der Systemsicherheit und der Übersicht über das Nutzungsverhalten der Besuchenden im Rahmen von Cookies diverse personenbezogene Daten (Besuchszeitraum, Betriebssystem, Browserversion, innere Auflösung des Browserfensters, Herkunft nach Land, wievielter Besuch seit Beginn der Aufzeichnung) mittels Matomo-Tracking gespeichert werden. Die Daten werden bis auf weiteres gespeichert. Soweit dies erfolgt, werden diese Daten nicht ohne Ihre ausdrückliche Zustimmung an Dritte weitergegeben.',
        'en' => 'This is a notice to indicate that for reasons of system security and overview of user behavior, personal data of users of this website (visiting period, operating system, browser version, browser resolution, country of origin, number of visits) will be stored using cookies and <a href="https://matomo.org/">Matomo tracking</a>. Data will be stored until further notice. Data will not be disseminated without your explicit consent.'
    ],
];

// if something is missing in the Redmine, fill with default values
foreach ($descDefault as $k => $v) {
  // This will check string paramaters
  if (!isset($desc[$k]) || empty($desc[$k])) {
    $desc[$k] = $v;
  }
  // This will check array paramaters with language objects inside
  if(is_array($v)) {
    foreach ($v as $j => $l) {
      if (!isset($desc[$k][$j]) || empty($desc[$k][$j])) {
        $desc[$k][$j] = $l;
      }
    }
  }
}

// In case the output is prefered in only one language
if (filter_input(INPUT_GET, 'outputLang')) {
  $outputLang = filter_input(INPUT_GET, 'outputLang', FILTER_SANITIZE_STRING);
  $desc['language'] = [$outputLang];
}

// sanitize values
if ($desc['language'] === 'both') {
    $desc['language'] = ['de', 'en'];
}
if (!is_array($desc['language'])) {
    $desc['language'] = [$desc['language']];
}

if (((bool) $desc['hasMatomo']) === false || $desc['hasMatomo'] === 'no') {
    $desc['matomoNotice'] = '';
}

foreach ($desc['language'] as $n => $lang) {
    if ($n > 0) {
        echo '<hr/>';
    }

    $tmplPath = __DIR__ . '/data/' . $lang . '.html';
    if (file_exists($tmplPath)) {
        $tmpl = file_get_contents($tmplPath);
        foreach ($desc as $k => $v) {
            if (is_array($v)) {
                $v = $v[$lang] ?? '';
            }
            $tmpl = str_replace('{' . $k . '}', $v, $tmpl);
        }
    }
    echo $tmpl;
}

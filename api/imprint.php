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
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');

$config   = (object) yaml_parse_file('config.yaml');
$id       = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$url      = $config->redmineApiUrl . '/issues/' . $id . '.json';
$curl     = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL               => $url,
    CURLOPT_FOLLOWLOCATION    => true,
    CURLOPT_HEADER            => false,
    CURLOPT_RETURNTRANSFER    => true,
    CURLOPT_UNRESTRICTED_AUTH => true,
    CURLOPT_HTTPAUTH          => CURLAUTH_BASIC,
    CURLOPT_USERPWD           => $config->user . ':' . $config->password,
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

// for Peter
if (filter_input(INPUT_GET, 'raw')){
    header('Content-Type: application/json');
    exit(json_encode($desc));
}

$descMinimal = [
    'language'           => 'both',
    'projectName'        => '',
    'projectPartners'    => '',
    'projectPurpose'     => '',
    'copyrightNotice'    => [
        'de' => 'Diese Seite und ihre Inhalte sind, sofern nicht anders gekennzeichnet, unter der creative commons Lizenz <a href="http://creativecommons.org/licenses/by/4.0/">CC-BY 4.0</a> International lizensiert (Namensnennung – Weitergabe unter gleichen Bedingungen).',
        'en' => 'This website and its content is, unless indicated otherwise, licensed under a creative commons <a href="http://creativecommons.org/licenses/by/4.0/">CC-BY 4.0</a> International license (Attribution – Share alike).'
    ],
    'hasMatomo'          => false,
];
// if something is missing in the Redmine, fill with default values
foreach ($descMinimal as $k => $v) {
    if (!isset($desc[$k]) || empty($desc[$k])) {
        $desc[$k] = $v;
    }
}
// sanitize values
if ($desc['language'] === 'both') {
    $desc['language'] = ['de', 'en'];
}
if (!is_array($desc['language'])) {
    $desc['language'] = [$desc['language']];
}

if (!empty($desc['projectPartners'])) {
    $desc['projectPartners'] = [
        'de' => ' (umgesetzt durch das ACDH in Kooperation mit <strong>' . $desc['projectPartners'] . '</strong>)',
        'en' => ' (implemented by the ACDH in cooperation with <strong>' . $desc['projectPartners'] . '</strong>)'
    ];
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

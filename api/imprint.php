<?php
  // Set header type
  header('content-type: text/html; charset=utf-8');

  include('config.php');

  // Dependencies for yaml and http fetching
  require 'vendor/autoload.php';
  use Symfony\Component\Yaml\Yaml;
  $client = new GuzzleHttp\Client();

  // Only parameter expected from the client
  $serviceID = filter_input(INPUT_GET, "serviceID", FILTER_SANITIZE_STRING);

  // Imprint params stay empty if the below request is unsuccessful
  $imprintParams = [];

  // If service id is provided
  if (!is_null($serviceID)) {
    // Request to redmine for imprint information
    $response = $client->get('https://redmine.acdh.oeaw.ac.at/issues/'.$serviceID.'.json', [
      'auth' => [
          REDMINEUSER, // Redmine username
          REDMINEPWD  // Redmine password
      ]
    ]);
    // Response status code
    $statusCode = $response->getStatusCode();
    if ($statusCode == "200") {
      // Get the imprint params from the issue
      $issueData = json_decode($response->getBody(), true);
      foreach ($issueData["issue"]["custom_fields"] as $customField) {
        if ($customField["name"] == "ImprintParams") {
          $imprintParams = $customField["value"];
          break;
        }
      }
      // Here we have the imprint params in $imprintParams array
      $imprintParams = Yaml::parse($imprintParams);
    }
  }

  // Default values for imprint
  $defaultImprintParams = array(
    "language" => ["en", "de"],
    "responsiblePersons" => [
      "en" => "",
      "de" => ""
    ],
    "projectNature" => [
      "en" => "",
      "de" => ""
    ],
    "websiteAim" => [
      "en" => "Diese Webseite widmet sich der Bereitstellung der aus diesem Projekt hervorgehenden Ergebnisse.",
      "de" => "This website is dedicated to providing information on the results emerging from this project."
    ],
    "copyrightNotice" => [
      'en' => 'This website and its content is, unless indicated otherwise, licensed under a creative commons <a href="http://creativecommons.org/licenses/by/4.0/">CC-BY 4.0</a> International license (Attribution – Share alike).',
      'de' => 'Diese Seite und ihre Inhalte sind, sofern nicht anders gekennzeichnet, unter der creative commons Lizenz <a href="http://creativecommons.org/licenses/by/4.0/">CC-BY 4.0</a> International lizensiert (Namensnennung – Weitergabe unter gleichen Bedingungen).'
    ],
    "hasMatomo" => "yes"
  );

  // Merge the default values with the provided ones
  $imprintParams = array_merge($defaultImprintParams, $imprintParams);

  // Matomo notice
  if ($imprintParams["hasMatomo"] == "yes") {
    $imprintParams["matomoNotice"] = [
      'de' => 'Wir weisen darauf hin, dass zum Zwecke der Systemsicherheit und der Übersicht über das Nutzungsverhalten der Besuchenden im Rahmen von Cookies diverse personenbezogene Daten (Besuchszeitraum, Betriebssystem, Browserversion, innere Auflösung des Browserfensters, Herkunft nach Land, wievielter Besuch seit Beginn der Aufzeichnung) mittels Matomo-Tracking gespeichert werden. Die Daten werden bis auf weiteres gespeichert. Soweit dies erfolgt, werden diese Daten nicht ohne Ihre ausdrückliche Zustimmung an Dritte weitergegeben.',
      'en' => 'This is a notice to indicate that for reasons of system security and overview of user behavior, personal data of users of this website (visiting period, operating system, browser version, browser resolution, country of origin, number of visits) will be stored using cookies and <a href="https://matomo.org/">Matomo tracking</a>. Data will be stored until further notice. Data will not be disseminated without your explicit consent.'
      ];
  } else {
    $imprintParams["matomoNotice"] = ['de' => '','en' => ''];
  }

  // Prepare the HTML output for content
  $imprint = generateImprint($imprintParams);

  // Send the response back to the client
  sendResponse(200, $imprint);

  // HTTP response function
  function sendResponse($status, $body) {
  	header("HTTP/1.1 ".$status);
  	echo $body;
  }

  // Up-to-date imprint content with added parameters
  function generateImprint($imprintParams) {
    $imprintDE = '
      <div lang="ger">
        <h2>Offenlegung gemäß §§ 24, 25 Mediengesetz und § 5 E-Commerce-Gesetz</h2>
        <h3>Medieninhaberin, Herausgeberin, inhaltliche und redaktionelle Verantwortung, Dienstanbieterin:</h3>
        <p>
            <a href="http://www.oeaw.ac.at">Österreichische Akademie der Wissenschaften</a><br/>
            Juristische Person öffentlichen Rechts (BGBl 569/1921 idF BGBl I 130/2003)<br/>
            <a href="https://acdh.oeaw.ac.at">Austrian Centre for Digital Humanities (ACDH)</a><br/>
            Dr. Ignaz Seipel-Platz 2, 1010 Wien, Österreich<br/>
            E-Mail: <a href="mailto:acdh-tech@oeaw.ac.att" style="color:black">acdh-tech@oeaw.ac.at</a>
        </p>
        <h3>Unternehmensgegenstand</h3>
        <p>Die Österreichische Akademie der Wissenschaften (ÖAW) hat den gesetzlichen Auftrag, die Wissenschaft in jeder Hinsicht zu fördern. Als Gelehrtengesellschaft pflegt die ÖAW den Diskurs und die Zusammenarbeit der Wissenschaft mit Öffentlichkeit, Politik und Wirtschaft.<br/>
            Das Austrian Centre for Digital Humanities (ACDH) ist ein Institut der ÖAW, das mit dem Ziel gegründet wurde, digitale Methoden und Ansätze in den geisteswissenschaftlichen Disziplinen zu fördern. Das ACDH unterstützt digitale Forschung in vielfältiger Weise.<br/>
            '.$imprintParams['projectNature']['de'].'
        </p>
        <h3>Vertretungsbefugte Organe:</h3>
        <p>
            Präsident: Univ.-Prof. Dr. Anton Zeilinger<br/>
            Vizepräsident: Univ.-Doz. Dr. phil. Michael Alram<br/>
            Klassenpräsident: Univ.-Prof. Dr. phil. Oliver Jens Schmitt, <br/>
            Klassenpräsident: Univ.-Prof. Dipl.-Ing. Dr.techn. Georg Brasseur<br/>
            Als Aufsichtsorgan besteht der Akademierat. Siehe mehr dazu unter <a href="http://www.oeaw.ac.at/die-oeaw/gremien-der-oeaw/akademierat/">http://www.oeaw.ac.at/die-oeaw/gremien-der-oeaw/akademierat/</a>
            '.$imprintParams['responsiblePersons']['de'].'
        </p>
        <h3>Grundlegende Richtung:</h3>
        <p>'.$imprintParams['websiteAim']['de'].'</p>
        <h3>Haftungsausschluss</h3>
        <p>Die Österreichische Akademie der Wissenschaften übernimmt keinerlei Gewähr für die Aktualität, Korrektheit, Vollständigkeit oder Qualität der bereitgestellten Informationen.<br/>
            Im Falle des Bestehens von Links auf Webseiten anderer Medieninhaber, für deren Inhalt die ÖAW weder direkt oder indirekt mitverantwortlich ist, übernimmt die ÖAW keine Haftung für deren Inhalte und schließt jegliche Haftung hierfür aus.
        </p>
        <h3>Urheberrechtlicher Hinweis:</h3>
        <p>'.$imprintParams['copyrightNotice']['de'].'</p>
        <h3>Datenschutzrechtlicher Hinweis:</h3>
        <p>'.$imprintParams['matomoNotice']['de'].'<br/>
            Durch die Nutzung der Website erklären Sie sich mit der Art und Weise sowie dem Zweck der Datenverarbeitung einverstanden. Durch eine entsprechende Einstellung in Ihrem Browser können Sie die Speicherung der Cookies verhindern. In diesem Fall stehen Ihnen aber gegebenenfalls nicht alle Funktionen der Website zur Verfügung. <br/>
            Die ausführliche Datenschutzerklärung der ÖAW finden Sie <a href="https://www.oeaw.ac.at/die-oeaw/datenschutz/">hier</a>.
            Die im Rahmen der Impressumspflicht veröffentlichten Kontaktdaten dürfen von Dritten nicht zur Übersendung von nicht ausdrücklich angeforderter Werbung und Informationsmaterialien verwendet werden. Einer derartigen Verwendung wird hiermit ausdrücklich widersprochen.
        </p>
    </div>';

    $imprintEN = '
      <div lang="en">
        <h2>Legal disclosure according to §§ 24, 25 Austrian media law and § 5 E-Commerce law</h2>
        <h3>Media owner, publisher, responsible for content and editorial office, service provider:</h3>
        <p>
            <a href="http://www.oeaw.ac.at">Austrian Academy of Sciences</a><br/>
            Corporate body organized under public law (BGBl 569/1921 idF BGBl I 130/2003)<br/>
            <a href="https://acdh.oeaw.ac.at">Austrian Centre for Digital Humanities (ACDH)</a><br/>
            Dr. Ignaz Seipel-Platz 2, 1010 Vienna, Austria<br/>
            E-Mail: <a href="mailto:acdh-tech@oeaw.ac.att" style="color:black">acdh-tech@oeaw.ac.at</a>
        </p>
        <h3>Nature and purpose of the business:</h3>
        <p>
            The Austrian Academy of Sciences (OEAW) has the legal duty to support the sciences and humanities in every respect. As a learned society, the OEAW fosters discourse and cooperation between science and society, politics and economy.<br/>
            The Austrian Centre for Digital Humanities (ACDH) is an OEAW institute founded with the goal to support digital methods in arts and humanities disciplines. The ACDH supports digital research in manifold ways.<br/>
            '.$imprintParams['projectNature']['en'].'
        </p>
        <h3>Signing power:</h3>
        <p>
            President: Univ.-Prof. Dr. Anton Zeilinger<br/>
            Vice president: Univ.-Doz. Dr. phil. Michael Alram<br/>
            Class presidents: Univ.-Prof. Dr. phil. Oliver Jens Schmitt, Univ.-Prof. Dipl.-Ing. Dr.techn. Georg Brasseur<br/>
            Supervisory body:  Academy council. For more information, please visit <a href="http://www.oeaw.ac.at/die-oeaw/gremien-der-oeaw/akademierat/">http://www.oeaw.ac.at/die-oeaw/gremien-der-oeaw/akademierat/</a>
            '.$imprintParams['responsiblePersons']['en'].'
        </p>
        <h3>Main aim:</h3>
        <p>
        '.$imprintParams['websiteAim']['en'].'
        </p>
        <h3>Disclaimer:</h3>
        <p>
            The Austrian Academy of Sciences does not take responsibility for the nature, accuracy, entirety or quality of the provided information.<br/>
            In the case of links to websites of other media owners, whose content the OEAW is neither directly nor indirectly responsible for, the OEAW does not assume liability for their content and excludes any liability in this case.
        </p>
        <h3>Copyright notice:</h3>
        <p>'.$imprintParams['copyrightNotice']['en'].'</p>
        <h3>Data privacy notice:</h3>
        <p>'.$imprintParams['matomoNotice']['en'].'<br/>
            By using this website, you agree to the manner and purposes of data processing. You can disable cookies in your browser settings. However, this might limit functionality of this website.<br/>
            Please find the ÖAW\'s detailed data privacy statement <a href="https://www.oeaw.ac.at/die-oeaw/datenschutz/">here</a>.
            The contact data published in the context of the imprint duty may not be used to send promotional or informational material not explicitly requested. We explicitly disagree with this usage.
        </p>
      </div>';

    if ($imprintParams['language'] == ["en", "de"]) {
      return $imprintDE . '<hr/>' . $imprintEN;
    } else if ($imprintParams['language'] == ['de']) {
      return $imprintDE;
    } else if ($imprintParams['language'] == ['en']) {
      return $imprintEN;
    } else {
      return 'Requested language for the content is not supported.';
    }

  }

?>
<?php
  // Set header type
  header('content-type: text/html; charset=utf-8');

  // Handle the request and parameters
  // Impring language, parameters 'de', 'en' or 'both' are accepted 
  $language = filter_input(INPUT_GET, "language", FILTER_SANITIZE_STRING);
    if (is_null($projectName)) $language = "both";
  // Project name, if empty the imprint text will only say "the project"
  $projectName = filter_input(INPUT_GET, "projectName", FILTER_SANITIZE_STRING);
  // References to project partners
  $projectPartners = filter_input(INPUT_GET, "projectPartners", FILTER_SANITIZE_STRING);
    if (!is_null($projectPartners)) $projectPartners = [
      'de' => ' (umgesetzt durch das ACDH in Kooperation mit <strong>'.$projectPartners.'</strong>)',
      'en' => ' (implemented by the ACDH in cooperation with <strong>'.$projectPartners.'</strong>)'
    ]
  // Purpose of the project
  $projectPurpose = filter_input(INPUT_GET, "projectPurpose", FILTER_SANITIZE_STRING);
    if (!is_null($projectPurpose)) $projectPurpose = [
      'de' => 'der Bereitstellung der aus diesem Projekt hervorgehenden Ergebnisse.',
      'en' => 'providing information on the results emerging from this project.'
    ];
  // Copyright notice, should be an array as following: ['de' => 'text', 'en' => 'text']
  $copyrightNotice = filter_input(INPUT_GET, "copyrightNotice", FILTER_SANITIZE_STRING);
    if (is_null($copyrightNotice)) $copyrightNotice = [
      'de' => 'Diese Seite und ihre Inhalte sind, sofern nicht anders gekennzeichnet, unter der creative commons Lizenz <a href="http://creativecommons.org/licenses/by-sa/4.0/">CC-BY-SA 4.0</a> International lizensiert (Namensnennung – Weitergabe unter gleichen Bedingungen).',
      'en' => 'This website and its content is, unless indicated otherwise, licensed under a creative commons <a href="http://creativecommons.org/licenses/by-sa/4.0/">CC-BY-SA 4.0</a> International license (Attribution – Share alike).'
    ];
  // Piwik trackting, true or false
  $hasPiwik = filter_input(INPUT_GET, "hasPiwik", FILTER_SANITIZE_STRING);
    if (is_null($hasPiwik)) $hasPiwik = true;
    if ($hasPiwik) {
      $piwikNotice = [
        'de' => 'Wir weisen darauf hin, dass zum Zwecke der Systemsicherheit und der Übersicht über das Nutzungsverhalten der Besuchenden im Rahmen von Cookies diverse personenbezogene Daten (Besuchszeitraum, Betriebssystem, Browserversion, innere Auflösung des Browserfensters, Herkunft nach Land, wievielter Besuch seit Beginn der Aufzeichnung) mittels Piwik-Tracking gespeichert werden. Die Daten werden bis auf weiteres gespeichert. Soweit dies erfolgt, werden diese Daten nicht ohne Ihre ausdrückliche Zustimmung an Dritte weitergegeben.',
        'en' => 'This is a notice to indicate that for reasons of system security and overview of user behavior, personal data of users of this website (visiting period, operating system, browser version, browser resolution, country of origin, number of visits) will be stored using cookies and <a href="http://piwik.org/">piwik tracking</a>. Data will be stored until further notice. Data will not be disseminated without your explicit consent.'
      ]
    }

  // Prepare the HTML output for content
  $imprint = generateImprint($projectName);

  // Send the response back to the client
  sendResponse(200, $imprint);

  // HTTP response function
  function sendResponse($status, $body) {
  	header("HTTP/1.1 ".$status);
  	echo $body;
  }

  // Up-to-date imprint content with added parameters
  function generateImprint($projectName) {
    $imprintDE = '
      <div lang="ger">
        <h2>Offenlegung gemäß §§ 24, 25 Mediengesetz und § 5 E-Commerce-Gesetz</h2>
        <h2>Medieninhaberin, Herausgeberin, inhaltliche und redaktionelle Verantwortung, Dienstanbieterin:</h2>
        <p>
            <a href="http://www.oeaw.ac.at">Österreichische Akademie der Wissenschaften</a><br/>
            Juristische Person öffentlichen Rechts (BGBl 569/1921 idF BGBl I 130/2003)<br/>
            <a href="https://acdh.oeaw.ac.at">Austrian Centre for Digital Humanities (ACDH)</a><br/>
            Dr. Ignaz Seipel-Platz 2, 1010 Wien, Österreich<br/>
            E-Mail: <a href="mailto:acdh-tech@oeaw.ac.att" style="color:black">acdh-tech@oeaw.ac.at</a>
        </p>
        <h2>Unternehmensgegenstand</h2>
        <p>Die Österreichische Akademie der Wissenschaften (ÖAW) hat den gesetzlichen Auftrag, die Wissenschaft in jeder Hinsicht zu fördern. Als Gelehrtengesellschaft pflegt die ÖAW den Diskurs und die Zusammenarbeit der Wissenschaft mit Öffentlichkeit, Politik und Wirtschaft.<br/>
            Das Austrian Centre for Digital Humanities (ACDH) ist ein Institut der ÖAW, das mit dem Ziel gegründet wurde, digitale Methoden und Ansätze in den geisteswissenschaftlichen Disziplinen zu fördern. Das ACDH unterstützt digitale Forschung in vielfältiger Weise.<br/>
            Das Projekt <strong>'.$projectName.'</strong>'.$projectPartners.' widmet sich <strong>'.$projectPurpose.'</strong>
        </p>
        <h2>Vertretungsbefugte Organe:</h2>
        <p>
            Präsident: Univ.-Prof. Dr. Anton Zeilinger<br/>
            Vizepräsident: Univ.-Doz. Dr. phil. Michael Alram<br/>
            Klassenpräsidentin: Univ.-Prof. Dr. Brigitte Mazohl, <br/>
            Klassenpräsident: Univ.-Prof. Dipl.-Ing. Dr.techn. Georg Brasseur<br/>
            Als Aufsichtsorgan besteht der Akademierat. Siehe mehr dazu unter <a href="http://www.oeaw.ac.at/die-oeaw/gremien-der-oeaw/akademierat/">http://www.oeaw.ac.at/die-oeaw/gremien-der-oeaw/akademierat/</a>
        </p>
        <h2>Grundlegende Richtung:</h2>
        <p>Diese Website dient der Information über das Projekt <strong>'.$projectName.'</strong> und der Bereitstellung der aus diesem Projekt hervorgehenden Forschungsdaten und -ergebnisse.</p>
        <h2>Haftungsausschluss</h2>
        <p>Die Österreichische Akademie der Wissenschaften übernimmt keinerlei Gewähr für die Aktualität, Korrektheit, Vollständigkeit oder Qualität der bereitgestellten Informationen.<br/>
            Im Falle des Bestehens von Links auf Webseiten anderer Medieninhaber, für deren Inhalt die ÖAW weder direkt oder indirekt mitverantwortlich ist, übernimmt die ÖAW keine Haftung für deren Inhalte und schließt jegliche Haftung hierfür aus.
        </p>
        <h2>Urheberrechtlicher Hinweis:</h2>
        <p>'.$copyrightNotice['de'].'</p>
        <h2>Datenschutzrechtlicher Hinweis:</h2>
        <p>'.$piwikNotice['de'].'<br/>
            Durch die Nutzung der Website erklären Sie sich mit der Art und Weise sowie dem Zweck der Datenverarbeitung einverstanden. Durch eine entsprechende Einstellung in Ihrem Browser können Sie die Speicherung der Cookies verhindern. In diesem Fall stehen Ihnen aber gegebenenfalls nicht alle Funktionen der Website zur Verfügung. <br/>
            Die ausführliche Datenschutzerklärung der ÖAW finden Sie <a href="https://www.oeaw.ac.at/die-oeaw/datenschutz/">hier</a>.
            Die im Rahmen der Impressumspflicht veröffentlichten Kontaktdaten dürfen von Dritten nicht zur Übersendung von nicht ausdrücklich angeforderter Werbung und Informationsmaterialien verwendet werden. Einer derartigen Verwendung wird hiermit ausdrücklich widersprochen.
        </p>
    </div>';

    $imprintEN = '
      <div lang="ger">
        <h1>Legal disclosure according to §§ 24, 25 Austrian media law and § 5 E-Commerce law</h1>
        <h2>Media owner, publisher, responsible for content and editorial office, service provider:</h2>
        <p>
            <a href="http://www.oeaw.ac.at">Austrian Academy of Sciences</a><br/>
            Corporate body organized under public law (BGBl 569/1921 idF BGBl I 130/2003)<br/>
            <a href="https://acdh.oeaw.ac.at">Austrian Centre for Digital Humanities (ACDH)</a><br/>
            Dr. Ignaz Seipel-Platz 2, 1010 Vienna, Austria<br/>
            E-Mail: <a href="mailto:acdh-tech@oeaw.ac.att" style="color:black">acdh-tech@oeaw.ac.at</a>
        </p>
        <h2>Nature and purpose of the business:</h2>
        <p>
            The Austrian Academy of Sciences (OEAW) has the legal duty to support the sciences and humanities in every respect. As a learned society, the OEAW fosters discourse and cooperation between science and society, politics and economy.<br/>
            The Austrian Centre for Digital Humanities (ACDH) is an OEAW institute founded with the goal to support digital methods in arts and humanities disciplines. The ACDH supports digital research in manifold ways.<br/>
            The project <strong>'.$projectName.'</strong>'.$projectPartners.' is dedicated to <strong>'.$projectPurpose.'</strong>
        </p>
        <h2>Signing power:</h2>
        <p>
            President: Univ.-Prof. Dr. Anton Zeilinger<br/>
            Vice president: Univ.-Doz. Dr. phil. Michael Alram<br/>
            Class presidents: Univ.-Prof. Dr. Brigitte Mazohl, Univ.-Prof. Dipl.-Ing. Dr.techn. Georg Brasseur<br/>
            Supervisory body:  Academy council. For more information, please visit <a href="http://www.oeaw.ac.at/die-oeaw/gremien-der-oeaw/akademierat/">http://www.oeaw.ac.at/die-oeaw/gremien-der-oeaw/akademierat/</a>
        </p>
        <h2>Main aim:</h2>
        <p>
            This website provides information on the project <strong>'.$projectName.'</strong> as well as the data and results emerging from this project.
        </p>
        <h2>Disclaimer:</h2>
        <p>
            The Austrian Academy of Sciences does not take responsibility for the nature, accuracy, entirety or quality of the provided information.<br/>
            In the case of links to websites of other media owners, whose content the OEAW is neither directly nor indirectly responsible for, the OEAW does not assume liability for their content and excludes any liability in this case.
        </p>
        <h2>Copyright notice:</h2>
        <p>'.$copyrightNotice['en'].'</p>
        <h2>Data privacy notice:</h2>
        <p>'.$piwikNotice['en'].'<br/>
            By using this website, you agree to the manner and purposes of data processing. You can disable cookies in your browser settings. However, this might limit functionality of this website.<br/>
            Please find the ÖAW\'s detailed data privacy statement <a href="https://www.oeaw.ac.at/die-oeaw/datenschutz/">here</a>.
            The contact data published in the context of the imprint duty may not be used to send promotional or informational material not explicitly requested. We explicitly disagree with this usage. 
        </p>
      </div>';

    if ($language == 'both') {
      return $imprintDE . $imprintEN;
    } else if ($language == 'de') {
      return $imprintDE;
    } else if ($language == 'en') {
      return $imprintEN;
    } else {
      return 'Requested language for the content is not supported.';
    }

  }

?>
<?php
  // Set header type
  header('content-type: text/html; charset=utf-8');

  // Handle the request and parameters
  $projectName = filter_input(INPUT_GET, "projectName", FILTER_SANITIZE_STRING);
  if (is_null($projectName)) $projectName = "ACDH";
  // TODO: Add other relevant paramenters here

  // Prepare the HTML output for content
  $imprint = generateImprint($projectName);

  // Send the response back to the client
  response(200, $imprint);

  // HTTP response function
  function response($status, $body) {
  	header("HTTP/1.1 ".$status);
  	echo $body;
  }

  // Up-to-date imprint content with added parameters
  function generateImprint($projectName) {
    $imprint = '<p>Legal disclosure according to §§ 24, 25 Austrian media law and § 5 E-Commerce law</p>

    <h3>Media owner, publisher, responsible for content and editorial office, service provider:</h3>
    
    <p>Austrian Academy of Sciences<br>
    Corporate body organized under public law (BGBl 569/1921 idF BGBl I 130/2003)<br>
    Austrian Centre for Digital Humanities (ACDH)<br>
    Dr. Ignaz Seipel-Platz 2, 1010 Vienna, Austria<br>
    E-Mail: acdh-tech@oeaw.ac.at</p>
    
    <h3>Nature and purpose of the business:</h3>
    
    <p>The Austrian Academy of Sciences (OEAW) has the legal duty to support the sciences and humanities in every respect. As a learned society, the OEAW fosters discourse and cooperation between science and society, politics and economy.<br>
    The Austrian Centre for Digital Humanities (ACDH) is an OEAW institute founded with the goal to support digital methods in arts and humanities disciplines. The ACDH supports digital research in manifold ways. A Resource Centre for the HumanitiEs (ARCHE) is one of the central services of the ACDH.</p>
    
    <h3>Signing power:</h3>
    
    <p>President: Univ.-Prof. Dr. Anton Zeilinger<br>
    Vice president: Univ.-Doz. Dr. phil. Michael Alram<br>
    Class presidents: Univ.-Prof. Dr. Brigitte Mazohl, Univ.-Prof. Dipl.-Ing. Dr.techn. Georg Brasseur<br>
    Supervisory body: Academy council.<br>
    For more information, please visit <a href="http://www.oeaw.ac.at/die-oeaw/gremien-der-oeaw/akademierat/">http://www.oeaw.ac.at/die-oeaw/gremien-der-oeaw/akademierat/</a></p>
    
    <h3>Main aim:</h3>
    
    <p>'.$projectName.' offers stable access to digital research data for the Austrian humanities community.</p>
    
    <h3>Disclaimer:</h3>
    
    <p>The Austrian Academy of Sciences does not take responsibility for the nature, accuracy, entirety or quality of the provided information and the data hosted in this repository.<br>
    In the case of links to websites of other media owners, whose content the OEAW is neither directly nor indirectly responsible for, the OEAW does not assume liability for their content and excludes any liability in this case.</p>
    
    <h3>Copyright notice:</h3>
    
    <p>This website and its content is, unless indicated otherwise, licensed under a creative commons <a href="https://creativecommons.org/licenses/by/4.0">CC-BY 4.0 International license</a> (Attribution).</p>
    
    <h3>Data privacy notice:</h3>
    
    <p>This is a notice to indicate that for reasons of system security and overview of user behavior, personal data of users of this website (visiting period, operating system, browser version, browser resolution, country of origin, number of visits) will be stored using cookies and <a href="/browser/privacy#privacy-piwik">Piwik tracking</a>. Data will be stored until further notice. Data will not be disseminated without your explicit consent.<br>
    By using this website, you agree to the manner and purposes of data processing. You can disable cookies in your browser settings. However, this might limit functionality of this website.<br>
    Further information is detailed in the <a href="/browser/privacy">Privacy Policy</a>.<br>
    The contact data published in the context of the imprint duty may not be used to send promotional or informational material not explicitly requested. We explicitly disagree with this usage.</p>';

    return $imprint;
  }

?>
# acdh-common-assets
Common web assets and templates for the Austrian Centre for Digital Humanities such as Imprint and HTTP error pages.

## Imprint Service Installation
1. Duplicate the api/config-sample.php file and rename it to config.php
2. Inside the api/config.php type in the credentials of an autherized redmine user.
3. Inside the api/ install the composer libraries:
```
composer install
```
4. Make a request to the imprint service with service issue id from redmine:
```
https://shared.acdh.oeaw.ac.at/acdh-common-assets/api/imprint.php?serviceID=9945
```
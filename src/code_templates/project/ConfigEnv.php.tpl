<?php

ini_set('display_errors', 1);
ini_set("error_reporting", E_ALL ^ E_DEPRECATED);

$config['sessionSalt'] = '{{ sessionSalt }}';

$config['accountFullName'] = '{{ accountFullName }}';

// dirs

$config['srcFolder'] = '{{ srcFolder }}';
$config['rootFolder'] = '{{ rootFolder }}';
$config['logFolder'] = '{{ rootFolder }}/log';
$config['uploadFolder'] = '{{ rootFolder }}/upload';

// urls
$config['rewriteBase'] = "{{ rewriteBase }}";
$config['srcUrl'] = '{{ srcUrl }}';
$config['rootUrl'] = '{{ rootUrl }}';
$config['uploadUrl'] = '{{ rootUrl }}/upload';

// db
$config['db_host'] = '{{ dbHost }}';
$config['db_user'] = '{{ dbUser }}';
$config['db_password'] = '{{ dbPassword }}';
$config['db_name'] = '{{ dbName }}';
$config['db_codepage'] = 'utf8mb4';
$config['global_table_prefix'] = '';

// smtp
$config['smtpHost'] = '{{ smtpHost }}';
$config['smtpPort'] = '{{ smtpPort }}';
$config['smtpEncryption'] = '{{ smtpEncryption }}';
$config['smtpLogin'] = '{{ smtpLogin }}';
$config['smtpPassword'] = '{{ smtpPassword }}';

// misc
$config['develMode'] = TRUE;
$config['language'] = 'en';
$config['premiumRepoFolder'] = '{{ premiumRepoFolder }}';
$config['externalAppsRepositories'] = [
  'MyCompany' => __DIR__ . '/apps/external/MyCompany'
];

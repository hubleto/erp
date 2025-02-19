<?php

ini_set('display_errors', 1);
ini_set("error_reporting", E_ALL ^ E_DEPRECATED);

// dirs

const HUBLETO_COMMUNITY_REPO = '{{ mainFolder }}/apps/community';
const HUBLETO_EXTERNAL_REPO = '{{ mainFolder }}/apps/external';
const HUBLETO_ENTERPRISE_REPO = '{{ mainFolder }}/apps/enterprise';

$config['dir'] = '{{ mainFolder }}';
$config['accountDir'] = __DIR__;
$config['logDir'] = __DIR__ . '/log';
$config['tmpDir'] = __DIR__ . '/tmp';
$config['uploadDir'] = __DIR__ . '/upload';

// urls
$config['rewriteBase'] = "{{ rewriteBase }}";
$config['url'] = '{{ mainUrl }}';
$config['accountUrl'] = '{{ accountUrl }}';
$config['uploadUrl'] = '{{ accountUrl }}/upload';

// db
$config['db_host'] = '{{ dbHost }}';
$config['db_user'] = '{{ dbUser }}';
$config['db_password'] = '{{ dbPassword }}';
$config['db_name'] = '{{ dbName }}';
$config['db_codepage'] = 'utf8mb4';
$config['global_table_prefix'] = '';

// misc
$config['develMode'] = TRUE;
$config['language'] = 'en';
$config['externalAppsRepositories'] = [
  'MyCompany' => __DIR__ . '/apps/external/MyCompany'
];

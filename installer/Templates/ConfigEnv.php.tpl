<?php

$config['release'] = trim(@file_get_contents('release')) ?? '';

ini_set('display_errors', 1);
ini_set("error_reporting", E_ALL ^ E_DEPRECATED);

$config['sessionSalt'] = '{{ sessionSalt }}';

$config['accountUid'] = '{{ accountUid }}';
$config['accountFullName'] = '{{ accountFullName }}';

// dirs

$config['projectFolder'] = __DIR__;
$config['releaseFolder'] = "{{ releaseFolder }}";
$config['logFolder'] = __DIR__ . '/log';
$config['uploadFolder'] = __DIR__ . '/upload';

// urls
$config['rewriteBase'] = "{{ rewriteBase }}";
$config['projectUrl'] = '{{ projectUrl }}';
$config['uploadUrl'] = '{{ projectUrl }}/upload';
$config['assetsUrl'] = '{{ assetsUrl }}';

if (is_dir(__DIR__ . '/assets')) $config['assetsCompiledUrl'] = '{{ projectUrl }}/assets/compiled';
else $config['assetsCompiledUrl'] = '{{ projectUrl }}/vendor/hubleto/assets/compiled';

// sanitize dirs and urls based on used release
$config['releaseFolder'] = str_replace('__RELEASE__', $config['release'], $config['releaseFolder']);
$config['assetsUrl'] = str_replace('__RELEASE__', $config['release'], $config['assetsUrl']);
$config['assetsCompiledUrl'] = str_replace('__RELEASE__', $config['release'], $config['assetsCompiledUrl']);

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
$config['language'] = '{{ language }}';
$config['enterpriseAppsRepository'] = '{{ enterpriseAppsRepository }}';
$config['externalAppsRepositories'] = [
  'MyCompany' => __DIR__ . '/apps/external/MyCompany'
];

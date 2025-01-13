<?php

// dirs

const HUBLETO_COMMUNITY_REPO = '{{ mainRootFolder }}/apps/community';
const HUBLETO_EXTERNAL_REPO = '{{ mainRootFolder }}/apps/external';
const HUBLETO_ENTERPRISE_REPO = '{{ mainRootFolder }}/apps/enterprise';

$config['dir'] = '{{ mainRootFolder }}';
$config['accountDir'] = __DIR__;
$config['logDir'] = __DIR__ . '/log';
$config['tmpDir'] = __DIR__ . '/tmp';
$config['uploadDir'] = __DIR__ . '/upload';

// urls
$config['rewriteBase'] = "{{ rewriteBase }}";
$config['url'] = '{{ mainRootUrl }}';
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


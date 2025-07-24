<?php

namespace HubletoApp\Community\Settings;

class Loader extends \Hubleto\Framework\App
{
  public bool $canBeDisabled = false;

  /** @var array<int, array<\Hubleto\Framework\App, array>> */
  private array $settings = [];

  /** @var array<int, array<\Hubleto\Framework\App, array>> */
  private array $tools = [];

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^settings\/?$/' => Controllers\Dashboard::class,
      '/^settings\/my-account\/?$/' => Controllers\MyAccount::class,
      '/^settings\/apps\/?$/' => Controllers\Apps::class,
      '/^settings\/users\/?$/' => Controllers\Users::class,
      '/^settings\/user-roles\/?$/' => Controllers\UserRoles::class,
      '/^settings\/companies\/?$/' => Controllers\Companies::class,
      '/^settings\/general\/?$/' => Controllers\General::class,
      '/^settings\/activity-types\/?$/' => Controllers\ActivityTypes::class,
      '/^settings\/countries\/?$/' => Controllers\Countries::class,
      '/^settings\/currencies\/?$/' => Controllers\Currencies::class,
      '/^settings\/sidebar\/?$/' => Controllers\Sidebar::class,
      '/^settings\/permissions\/?$/' => Controllers\Permissions::class,
      '/^settings\/role-permissions\/?$/' => Controllers\RolePermissions::class,
      '/^settings\/teams\/?$/' => Controllers\Teams::class,
      '/^settings\/invoice-profiles\/?$/' => Controllers\InvoiceProfiles::class,
      '/^settings\/theme\/?$/' => Controllers\Theme::class,
      '/^settings\/config\/?$/' => Controllers\Config::class,
      '/^settings\/get-permissions\/?$/' => Controllers\Api\GetPermissions::class,
      '/^settings\/save-permissions\/?$/' => Controllers\Api\SavePermissions::class,
      '/^settings\/update-default-permissions\/?$/' => Controllers\UpdateDefaultPermissions::class,
    ]);

    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Users'), 'icon' => 'fas fa-user', 'url' => 'settings/users']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Roles'), 'icon' => 'fas fa-user-group', 'url' => 'settings/user-roles']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Your companies'), 'icon' => 'fas fa-id-card', 'url' => 'settings/companies']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('General settings'), 'icon' => 'fas fa-cog', 'url' => 'settings/general']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Permissions list'), 'icon' => 'fas fa-shield-halved', 'url' => 'settings/permissions']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Role permissions'), 'icon' => 'fas fa-user-shield', 'url' => 'settings/role-permissions']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Activity types'), 'icon' => 'fas fa-layer-group', 'url' => 'settings/activity-types']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Countries'), 'icon' => 'fas fa-globe', 'url' => 'settings/countries']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Currencies'), 'icon' => 'fas fa-dollar-sign', 'url' => 'settings/currencies']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Invoice profiles'), 'icon' => 'fas fa-user-tie', 'url' => 'settings/invoice-profiles']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Platform config'), 'icon' => 'fas fa-hammer', 'url' => 'settings/config']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Sidebar'), 'icon' => 'fas fa-bars', 'url' => 'settings/sidebar']);
    $this->main->apps->community('Settings')->addSetting($this, ['title' => $this->translate('Teams'), 'icon' => 'fas fa-users', 'url' => 'settings/teams']);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mCompany = $this->main->di->create(Models\Company::class);
      $mUser = $this->main->di->create(Models\User::class);
      $mUserRole = $this->main->di->create(Models\UserRole::class);
      $mUserHasRole = $this->main->di->create(Models\UserHasRole::class);
      $mPermission = $this->main->di->create(Models\Permission::class);
      $mRolePermission = $this->main->di->create(Models\RolePermission::class);
      $mCountry = $this->main->di->create(Models\Country::class);
      $mSetting = $this->main->di->create(Models\Setting::class);
      $mActivityTypes = $this->main->di->create(Models\ActivityType::class);
      $mCurrency = $this->main->di->create(Models\Currency::class);
      $mInvoiceProfile = $this->main->di->create(Models\InvoiceProfile::class);
      $mTeam = $this->main->di->create(Models\Team::class);
      $mTeamMember = $this->main->di->create(Models\TeamMember::class);

      $mCompany->dropTableIfExists()->install();
      $mUser->dropTableIfExists()->install();
      $mUserRole->dropTableIfExists()->install();
      $mUserHasRole->dropTableIfExists()->install();
      $mPermission->dropTableIfExists()->install();
      $mRolePermission->dropTableIfExists()->install();
      $mCountry->dropTableIfExists()->install();
      $mSetting->dropTableIfExists()->install();
      $mActivityTypes->dropTableIfExists()->install();
      $mCurrency->dropTableIfExists()->install();
      $mInvoiceProfile->dropTableIfExists()->install();
      $mTeam->dropTableIfExists()->install();
      $mTeamMember->dropTableIfExists()->install();

      $mSetting->record->recordCreate([
        'key' => 'Apps\Community\Settings\Currency\DefaultCurrency',
        'value' => '1',
        'id_owner' => null
      ]);

      $mCurrency->record->recordCreate([ 'name' => 'EURO', 'code' => 'EUR', 'symbol' => '€' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Pound Sterling', 'code' => 'GBP', 'symbol' => '£' ]);
      $mCurrency->record->recordCreate([ 'name' => 'US Dollar', 'code' => 'USD', 'symbol' => '$' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Canadian Dollar', 'code' => 'CAD', 'symbol' => 'CA$' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Mexican Peso', 'code' => 'MXN', 'symbol' => 'MX$' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Brazilian Real', 'code' => 'BRL', 'symbol' => 'R$' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Japanese Yen', 'code' => 'JPY', 'symbol' => '¥' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Chinese Yuan', 'code' => 'CNY', 'symbol' => 'CN¥' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Australian Dollar', 'code' => 'AUD', 'symbol' => 'AU$' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Saudi Riyal', 'code' => 'SAR', 'symbol' => 'SAR' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Polish Złoty', 'code' => 'PLZ', 'symbol' => 'zł' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Czech Koruna', 'code' => 'CZK', 'symbol' => 'Kč' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Hungarian Forint', 'code' => 'HUF', 'symbol' => 'Ft' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Swiss Franc', 'code' => 'CHF', 'symbol' => 'SFr.' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Danish Krone', 'code' => 'DKK', 'symbol' => 'kr.' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Serbian Dinar', 'code' => 'RSD', 'symbol' => 'РСД' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Norwegian Krone', 'code' => 'NOK', 'symbol' => 'kr' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Swedish Krona', 'code' => 'SEK', 'symbol' => 'kr' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Belarusian Ruble', 'code' => 'BYN ', 'symbol' => '₽' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Russian Ruble', 'code' => 'RUB', 'symbol' => '₽' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Romanian Leu', 'code' => 'RON', 'symbol' => 'lei' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Turkish Lira', 'code' => 'TRY', 'symbol' => '₺' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Bosnia-Herzegovina Convertible Marka', 'code' => 'BAM', 'symbol' => 'KM' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Argentine Peso', 'code' => 'ARS', 'symbol' => '$' ]);
      $mCurrency->record->recordCreate([ 'name' => 'New Zealand Dollar', 'code' => 'NZD', 'symbol' => 'NZ$' ]);

      $mActivityTypes->record->recordCreate([ 'name' => 'Meeting', 'color' => '#607d8b', 'calendar_visibility' => true ]);
      $mActivityTypes->record->recordCreate([ 'name' => 'Bussiness Trip', 'color' => '#673ab7', 'calendar_visibility' => true ]);
      $mActivityTypes->record->recordCreate([ 'name' => 'Call', 'color' => '#348789', 'calendar_visibility' => true ]);
      $mActivityTypes->record->recordCreate([ 'name' => 'Email', 'color' => '#3f51b5', 'calendar_visibility' => true ]);
      $mActivityTypes->record->recordCreate([ 'name' => 'Other', 'color' => '#91133e', 'calendar_visibility' => true ]);

      $countries = [
        [1, 'Aruba', 'ABW'],
        [2, 'Afghanistan', 'AFG'],
        [3, 'Angola', 'AGO'],
        [4, 'Anguilla', 'AIA'],
        [5, 'Åland Islands', 'ALA'],
        [6, 'Albania', 'ALB'],
        [7, 'Andorra', 'AND'],
        [8, 'United Arab Emirates (the)', 'ARE'],
        [9, 'Argentina', 'ARG'],
        [10, 'Armenia', 'ARM'],
        [11, 'American Samoa', 'ASM'],
        [12, 'Antarctica', 'ATA'],
        [13, 'French Southern Territories (the)', 'ATF'],
        [14, 'Antigua and Barbuda', 'ATG'],
        [15, 'Australia', 'AUS'],
        [16, 'Austria', 'AUT'],
        [17, 'Azerbaijan', 'AZE'],
        [18, 'Burundi', 'BDI'],
        [19, 'Belgium', 'BEL'],
        [20, 'Benin', 'BEN'],
        [21, 'Bonaire, Sint Eustatius and Saba', 'BES'],
        [22, 'Burkina Faso', 'BFA'],
        [23, 'Bangladesh', 'BGD'],
        [24, 'Bulgaria', 'BGR'],
        [25, 'Bahrain', 'BHR'],
        [26, 'Bahamas (the)', 'BHS'],
        [27, 'Bosnia and Herzegovina', 'BIH'],
        [28, 'Saint Barthélemy', 'BLM'],
        [29, 'Belarus', 'BLR'],
        [30, 'Belize', 'BLZ'],
        [31, 'Bermuda', 'BMU'],
        [32, 'Bolivia (Plurinational State of)', 'BOL'],
        [33, 'Brazil', 'BRA'],
        [34, 'Barbados', 'BRB'],
        [35, 'Brunei Darussalam', 'BRN'],
        [36, 'Bhutan', 'BTN'],
        [37, 'Bouvet Island', 'BVT'],
        [38, 'Botswana', 'BWA'],
        [39, 'Central African Republic (the)', 'CAF'],
        [40, 'Canada', 'CAN'],
        [41, 'Cocos (Keeling) Islands (the)', 'CCK'],
        [42, 'Switzerland', 'CHE'],
        [43, 'Chile', 'CHL'],
        [44, 'China', 'CHN'],
        [45, 'Côte d\'Ivoire', 'CIV'],
        [46, 'Cameroon', 'CMR'],
        [47, 'Congo (the Democratic Republic of the)', 'COD'],
        [48, 'Congo (the)', 'COG'],
        [49, 'Cook Islands (the)', 'COK'],
        [50, 'Colombia', 'COL'],
        [51, 'Comoros (the)', 'COM'],
        [52, 'Cabo Verde', 'CPV'],
        [53, 'Costa Rica', 'CRI'],
        [54, 'Cuba', 'CUB'],
        [55, 'Curaçao', 'CUW'],
        [56, 'Christmas Island', 'CXR'],
        [57, 'Cayman Islands (the)', 'CYM'],
        [58, 'Cyprus', 'CYP'],
        [59, 'Czechia', 'CZE'],
        [60, 'Germany', 'DEU'],
        [61, 'Djibouti', 'DJI'],
        [62, 'Dominica', 'DMA'],
        [63, 'Denmark', 'DNK'],
        [64, 'Dominican Republic (the)', 'DOM'],
        [65, 'Algeria', 'DZA'],
        [66, 'Ecuador', 'ECU'],
        [67, 'Egypt', 'EGY'],
        [68, 'Eritrea', 'ERI'],
        [69, 'Western Sahara', 'ESH'],
        [70, 'Spain', 'ESP'],
        [71, 'Estonia', 'EST'],
        [72, 'Ethiopia', 'ETH'],
        [73, 'Finland', 'FIN'],
        [74, 'Fiji', 'FJI'],
        [75, 'Falkland Islands (the) [Malvinas]', 'FLK'],
        [76, 'France', 'FRA'],
        [77, 'Faroe Islands (the)', 'FRO'],
        [78, 'Micronesia (Federated States of)', 'FSM'],
        [79, 'Gabon', 'GAB'],
        [80, 'United Kingdom of Great Britain and Northern Ireland (the)', 'GBR'],
        [81, 'Georgia', 'GEO'],
        [82, 'Guernsey', 'GGY'],
        [83, 'Ghana', 'GHA'],
        [84, 'Gibraltar', 'GIB'],
        [85, 'Guinea', 'GIN'],
        [86, 'Guadeloupe', 'GLP'],
        [87, 'Gambia (the)', 'GMB'],
        [88, 'Guinea-Bissau', 'GNB'],
        [89, 'Equatorial Guinea', 'GNQ'],
        [90, 'Greece', 'GRC'],
        [91, 'Grenada', 'GRD'],
        [92, 'Greenland', 'GRL'],
        [93, 'Guatemala', 'GTM'],
        [94, 'French Guiana', 'GUF'],
        [95, 'Guam', 'GUM'],
        [96, 'Guyana', 'GUY'],
        [97, 'Hong Kong', 'HKG'],
        [98, 'Heard Island and McDonald Islands', 'HMD'],
        [99, 'Honduras', 'HND'],
        [100, 'Croatia', 'HRV'],
        [101, 'Haiti', 'HTI'],
        [102, 'Hungary', 'HUN'],
        [103, 'Indonesia', 'IDN'],
        [104, 'Isle of Man', 'IMN'],
        [105, 'India', 'IND'],
        [106, 'British Indian Ocean Territory (the)', 'IOT'],
        [107, 'Ireland', 'IRL'],
        [108, 'Iran (Islamic Republic of)', 'IRN'],
        [109, 'Iraq', 'IRQ'],
        [110, 'Iceland', 'ISL'],
        [111, 'Israel', 'ISR'],
        [112, 'Italy', 'ITA'],
        [113, 'Jamaica', 'JAM'],
        [114, 'Jersey', 'JEY'],
        [115, 'Jordan', 'JOR'],
        [116, 'Japan', 'JPN'],
        [117, 'Kazakhstan', 'KAZ'],
        [118, 'Kenya', 'KEN'],
        [119, 'Kyrgyzstan', 'KGZ'],
        [120, 'Cambodia', 'KHM'],
        [121, 'Kiribati', 'KIR'],
        [122, 'Saint Kitts and Nevis', 'KNA'],
        [123, 'Korea (the Republic of)', 'KOR'],
        [124, 'Kuwait', 'KWT'],
        [125, 'Lao People\'s Democratic Republic (the)', 'LAO'],
        [126, 'Lebanon', 'LBN'],
        [127, 'Liberia', 'LBR'],
        [128, 'Libya', 'LBY'],
        [129, 'Saint Lucia', 'LCA'],
        [130, 'Liechtenstein', 'LIE'],
        [131, 'Sri Lanka', 'LKA'],
        [132, 'Lesotho', 'LSO'],
        [133, 'Lithuania', 'LTU'],
        [134, 'Luxembourg', 'LUX'],
        [135, 'Latvia', 'LVA'],
        [136, 'Macao', 'MAC'],
        [137, 'Saint Martin (French part)', 'MAF'],
        [138, 'Morocco', 'MAR'],
        [139, 'Monaco', 'MCO'],
        [140, 'Moldova (the Republic of)', 'MDA'],
        [141, 'Madagascar', 'MDG'],
        [142, 'Maldives', 'MDV'],
        [143, 'Mexico', 'MEX'],
        [144, 'Marshall Islands (the)', 'MHL'],
        [145, 'Republic of North Macedonia', 'MKD'],
        [146, 'Mali', 'MLI'],
        [147, 'Malta', 'MLT'],
        [148, 'Myanmar', 'MMR'],
        [149, 'Montenegro', 'MNE'],
        [150, 'Mongolia', 'MNG'],
        [151, 'Northern Mariana Islands (the)', 'MNP'],
        [152, 'Mozambique', 'MOZ'],
        [153, 'Mauritania', 'MRT'],
        [154, 'Montserrat', 'MSR'],
        [155, 'Martinique', 'MTQ'],
        [156, 'Mauritius', 'MUS'],
        [157, 'Malawi', 'MWI'],
        [158, 'Malaysia', 'MYS'],
        [159, 'Mayotte', 'MYT'],
        [160, 'Namibia', 'NAM'],
        [161, 'New Caledonia', 'NCL'],
        [162, 'Niger (the)', 'NER'],
        [163, 'Norfolk Island', 'NFK'],
        [164, 'Nigeria', 'NGA'],
        [165, 'Nicaragua', 'NIC'],
        [166, 'Niue', 'NIU'],
        [167, 'Netherlands (the)', 'NLD'],
        [168, 'Norway', 'NOR'],
        [169, 'Nepal', 'NPL'],
        [170, 'Nauru', 'NRU'],
        [171, 'New Zealand', 'NZL'],
        [172, 'Oman', 'OMN'],
        [173, 'Pakistan', 'PAK'],
        [174, 'Panama', 'PAN'],
        [175, 'Pitcairn', 'PCN'],
        [176, 'Peru', 'PER'],
        [177, 'Philippines (the)', 'PHL'],
        [178, 'Palau', 'PLW'],
        [179, 'Papua New Guinea', 'PNG'],
        [180, 'Poland', 'POL'],
        [181, 'Puerto Rico', 'PRI'],
        [182, 'Korea (the Democratic People\'s Republic of)', 'PRK'],
        [183, 'Portugal', 'PRT'],
        [184, 'Paraguay', 'PRY'],
        [185, '"Palestine, State of"', 'PSE'],
        [186, 'French Polynesia', 'PYF'],
        [187, 'Qatar', 'QAT'],
        [188, 'Réunion', 'REU'],
        [189, 'Romania', 'ROU'],
        [190, 'Russian Federation (the)', 'RUS'],
        [191, 'Rwanda', 'RWA'],
        [192, 'Saudi Arabia', 'SAU'],
        [193, 'Sudan (the)', 'SDN'],
        [194, 'Senegal', 'SEN'],
        [195, 'Singapore', 'SGP'],
        [196, 'South Georgia and the South Sandwich Islands', 'SGS'],
        [197, '"Saint Helena, Ascension and Tristan da Cunha"', 'SHN'],
        [198, 'Svalbard and Jan Mayen', 'SJM'],
        [199, 'Solomon Islands', 'SLB'],
        [200, 'Sierra Leone', 'SLE'],
        [201, 'El Salvador', 'SLV'],
        [202, 'San Marino', 'SMR'],
        [203, 'Somalia', 'SOM'],
        [204, 'Saint Pierre and Miquelon', 'SPM'],
        [205, 'Serbia', 'SRB'],
        [206, 'South Sudan', 'SSD'],
        [207, 'Sao Tome and Principe', 'STP'],
        [208, 'Suriname', 'SUR'],
        [209, 'Slovakia', 'SVK'],
        [210, 'Slovenia', 'SVN'],
        [211, 'Sweden', 'SWE'],
        [212, 'Eswatini', 'SWZ'],
        [213, 'Sint Maarten (Dutch part)', 'SXM'],
        [214, 'Seychelles', 'SYC'],
        [215, 'Syrian Arab Republic', 'SYR'],
        [216, 'Turks and Caicos Islands (the)', 'TCA'],
        [217, 'Chad', 'TCD'],
        [218, 'Togo', 'TGO'],
        [219, 'Thailand', 'THA'],
        [220, 'Tajikistan', 'TJK'],
        [221, 'Tokelau', 'TKL'],
        [222, 'Turkmenistan', 'TKM'],
        [223, 'Timor-Leste', 'TLS'],
        [224, 'Tonga', 'TON'],
        [225, 'Trinidad and Tobago', 'TTO'],
        [226, 'Tunisia', 'TUN'],
        [227, 'Turkey', 'TUR'],
        [228, 'Tuvalu', 'TUV'],
        [229, 'Taiwan (Province of China)', 'TWN'],
        [230, '"Tanzania, United Republic of"', 'TZA'],
        [231, 'Uganda', 'UGA'],
        [232, 'Ukraine', 'UKR'],
        [233, 'United States Minor Outlying Islands (the)', 'UMI'],
        [234, 'Uruguay', 'URY'],
        [235, 'United States of America (the)', 'USA'],
        [236, 'Uzbekistan', 'UZB'],
        [237, 'Holy See (the)', 'VAT'],
        [238, 'Saint Vincent and the Grenadines', 'VCT'],
        [239, 'Venezuela (Bolivarian Republic of)', 'VEN'],
        [240, 'Virgin Islands (British)', 'VGB'],
        [241, 'Virgin Islands (U.S.)', 'VIR'],
        [242, 'Viet Nam', 'VNM'],
        [243, 'Vanuatu', 'VUT'],
        [244, 'Wallis and Futuna', 'WLF'],
        [245, 'Samoa', 'WSM'],
        [246, 'Yemen', 'YEM'],
        [247, 'South Africa', 'ZAF'],
        [248, 'Zambia', 'ZMB'],
        [249, 'Zimbabwe', 'ZWE'],
      ];

      foreach ($countries as $country) {
        $mCountry->record->recordCreate([
          "id" => $country[0],
          "name" => $country[1],
          "code" => $country[2],
        ]);
      }

      $mUserRole = $this->main->di->create(Models\UserRole::class);
      $mPermission = $this->main->di->create(Models\Permission::class);

      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_ADMINISTRATOR,
        'role' => 'Administrator',
        'description' => 'Can do anything.',
        'grant_all' => true,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_CHIEF_OFFICER,
        'role' => 'Chief Officer (CEO, CFO, CTO, ...)',
        'description' => 'Can read all data and can modify most of the data. Does not have access to settings.',
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_MANAGER,
        'role' => 'Manager (Sales, Project, ...)',
        'description' => 'Can read and modify all data that he/she owns or is manager.',
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_EMPLOYEE,
        'role' => 'Employee',
        'description' => 'In general, can read or modify only data that he/she owns.',
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_ASSISTANT,
        'role' => 'Assistant',
        'description' => 'Very similar to employee, but may be more limited in some certain situations.',
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_EXTERNAL,
        'role' => 'External',
        'description' => 'By default should not have access to anything. Access permissions must be enabled in settings by administrator.',
        'grant_all' => false,
        'is_default' => true,
      ])['id'];

    }
  }

  public function addSetting(\Hubleto\Framework\App $app, array $setting): void
  {
    $this->settings[] = [$app, $setting];
  }

  public function getSettings(): array
  {
    $settings = [];
    foreach ($this->settings as $setting) {
      $settings[] = $setting[1];
    }

    $titles = array_column($settings, 'title');
    array_multisort($titles, SORT_ASC, $settings);
    return $settings;
  }

}

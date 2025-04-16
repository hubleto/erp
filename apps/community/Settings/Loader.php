<?php

namespace HubletoApp\Community\Settings;

use HubletoApp\Community\Settings\Models\Permission;

class Loader extends \HubletoMain\Core\App
{

  // public function __construct(\HubletoMain $main)
  // {
  //   parent::__construct($main);

  //   $this->registerModel(\HubletoApp\Community\Settings\Models\User::class);
  //   $this->registerModel(\HubletoApp\Community\Settings\Models\UserRole::class);
  //   $this->registerModel(\HubletoApp\Community\Settings\Models\UserHasRole::class);
  //   $this->registerModel(\HubletoApp\Community\Settings\Models\Setting::class);
  // }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^settings\/?$/' => Controllers\Dashboard::class,
      '/^settings\/profile\/?$/' => Controllers\Profile::class,
      '/^settings\/users\/?$/' => Controllers\Users::class,
      '/^settings\/user-roles\/?$/' => Controllers\UserRoles::class,
      '/^settings\/profiles\/?$/' => Controllers\Profiles::class,
      '/^settings\/general\/?$/' => Controllers\General::class,
      '/^settings\/activity-types\/?$/' => Controllers\ActivityTypes::class,
      '/^settings\/countries\/?$/' => Controllers\Countries::class,
      '/^settings\/currencies\/?$/' => Controllers\Currencies::class,
      '/^settings\/pipelines\/?$/' => Controllers\Pipelines::class,
      '/^settings\/permissions\/?$/' => Controllers\Permissions::class,
      '/^settings\/invoice-profiles\/?$/' => Controllers\InvoiceProfiles::class,
      '/^settings\/config\/?$/' => Controllers\Config::class,
      '/^settings\/get-permissions\/?$/' => Controllers\Api\GetPermissions::class,
      '/^settings\/save-permissions\/?$/' => Controllers\Api\SavePermissions::class,
    ]);

    $this->main->addSetting($this, ['title' => $this->translate('Users'), 'icon' => 'fas fa-user', 'url' => 'settings/users']);
    $this->main->addSetting($this, ['title' => $this->translate('User roles'), 'icon' => 'fas fa-user-group', 'url' => 'settings/user-roles']);
    $this->main->addSetting($this, ['title' => $this->translate('Your companies'), 'icon' => 'fas fa-id-card', 'url' => 'settings/profiles']);
    $this->main->addSetting($this, ['title' => $this->translate('General settings'), 'icon' => 'fas fa-cog', 'url' => 'settings/general']);
    $this->main->addSetting($this, ['title' => $this->translate('Permissions'), 'icon' => 'fas fa-shield-halved', 'url' => 'settings/permissions']);
    $this->main->addSetting($this, ['title' => $this->translate('Activity types'), 'icon' => 'fas fa-layer-group', 'url' => 'settings/activity-types']);
    $this->main->addSetting($this, ['title' => $this->translate('Countries'), 'icon' => 'fas fa-globe', 'url' => 'settings/countries']);
    $this->main->addSetting($this, ['title' => $this->translate('Currencies'), 'icon' => 'fas fa-dollar-sign', 'url' => 'settings/currencies']);
    $this->main->addSetting($this, ['title' => $this->translate('Pipelines'), 'icon' => 'fas fa-bars-progress', 'url' => 'settings/pipelines']);
    $this->main->addSetting($this, ['title' => $this->translate('Invoice profiles'), 'icon' => 'fas fa-user-tie', 'url' => 'settings/invoice-profiles']);
    $this->main->addSetting($this, ['title' => $this->translate('Platform config'), 'icon' => 'fas fa-hammer', 'url' => 'settings/config']);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mProfile = new Models\Profile($this->main);
      $mUser = new Models\User($this->main);
      $mUserRole = new Models\UserRole($this->main);
      $mUserHasRole = new Models\UserHasRole($this->main);
      $mPermission = new Models\Permission($this->main);
      $mRolePermission = new Models\RolePermission($this->main);
      $mCountry = new Models\Country($this->main);
      $mSetting = new Models\Setting($this->main);
      $mActivityTypes = new Models\ActivityType($this->main);
      $mCurrency = new Models\Currency($this->main);
      $mPipeline = new Models\Pipeline($this->main);
      $mPipelineStep = new Models\PipelineStep($this->main);
      $mInvoiceProfile = new Models\InvoiceProfile($this->main);

      $mProfile->dropTableIfExists()->install();
      $mUser->dropTableIfExists()->install();
      $mUserRole->dropTableIfExists()->install();
      $mUserHasRole->dropTableIfExists()->install();
      $mPermission->dropTableIfExists()->install();
      $mRolePermission->dropTableIfExists()->install();
      $mCountry->dropTableIfExists()->install();
      $mSetting->dropTableIfExists()->install();
      $mActivityTypes->dropTableIfExists()->install();
      $mCurrency->dropTableIfExists()->install();
      $mPipeline->dropTableIfExists()->install();
      $mPipelineStep->dropTableIfExists()->install();
      $mInvoiceProfile->dropTableIfExists()->install();

      $mSetting->record->recordCreate([
        'key' => 'Apps\Community\Settings\Pipeline\DefaultPipeline',
        'value' => '2',
        'id_user' => null
      ]);
      $mSetting->record->recordCreate([
        'key' => 'Apps\Community\Settings\Currency\DefaultCurrency',
        'value' => '1',
        'id_user' => null
      ]);

      $mCurrency->record->recordCreate([ 'name' => 'Euro', 'code' => 'EUR' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Dollar', 'code' => 'USD' ]);
      $mCurrency->record->recordCreate([ 'name' => 'Koruny', 'code' => 'CZK' ]);

      $mPipeline->record->recordCreate([ "name" => "New customer" ]);
      $mPipelineStep->record->recordCreate([ 'name' => 'New', 'order' => 1, 'color' => '#4080A0', 'id_pipeline' => 1 ]);
      $mPipelineStep->record->recordCreate([ 'name' => 'In Progress', 'order' => 2, 'color' => '#A04020', 'id_pipeline' => 1 ]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Closed', 'order' => 3, 'color' => '#006060', 'id_pipeline' => 1 ]);

      $mPipeline->record->recordCreate([ "name" => "Existing customer" ]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Start', 'order' => 1, 'color' => '#405060', 'id_pipeline' => 2 ]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Client Contacted', 'order' => 2, 'color' => '#800000', 'id_pipeline' => 2 ]);
      $mPipelineStep->record->recordCreate([ 'name' => 'In Progress', 'order' => 3, 'color' => '#808000', 'id_pipeline' => 2 ]);
      $mPipelineStep->record->recordCreate([ 'name' => 'Ended', 'order' => 4, 'color' => '#002080', 'id_pipeline' => 2 ]);

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
    }
  }

  public function installDefaultPermissions(): void
  {
    $mUserRole = new Models\UserRole($this->main);
    $mPermission = new Models\Permission($this->main);

    $permissions = [
      "HubletoApp/Community/Settings/Models/ActivityType:Create",
      "HubletoApp/Community/Settings/Models/ActivityType:Read",
      "HubletoApp/Community/Settings/Models/ActivityType:Update",
      "HubletoApp/Community/Settings/Models/ActivityType:Delete",

      "HubletoApp/Community/Settings/Models/Country:Create",
      "HubletoApp/Community/Settings/Models/Country:Read",
      "HubletoApp/Community/Settings/Models/Country:Update",
      "HubletoApp/Community/Settings/Models/Country:Delete",

      "HubletoApp/Community/Settings/Models/Currency:Create",
      "HubletoApp/Community/Settings/Models/Currency:Read",
      "HubletoApp/Community/Settings/Models/Currency:Update",
      "HubletoApp/Community/Settings/Models/Currency:Delete",

      "HubletoApp/Community/Settings/Models/Tag:Create",
      "HubletoApp/Community/Settings/Models/Tag:Read",
      "HubletoApp/Community/Settings/Models/Tag:Update",
      "HubletoApp/Community/Settings/Models/Tag:Delete",

      "HubletoApp/Community/Settings/Models/Pipeline:Create",
      "HubletoApp/Community/Settings/Models/Pipeline:Read",
      "HubletoApp/Community/Settings/Models/Pipeline:Update",
      "HubletoApp/Community/Settings/Models/Pipeline:Delete",

      "HubletoApp/Community/Settings/Models/PipelineStep:Create",
      "HubletoApp/Community/Settings/Models/PipelineStep:Read",
      "HubletoApp/Community/Settings/Models/PipelineStep:Update",
      "HubletoApp/Community/Settings/Models/PipelineStep:Delete",

      "HubletoApp/Community/Settings/Models/Profile:Create",
      "HubletoApp/Community/Settings/Models/Profile:Read",
      "HubletoApp/Community/Settings/Models/Profile:Update",
      "HubletoApp/Community/Settings/Models/Profile:Delete",

      "HubletoApp/Community/Settings/Models/Setting:Create",
      "HubletoApp/Community/Settings/Models/Setting:Read",
      "HubletoApp/Community/Settings/Models/Setting:Update",
      "HubletoApp/Community/Settings/Models/Setting:Delete",

      "HubletoApp/Community/Settings/Models/User:Create",
      "HubletoApp/Community/Settings/Models/User:Read",
      "HubletoApp/Community/Settings/Models/User:Update",
      "HubletoApp/Community/Settings/Models/User:Delete",

      "HubletoApp/Community/Settings/Models/UserRole:Create",
      "HubletoApp/Community/Settings/Models/UserRole:Read",
      "HubletoApp/Community/Settings/Models/UserRole:Update",
      "HubletoApp/Community/Settings/Models/UserRole:Delete",

      "HubletoApp/Community/Settings/Models/UserHasRole:Create",
      "HubletoApp/Community/Settings/Models/UserHasRole:Read",
      "HubletoApp/Community/Settings/Models/UserHasRole:Update",
      "HubletoApp/Community/Settings/Models/UserHasRole:Delete",

      "HubletoApp/Community/Settings/Models/Permission:Create",
      "HubletoApp/Community/Settings/Models/Permission:Read",
      "HubletoApp/Community/Settings/Models/Permission:Update",
      "HubletoApp/Community/Settings/Models/Permission:Delete",

      "HubletoApp/Community/Settings/Controllers/Dashboard",
      "HubletoApp/Community/Settings/Controllers/ActivityType",
      "HubletoApp/Community/Settings/Controllers/Country",
      "HubletoApp/Community/Settings/Controllers/Currency",
      "HubletoApp/Community/Settings/Controllers/Pipeline",
      "HubletoApp/Community/Settings/Controllers/PipelineStep",
      "HubletoApp/Community/Settings/Controllers/Profile",
      "HubletoApp/Community/Settings/Controllers/Setting",
      "HubletoApp/Community/Settings/Controllers/Tag",
      "HubletoApp/Community/Settings/Controllers/User",
      "HubletoApp/Community/Settings/Controllers/UserRole",
      "HubletoApp/Community/Settings/Controllers/UserHasRole",
      "HubletoApp/Community/Settings/Controllers/Permissions",
      "HubletoApp/Community/Settings/Controllers/Dashboard",

      "HubletoApp/Community/Settings/Dashboard",
      "HubletoApp/Community/Settings/ActivityType",
      "HubletoApp/Community/Settings/Country",
      "HubletoApp/Community/Settings/Currency",
      "HubletoApp/Community/Settings/Pipeline",
      "HubletoApp/Community/Settings/PipelineStep",
      "HubletoApp/Community/Settings/Profile",
      "HubletoApp/Community/Settings/Setting",
      "HubletoApp/Community/Settings/Tag",
      "HubletoApp/Community/Settings/User",
      "HubletoApp/Community/Settings/UserRole",
      "HubletoApp/Community/Settings/UserHasRole",
      "HubletoApp/Community/Settings/Permissions",
    ];

    $idRoles = [];
    $idRoles[Models\UserRole::ROLE_ADMINISTRATOR] = $mUserRole->record->recordCreate(['id' => Models\UserRole::ROLE_ADMINISTRATOR, 'role' => 'Administrator', 'grant_all' => 1])['id'];
    $idRoles[Models\UserRole::ROLE_SALES_MANAGER] = $mUserRole->record->recordCreate(['id' => Models\UserRole::ROLE_SALES_MANAGER, 'role' => 'Sales manager', 'grant_all' => 0])['id'];
    $idRoles[Models\UserRole::ROLE_ACCOUNTANT] = $mUserRole->record->recordCreate(['id' => Models\UserRole::ROLE_ACCOUNTANT, 'role' => 'Accountant', 'grant_all' => 0])['id'];

    foreach ($permissions as $permission) {
      $mPermission->record->recordCreate([
        "permission" => $permission
      ]);
    }
  }
}


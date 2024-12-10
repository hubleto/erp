<?php

namespace CeremonyCrmApp\Modules\Core\Settings;

use CeremonyCrmApp\Modules\Core\Settings\Models\Permission;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public string $translationContext = 'mod.core.settings.loader';

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);

    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\User::class);
    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\Models\UserRole::class);
    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\UserHasRole::class);
    $this->registerModel(\CeremonyCrmApp\Modules\Core\Settings\Models\Setting::class);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^settings\/?$/' => Controllers\Dashboard::class,
      '/^settings\/users\/?$/' => Controllers\Users::class,
      '/^settings\/user-roles\/?$/' => Controllers\UserRoles::class,
      '/^settings\/profiles\/?$/' => Controllers\Profiles::class,
      '/^settings\/settings\/?$/' => Controllers\Settings::class,
      '/^settings\/tags\/?$/' => Controllers\Tags::class,
      '/^settings\/activity-types\/?$/' => Controllers\ActivityTypes::class,
      '/^settings\/contact-types\/?$/' => Controllers\ContactTypes::class,
      '/^settings\/countries\/?$/' => Controllers\Countries::class,
      '/^settings\/currencies\/?$/' => Controllers\Currencies::class,
      '/^settings\/labels\/?$/' => Controllers\Labels::class,
      '/^settings\/lead-statuses\/?$/' => Controllers\LeadStatuses::class,
      '/^settings\/deal-statuses\/?$/' => Controllers\DealStatuses::class,
      '/^settings\/pipelines\/?$/' => Controllers\Pipelines::class,
      '/^settings\/permissions\/?$/' => Controllers\Permissions::class,
      '/^settings\/invoice-profiles\/?$/' => Controllers\InvoiceProfiles::class,
    ]);

    $this->app->sidebar->addLink(1, 99100, 'settings', $this->translate('Settings'), 'fas fa-cog');

    if (str_starts_with($this->app->requestedUri, 'settings')) {
      $this->app->sidebar->addHeading1(2, 99200, $this->translate('Settings'));
      $this->app->sidebar->addLink(2, 99201, 'settings/users', $this->translate('Users'), 'fas fa-user');
      $this->app->sidebar->addLink(2, 99202, 'settings/user-roles', $this->translate('User Roles'), 'fas fa-user-group');
      $this->app->sidebar->addLink(2, 99203, 'settings/profiles', $this->translate('Profiles'), 'fas fa-id-card');
      $this->app->sidebar->addLink(2, 99204, 'settings/settings', $this->translate('Settings'), 'fas fa-cog');
      $this->app->sidebar->addLink(2, 99205, 'settings/permissions', $this->translate('Permissions'), 'fas fa-shield-halved');
      $this->app->sidebar->addLink(2, 99206, 'settings/tags', $this->translate('Tags'), 'fas fa-tags');
      $this->app->sidebar->addLink(2, 99207, 'settings/activity-types', $this->translate('Activity Types'), 'fas fa-layer-group');
      $this->app->sidebar->addLink(2, 99208, 'settings/contact-types', $this->translate('Contact Types'), 'fas fa-phone');
      $this->app->sidebar->addLink(2, 99209, 'settings/countries', $this->translate('Countries'), 'fas fa-globe');
      $this->app->sidebar->addLink(2, 99210, 'settings/currencies', $this->translate('Currencies'), 'fas fa-dollar-sign');
      $this->app->sidebar->addLink(2, 99211, 'settings/labels', $this->translate('Labels'), 'fas fa-tags');
      $this->app->sidebar->addLink(2, 99212, 'settings/lead-statuses', $this->translate('Lead Statuses'), 'fas fa-arrow-down-short-wide');
      $this->app->sidebar->addLink(2, 99213, 'settings/deal-statuses', $this->translate('Deal Statuses'), 'fas fa-arrow-up-short-wide');
      $this->app->sidebar->addLink(2, 99214, 'settings/pipelines', $this->translate('Pipelines'), 'fas fa-bars-progress');
      $this->app->sidebar->addLink(2, 99214, 'settings/invoice-profiles', $this->translate('Invoice profiles'), 'fas fa-user-tie');
    }
  }

  public function installTables()
  {
    $mProfile = new Models\Profile($this->app);
    $mUser = new Models\User($this->app);
    $mUserRole = new Models\UserRole($this->app);
    $mUserHasRole = new Models\UserHasRole($this->app);
    $mPermission = new Models\Permission($this->app);
    $mRolePermission = new Models\RolePermission($this->app);
    $mCountry = new Models\Country($this->app);
    $mSetting = new Models\Setting($this->app);
    $mTag = new Models\Tag($this->app);
    $mActivityTypes = new Models\ActivityType($this->app);
    $mContactType = new Models\ContactType($this->app);
    $mCurrency = new Models\Currency($this->app);
    $mLabel = new Models\Label($this->app);
    $mLeadStatus = new Models\LeadStatus($this->app);
    $mDealStatus = new Models\DealStatus($this->app);
    $mPipeline = new Models\Pipeline($this->app);
    $mPipelineStep = new Models\PipelineStep($this->app);
    $mInvoiceProfile = new Models\InvoiceProfile($this->app);

    $mProfile->dropTableIfExists()->install();
    $mUser->dropTableIfExists()->install();
    $mUserRole->dropTableIfExists()->install();
    $mUserHasRole->dropTableIfExists()->install();
    $mPermission->dropTableIfExists()->install();
    $mRolePermission->dropTableIfExists()->install();
    $mCountry->dropTableIfExists()->install();
    $mSetting->dropTableIfExists()->install();
    $mTag->dropTableIfExists()->install();
    $mActivityTypes->dropTableIfExists()->install();
    $mContactType->dropTableIfExists()->install();
    $mCurrency->dropTableIfExists()->install();
    $mLabel->dropTableIfExists()->install();
    $mLeadStatus->dropTableIfExists()->install();
    $mDealStatus->dropTableIfExists()->install();
    $mPipeline->dropTableIfExists()->install();
    $mPipelineStep->dropTableIfExists()->install();
    $mInvoiceProfile->dropTableIfExists()->install();

    $mSetting->eloquent->create([
      'key' => 'Modules\Core\Settings\Pipeline\DefaultPipeline',
      'value' => '2',
      'id_user' => null
    ]);

    $mCurrency->eloquent->create([
      'name' => 'Euro',
      'code' => 'EUR',
    ]);
    $mCurrency->eloquent->create([
      'name' => 'Dollar',
      'code' => 'USD',
    ]);
    $mCurrency->eloquent->create([
      'name' => 'Koruny',
      'code' => 'CZK',
    ]);

    $mLabel->eloquent->create([
      'name' => 'Hot',
      'color' => '#f55442',
    ]);
    $mLabel->eloquent->create([
      'name' => 'Warm',
      'color' => '#f5bc42',
    ]);
    $mLabel->eloquent->create([
      'name' => 'Cold',
      'color' => '#42ddf5',
    ]);

    $mLeadStatus->eloquent->create([
      'name' => 'New',
      'order' => 1,
      'color' => '#f55442',
    ]);
    $mLeadStatus->eloquent->create([
      'name' => 'In Progress',
      'order' => 2,
      'color' => '#f5bc42',
    ]);
    $mLeadStatus->eloquent->create([
      'name' => 'Closed',
      'order' => 3,
      'color' => '#42ddf5',
    ]);
    $mLeadStatus->eloquent->create([
      'name' => 'Lost',
      'order' => 4,
      'color' => '#f55442',
    ]);

    $mDealStatus->eloquent->create([
      'name' => 'New',
      'order' => 1,
      'color' => '#f55442',
    ]);
    $mDealStatus->eloquent->create([
      'name' => 'In Progress',
      'order' => 2,
      'color' => '#f5bc42',
    ]);
    $mDealStatus->eloquent->create([
      'name' => 'Closed',
      'order' => 3,
      'color' => '#42ddf5',
    ]);
    $mDealStatus->eloquent->create([
      'name' => 'Lost',
      'order' => 4,
      'color' => '#f55442',
    ]);

    $mPipeline->eloquent->create([
      "name" => "Test Pipeline"
    ]);
    $mPipelineStep->eloquent->create([
      'name' => 'New',
      'order' => 1,
      'id_pipeline' => 1,
    ]);
    $mPipelineStep->eloquent->create([
      'name' => 'In Progress',
      'order' => 2,
      'id_pipeline' => 1,
    ]);
    $mPipelineStep->eloquent->create([
      'name' => 'Closed',
      'order' => 3,
      'id_pipeline' => 1,
    ]);


    $mPipeline->eloquent->create([
      "name" => "Test Pipeline 2"
    ]);
    $mPipelineStep->eloquent->create([
      'name' => 'Start',
      'order' => 1,
      'id_pipeline' => 2,
    ]);
    $mPipelineStep->eloquent->create([
      'name' => 'Client Contacted',
      'order' => 2,
      'id_pipeline' => 2,
    ]);
    $mPipelineStep->eloquent->create([
      'name' => 'In Progress',
      'order' => 3,
      'id_pipeline' => 2,
    ]);
    $mPipelineStep->eloquent->create([
      'name' => 'Ended',
      'order' => 4,
      'id_pipeline' => 2,
    ]);

    $mActivityTypes->eloquent->create([
      'name' => 'Meeting',
      'color' => '#ff5733',
      'calendar_visibility' => true,
    ]);
    $mActivityTypes->eloquent->create([
      'name' => 'Bussiness Trip',
      'color' => '#0070ff',
      'calendar_visibility' => true,
    ]);
    $mActivityTypes->eloquent->create([
      'name' => 'Call',
      'color' => '#16fa07',
      'calendar_visibility' => false,
    ]);
    $mActivityTypes->eloquent->create([
      'name' => 'Email',
      'color' => '#d4ff00',
      'calendar_visibility' => false,
    ]);
    $mActivityTypes->eloquent->create([
      'name' => 'Other',
      'color' => '#333333',
      'calendar_visibility' => true,
    ]);

    $mContactType->eloquent->create([
      'name' => 'Work'
    ]);
    $mContactType->eloquent->create([
      'name' => 'Home'
    ]);
    $mContactType->eloquent->create([
      'name' => 'Other'
    ]);

    $mTag->eloquent->create([
      'name' => "Tag 1",
      'color' => '#ff5733',
    ]);
    $mTag->eloquent->create([
      'name' => "Tag 2",
      'color' => '#0070ff',
    ]);
    $mTag->eloquent->create([
      'name' => "Tag 3",
      'color' => '#16fa07',
    ]);



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
      $mCountry->eloquent->create([
        "id" => $country[0],
        "name" => $country[1],
        "code" => $country[2],
      ]);
    }

  }

  public function installDefaultPermissions()
  {
    $mPermission = new Models\Permission($this->app);
    $mRolePermission = new Models\RolePermission($this->app);

    $permissions = [
      "CeremonyCrmApp/Modules/Core/Settings/Models/ActivityType:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/ActivityType:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/ActivityType:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/ActivityType:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/Country:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Country:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Country:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Country:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/Currency:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Currency:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Currency:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Currency:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/Label:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Label:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Label:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Label:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/PipelineStep:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/PipelineStep:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/PipelineStep:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/PipelineStep:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/Profile:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Profile:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Profile:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Profile:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/Setting:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Setting:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Setting:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Setting:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/Tag:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Tag:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Tag:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Tag:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/User:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/User:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/User:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/User:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/UserRole:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserRole:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserRole:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserRole:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/UserHasRole:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserHasRole:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserHasRole:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/UserHasRole:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Settings/Models/Permission:Create" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Permission:Read" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Permission:Update" => [],
      "CeremonyCrmApp/Modules/Core/Settings/Models/Permission:Delete" => [],

      "CeremonyCrmApp/Modules/Core/Setting/Controllers/ActivityType" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Country" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Currency" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Label" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Pipeline" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/PipelineStep" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Profile" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Setting" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Tag" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/User" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/UserRole" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/UserHasRole" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
      "CeremonyCrmApp/Modules/Core/Setting/Controllers/Permissions" => [Models\UserRole::ROLE_SALES_MANAGER, Models\UserRole::ROLE_ACCOUNTANT],
    ];

    foreach ($permissions as $permission => $grantedForRoles) {
      $idPermission = $mPermission->eloquent->create([
        "permission" => $permission
      ])->id;

      foreach ($grantedForRoles as $idRole) {
        $mRolePermission->eloquent->create(['id_role' => $idRole, 'id_permission' => $idPermission]);
      }
    }
  }
}


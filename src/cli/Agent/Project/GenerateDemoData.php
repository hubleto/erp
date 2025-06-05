<?php

namespace HubletoMain\Cli\Agent\Project;

use HubletoApp\Community\Settings\Models\Country;
use HubletoApp\Community\Settings\Models\Permission;
use HubletoApp\Community\Settings\Models\Company;
use HubletoApp\Community\Settings\Models\RolePermission;
use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Settings\Models\UserRole;
use HubletoApp\Community\Settings\Models\UserHasRole;
use HubletoApp\Community\Settings\Models\Tag;

class GenerateDemoData extends \HubletoMain\Cli\Agent\Command
{

  public function run(): void
  {

    $this->main->permissions->DANGEROUS__grantAllPermissions();

    $this->cli->cyan("Generating demo data...\n");

    $mCompany = new \HubletoApp\Community\Settings\Models\Company($this->main);
    $mUser = new \HubletoApp\Community\Settings\Models\User($this->main);
    $mUserRole = new \HubletoApp\Community\Settings\Models\UserRole($this->main);
    $mUserHasRole = new \HubletoApp\Community\Settings\Models\UserHasRole($this->main);

    $idCompany = 1; // plati za predpokladu, ze tento command sa spusta hned po CommandInit

    $mUser = new \HubletoApp\Community\Settings\Models\User($this->main);

    $idUserChiefOfficer = $mUser->record->recordCreate([
      "first_name" => "Chief officer",
      "nick" => "CEO",
      "email" => "chief.officer@hubleto.com",
      "id_default_company" => $idCompany,
      "is_active" => true,
      "login" => "chief.officer",
      "password" => password_hash("chief.officer", PASSWORD_DEFAULT),
    ])['id'];

    $idUserManager = $mUser->record->recordCreate([
      "first_name" => "Manager",
      "nick" => "MGR",
      "email" => "manager@hubleto.com",
      "id_default_company" => $idCompany,
      "is_active" => true,
      "login" => "manager",
      "password" => password_hash("manager", PASSWORD_DEFAULT),
    ])['id'];

    $idUserEmployee = $mUser->record->recordCreate([
      "first_name" => "Employee",
      "nick" => "EMP",
      "email" => "employee@hubleto.com",
      "id_default_company" => $idCompany,
      "is_active" => true,
      "login" => "employee",
      "password" => password_hash("employee", PASSWORD_DEFAULT),
    ])['id'];

    $idUserAssistant = $mUser->record->recordCreate([
      "first_name" => "Assistant",
      "nick" => "ASS",
      "email" => "assistant@hubleto.com",
      "id_default_company" => $idCompany,
      "is_active" => false,
      "login" => "assistant",
      "password" => password_hash("assistant", PASSWORD_DEFAULT),
    ])['id'];

    $idUserExternal = $mUser->record->recordCreate([
      "first_name" => "External",
      "nick" => "EXT",
      "email" => "external@hubleto.com",
      "id_default_company" => $idCompany,
      "is_active" => false,
      "login" => "external",
      "password" => password_hash("external", PASSWORD_DEFAULT),
    ])['id'];

    $mUserHasRole = new \HubletoApp\Community\Settings\Models\UserHasRole($this->main);
    $mUserHasRole->record->recordCreate([
      "id_user" => $idUserChiefOfficer,
      "id_role" => \HubletoApp\Community\Settings\Models\UserRole::ROLE_CHIEF_OFFICER,
    ]);

    $mUserHasRole = new \HubletoApp\Community\Settings\Models\UserHasRole($this->main);
    $mUserHasRole->record->recordCreate([
      "id_user" => $idUserManager,
      "id_role" => \HubletoApp\Community\Settings\Models\UserRole::ROLE_MANAGER,
    ]);

    $mUserHasRole = new \HubletoApp\Community\Settings\Models\UserHasRole($this->main);
    $mUserHasRole->record->recordCreate([
      "id_user" => $idUserEmployee,
      "id_role" => \HubletoApp\Community\Settings\Models\UserRole::ROLE_EMPLOYEE,
    ]);

    $mUserHasRole = new \HubletoApp\Community\Settings\Models\UserHasRole($this->main);
    $mUserHasRole->record->recordCreate([
      "id_user" => $idUserAssistant,
      "id_role" => \HubletoApp\Community\Settings\Models\UserRole::ROLE_ASSISTANT,
    ]);

    $mUserHasRole = new \HubletoApp\Community\Settings\Models\UserHasRole($this->main);
    $mUserHasRole->record->recordCreate([
      "id_user" => $idUserExternal,
      "id_role" => \HubletoApp\Community\Settings\Models\UserRole::ROLE_EXTERNAL,
    ]);

    $mInvoiceProfile = new \HubletoApp\Community\Settings\Models\InvoiceProfile($this->main);
    $mInvoiceProfile->record->recordCreate(['name' => 'Test Profile 1']);

    //Documents
    $mDocuments = new \HubletoApp\Community\Documents\Models\Document($this->main);

    //Customers & Contacts
    $mCustomer            = new \HubletoApp\Community\Customers\Models\Customer($this->main);
    $mContact             = new \HubletoApp\Community\Contacts\Models\Contact($this->main);
    $mContactTag          = new \HubletoApp\Community\Contacts\Models\ContactTag($this->main);
    $mValue               = new \HubletoApp\Community\Contacts\Models\Value($this->main);
    $mCustomerActivity    = new \HubletoApp\Community\Customers\Models\CustomerActivity($this->main);
    $mCustomerDocument    = new \HubletoApp\Community\Customers\Models\CustomerDocument($this->main);
    $mCustomerTag         = new \HubletoApp\Community\Customers\Models\CustomerTag($this->main);

    //Leads
    $mLead = new \HubletoApp\Community\Leads\Models\Lead($this->main);
    $mLeadHistory  = new \HubletoApp\Community\Leads\Models\LeadHistory($this->main);
    $mLeadTag = new \HubletoApp\Community\Leads\Models\LeadTag($this->main);
    $mLeadProducts = new \HubletoApp\Community\Leads\Models\LeadProduct($this->main);
    $mLeadActivity = new \HubletoApp\Community\Leads\Models\LeadActivity($this->main);
    $mLeadDocument = new \HubletoApp\Community\Leads\Models\LeadDocument($this->main);

    //Deals
    $mDeal         = new \HubletoApp\Community\Deals\Models\Deal($this->main);
    $mDealHistory  = new \HubletoApp\Community\Deals\Models\DealHistory($this->main);
    $mDealTag      = new \HubletoApp\Community\Deals\Models\DealTag($this->main);
    $mDealActivity = new \HubletoApp\Community\Deals\Models\DealActivity($this->main);
    $mDealDocument = new \HubletoApp\Community\Deals\Models\DealDocument($this->main);

    //Shop
    $mProduct = new \HubletoApp\Community\Products\Models\Product($this->main);
    $mGroup = new \HubletoApp\Community\Products\Models\Group($this->main);
    $mSupplier = new \HubletoApp\Community\Products\Models\Supplier($this->main);

    if (
      $this->main->apps->isAppInstalled("HubletoApp\Community\Documents") &&
      $this->main->apps->isAppInstalled("HubletoApp\Community\Customers") &&
      $this->main->apps->isAppInstalled("HubletoApp\Community\Contacts")
    ) {
      $this->generateCustomers($mCustomer, $mCustomerTag);
      $this->generateContacts($mContact, $mContactTag, $mValue);
    }

    $this->generateActivities($mCustomer, $mCustomerActivity);

    if (
      $this->main->apps->isAppInstalled("HubletoApp\Community\Customers") &&
      $this->main->apps->isAppInstalled("HubletoApp\Community\Documents") &&
      $this->main->apps->isAppInstalled("HubletoApp\Community\Products") &&
      $this->main->apps->isAppInstalled("HubletoApp\Community\Deals") &&
      $this->main->apps->isAppInstalled("HubletoApp\Community\Leads")
    ) {
      $this->generateLeads($mCustomer, $mLead, $mLeadHistory, $mLeadTag, $mLeadActivity);
      $this->generateDeals($mLead, $mLeadHistory, $mLeadTag, $mDeal, $mDealHistory, $mDealTag, $mDealActivity);
    }
    if ($this->main->apps->isAppInstalled("HubletoApp\Community\Products")) {
      $this->generateProducts($mProduct,$mGroup, $mSupplier);
    }

    foreach ($this->main->apps->getInstalledAppNamespaces() as $appNamespace => $appConfig) {
      $this->main->apps->getAppInstance($appNamespace)->generateDemoData();
    }

    $this->cli->cyan("Demo data generated. Administrator email (login) is now 'demo@hubleto.com' and password is 'demo'.\n");

    $this->main->permissions->revokeGrantAllPermissions();
  }

  public function generateInvoiceProfiles(): void
  {
    $mInvoiceProfile = new \HubletoApp\Community\Settings\Models\InvoiceProfile($this->main);
    $mInvoiceProfile->install();
    $mInvoiceProfile = $mInvoiceProfile->record->recordCreate([
      "name" => "Test Invoice Profile"
    ]);
  }

  public function generateCustomers(
    \HubletoApp\Community\Customers\Models\Customer $mCustomer,
    \HubletoApp\Community\Customers\Models\CustomerTag $mCustomerTag,
  ): void {

    $customersCsv = trim('
209,12354678,Slovak Telecom,83104,Karadžičova 10,,Bratislava,Bratislavský kraj,SK1234567890,SK2021234567,,TRUE
209,8765321,Tatrabanka,81101,Hodžovo námestie 3,,Bratislava,Bratislavský kraj,SK0987654321,SK2029876543,,TRUE
76,552001234,BNP Paribas,75008,1 Boulevard Haussmann,,Paris,Ile-de-France,FR12345678901,FR12345678901,,TRUE
80,7493847,HSBC Bank plc,EC2M 2AA,8 Canada Square,,London,England,GB123456789,GB123456789,,TRUE
210,12385678,NLB d.d.,1000,Trg Republike 2,,Ljubljana,Central Slovenia,SI12345678,SI202123456,,TRUE
60,12345678911,Deutsche Bank AG,60311,Tausendfüßler 2,,Frankfurt,Hesse,DE123456789012,DE123456789012,,TRUE
102,12375678,OTP Bank Nyrt.,1051,Nádor utca 16,,Budapest,Central Hungary,HU12345678,HU12345678,,TRUE
70,B12345678,Banco Santander,28013,Calle de Alcalá 42,,Madrid,Community of Madrid,ESB12345678,ESB12345678,,TRUE
112,12345679601,UniCredit S.p.A.,20121,Piazza Gae Aulenti 3,,Milan,Lombardy,IT12345678901,IT12345678901,,TRUE
59,12335678,Česká spořitelna,11000,Olbrachtova 1929/62,,Praha,Central Bohemia,CZ12345678,CZ202123456,,TRUE
167,123456789B,Rabobank,3521,Stationsweg 3,,Utrecht,Utrecht,NL123456789B01,NL123456789B01,,TRUE
235,12345789,Goldman Sachs & Co.,10282,200 West Street,,New York,New York,US123456789,US123456789,,TRUE
138,12345678,Banque Marocaine,20000,Rue El Jadida 5,,Casablanca,Grand Casablanca,MA12345678,MA202123456,,TRUE
247,1224567890,First National Bank,2001,1 First Place,,Johannesburg,Gauteng,ZA1234567890,ZA1234567890,,TRUE
143,ABC123456,Mexico National Bank,6600,Paseo de la Reforma 200,,Mexico City,Distrito Federal,MXABC123456,MXABC123456,,TRUE
235,987654321,Morgan Stanley,10036,1585 Broadway,,New York,New York,US987654321,US987654321,,TRUE
112,98865432101,Intesa Sanpaolo S.p.A.,10121,Corso Inghilterra 3,,Turin,Piedmont,IT98765432101,IT98765432101,,TRUE
60,98765432101,Commerzbank AG,60329,Mainzer Landstr. 153,,Frankfurt,Hesse,DE987654321012,DE987654321012,,TRUE
209,87654322,SSE,82109,Drotárska cesta 44,,Bratislava,Bratislavský kraj,SK0987654322,SK2029876544,,TRUE
80,7654321,Barclays Bank plc,E14 5HP,1 Churchill Place,,London,England,GB123987654,GB123987654,,TRUE
76,987354321,Crédit Agricole,75015,91-93 Boulevard Pasteur,,Paris,Ile-de-France,FR98765432101,FR98765432101,,TRUE
209,234156789,ZSE,81108,Čulenova 6,,Bratislava,Bratislavský kraj,SK2345678901,SK2022345678,,TRUE
24,12745678,Postbank,1000,Цар Борис III 136,,Sofia,Sofia-City,BG12345678901,BG12345678901,,TRUE
61,32345678,Banque Indosuez,1000,Rue de Paris 3,,Djibouti,Djibouti,DJ120456789,DJ120456789,,TRUE
27,12346789,Banka Intesa Sanpaolo,Banka Sarajevo 1,Obala Kulina bana 9,,Sarajevo,Sarajevo,BI123456789,BI123456789,,TRUE
42,1234567890123,UBS Group AG,8001,Bahnhofstrasse 45,,Zurich,Zürich,CH1234567890123,CH1234567890123,,TRUE
64,42345678,Banco Popular,10101,Calle El Conde 100,,Santo Domingo,Distrito Nacional,DO1234567890,DO1234567890,,TRUE
235,99765432101,JPMorgan Chase & Co.,10017,270 Park Avenue,,New York,New York,US98765432101,US98765432101,,TRUE
19,18745678901,BNP Paribas Fortis,1000,Warandeberg 3,,Brussels,Brussels-Capital Region,BE0123456789,BE0123456789,,TRUE
17,12345679,Nova Ljubljanska Banka,1000,Trg Republike 2,,Ljubljana,Central Slovenia,SI0123456789,SI0123456789,,TRUE
245,72345678,National Bank of Samoa,2001,Beach Road,,Apia,Upolu,WS0123456789,WS0123456789,,TRUE
235,1122334455,Capital One Financial,22193,15000 Capital One Dr,,McLean,Virginia,US1122334455,US1122334455,,TRUE
247,986354321,Absa Bank Limited,2001,Absa Towers West,,Johannesburg,Gauteng,ZA987654321,ZA987654321,,TRUE
138,987643210,CIH Bank,20100,Rue El Jadida 10,,Casablanca,Grand Casablanca,MA9876543210,MA9876543210,,TRUE
105,1134567890,State Bank of India,110001,"11, Parliament Street",,New Delhi,Delhi,IN1234567890,IN1234567890,,TRUE
17,987674321,Nova Kreditna Banka Maribor,2000,Ul. heroja Staneta 1,,Maribor,Podravska,SI987654321,SI987654321,,TRUE
42,987784321,UBS Bank,8001,Seilergraben 49,,Zurich,Zürich,CH9876543210123,CH9876543210123,,TRUE
112,12987654,Zopa,144,Viale Oceano Pacifico 153,,Rome,Lazio,IT12398765401,IT12398765401,,TRUE
235,543516789,PNC Bank,15222,300 Fifth Avenue,,Pittsburgh,Pennsylvania,US543216789,US543216789,,TRUE
17,76542109,Zagrebačka Banka,10000,Paromlinska 2,,Zagreb,Zagreb City,HR765432109,HR765432109,,TRUE
102,987654781,MKB Bank Nyrt.,1134,Váci út 22-24,,Budapest,Central Hungary,HU987654321,HU987654321,,TRUE
29,78345678,Belarusbank,220036,Nezavisimosti Ave 20,,Minsk,Minsk Region,BY12345678,BY2021234567,,TRUE
48,12345678901,EcoBank Congo,1,Route de Matadi 20,,Kinshasa,Kinshasa,CG12345678901,CG12345678901,,TRUE
110,1233567890,Landsbankinn,103,Austurstræti 11,,Reykjavik,Capital Region,IS1234567890,IS1234567890,,TRUE
18,1234567890,Burundi Commercial Bank,12345,3 Avenue de l\'Union,,Bujumbura,Mairie de Bujumbura,BI1234567890,BI1234567890,,TRUE
142,93345678,Bank of Madagascar,101,3 Rue de la Nation,,Antananarivo,Analamanga,MG12345678,MG2021234567,,TRUE
125,987654398,Lao Development Bank,1000,Lane Xang Avenue,,Vientiane,Vientiane Prefecture,LA987654321,LA987654321,,TRUE
72,23456789,Ethiopian Commercial Bank,1000,Churchill Avenue,,Addis Ababa,Addis Ababa,ET123456789,ET123456789,,TRUE
54,123456789,CRDB Bank Plc,14100,Azikiwe Street 20,,Dar es Salaam,Dar es Salaam,TZ123456789,TZ123456789,,TRUE
80,19837465,Lloyds Bank plc,EC2V 7HN,25 Gresham Street,,London,England,GB192837465,GB192837465,,TRUE
247,8765432101,Standard Chartered,2001,1 Basinghall Avenue,,Johannesburg,Gauteng,ZA8765432101,ZA8765432101,,TRUE
143,543246789,Bancomer,6600,Paseo de la Reforma 250,,Mexico City,Distrito Federal,MX543216789,MX543216789,,TRUE
125,19237465,BCEL,1000,Kaysone Phomvihane Ave,,Vientiane,Vientiane Prefecture,LA192837465,LA192837465,,TRUE
142,976543210,Bank of Africa,101,2 Rue de l\'Indépendance,,Antananarivo,Analamanga,MG9876543210,MG9876543210,,TRUE
247,19283765,FirstRand Bank,2001,4 Merchant Place,,Johannesburg,Gauteng,ZA192837465,ZA192837465,,TRUE
17,543296789,Raiffeisenbank Austria d.d.,10000,Petrinjska 59,,Zagreb,Zagreb City,HR543216789,HR543216789,,TRUE
110,987654210,Arion Bank,103,Borgartún 19,,Reykjavik,Capital Region,IS9876543210,IS9876543210,,TRUE
209,765472109,ČSOB,81106,Štúrova 5,,Bratislava,Bratislavský kraj,SK765432109,SK2027659321,,TRUE
27,987543210,UniCredit Bank d.d.,71000,Zmaja od Bosne bb,,Sarajevo,Sarajevo,BA9876543210,BA9876543210,,TRUE
105,765439109,HDFC Bank,400013,"HDFC Bank House, Lower Parel",,Mumbai,Maharashtra,IN765432109,IN765432109,,TRUE
64,9854321,Scotiabank,10101,Av. Winston Churchill,,Santo Domingo,Distrito Nacional,DO987654321,DO987654321,,TRUE
102,12397654,K&H Bank Zrt.,1095,Lechner Ödön fasor 9,,Budapest,Central Hungary,HU123987654,HU123987654,,TRUE
142,543216789,Banky Fampandrosoana Malagasy,101,4 Rue de la Liberté,,Antananarivo,Analamanga,MG543216789,MG543216789,,TRUE
61,87654210,Banque de Djibouti,0,Place Lagarde,,Djibouti,Djibouti,DJ876543210,DJ876543210,,TRUE
247,76532109,Nedbank Group,2001,135 Rivonia Road,,Johannesburg,Gauteng,ZA765432109,ZA765432109,,TRUE
24,9876543210,United Bulgarian Bank,1000,Knyaz Alexander I 12,,Sofia,Sofia-City,BG9876543210,BG9876543210,,TRUE
60,8765432109,DZ Bank AG,60325,Platz der Republik,,Frankfurt,Hesse,DE876543210912,DE876543210912,,TRUE
61,12356789,Djibouti Commercial Bank,0,Avenue 26 Juin,,Djibouti,Djibouti,DJ123456789,DJ123456789,,TRUE
209,123987654,VÚB Banka,81294,Mlynské nivy 1,,Bratislava,Bratislavský kraj,SK123987654,SK2021239876,,TRUE
9,765432109,BICIGUI,230,Rue KA 002,,Conakry,Conakry,GN765432109,GN765432109,,TRUE
24,143216789,First Investment Bank,1000,179 Tsar Boris III Blvd,,Sofia,Sofia-City,BG543216789,BG543216789,,TRUE
76,192837765,Société Générale,75008,29 Boulevard Haussmann,,Paris,Ile-de-France,FR19283746501,FR19283746501,,TRUE
209,98765421,Tatra Leasing,82108,Prievozská 4D,,Bratislava,Bratislavský kraj,SK987654321,SK2729876544,,TRUE
235,513216789,Wells Fargo & Company,94105,420 Montgomery Street,,San Francisco,California,US573216789,US543216783,,TRUE
110,199837465,Íslandsbanki,103,Kirkjusandur 2,,Reykjavik,Capital Region,IS192837465,IS192837465,,TRUE
61,543226789,Development Bank of Djibouti,0,Boulevard de la République,,Djibouti,Djibouti,DJ543216789,DJ543216789,,TRUE
9,12347789,Société Générale de Banques en Guinée,1000,Avenue de la République,,Conakry,Conakry,GN123456789,GN123456789,,TRUE
235,192537465,Bank of America,30309,600 Peachtree St. NE,,Atlanta,Georgia,US192837465,US192837465,,TRUE
143,98265432,Banamex,6600,Paseo de la Reforma 250,,Mexico City,Distrito Federal,MX098765432,MX098765432,,TRUE
16,76043210,UniCredit Bank Austria AG,1010,Rothschildplatz 1,,Vienna,Vienna,AT876543210,AT876543210,,TRUE
48,876532101,Rawbank SA,24310,Avenue Kasa-Vubu 10,,Kinshasa,Kinshasa,CG8765432101,CG8765432101,,TRUE
105,192837465,Axis Bank,400005,"14th Floor, Tower A, Peninsula Business Park",,Mumbai,Maharashtra,IN192837465,IN192837465,,TRUE
112,87654101,Banco di Sardegna S.p.A.,9124,Viale Bonaria 2,,Cagliari,Sardinia,IT8765432101,IT8765432101,,TRUE
152,12340689,National Bank of Cambodia,12202,26 Monivong Blvd,,Phnom Penh,Phnom Penh,KH123456789,KH123456789,,TRUE
54,90765432,Equity Bank Tanzania Limited,14100,"2nd Floor, Golden Jubilee Towers",,Dar es Salaam,Dar es Salaam,TZ987654321,TZ987654321,,TRUE
138,192937465,Banque Centrale Populaire,20000,Boulevard Zerktouni,,Casablanca,Grand Casablanca,MA192837465,MA192837465,,TRUE
49,12345689,Banque de l\'Habitat,1002,12 Avenue de la Liberté,,Tunis,Tunis,TI123456789,TI123456789,,TRUE
5,87654320,Union Bank of Nigeria,101001,36 Marina,,Lagos,Lagos,NG876543210,NG876543210,,TRUE
112,543266789,Banca Popolare di Milano,20121,Piazza Meda 4,,Milan,Lombardy,IT54321678901,IT54321678901,,TRUE
60,543214789,Volksbank AG,10785,Stauffenbergstrasse 7,,Berlin,Berlin,DE54321678901,DE54321678901,,TRUE
209,76543210,Všeobecná úverová banka,82005,Mlynské nivy 1,,Bratislava,Bratislavský kraj,SK76543210,SK2027654321,,TRUE
67,87654321,Commercial Bank of Kenya,200,Kenyatta Avenue,,Nairobi,Nairobi,KE987654321,KE987654321,,TRUE
247,87543210,Standard Bank,2001,30 Baker Street,,Johannesburg,Gauteng,ZA876543210,ZA876543210,,TRUE
125,54316789,Banque pour le Commerce Extérieur Lao,1000,2 Pangkham St,,Vientiane,Vientiane Prefecture,LA543216789,LA543216789,,TRUE
245,543213789,Banco Nacional de Samoa,0,Beach Road,,Apia,Upolu,WS987654321,WS987654321,,TRUE
59,192865,Raiffeisenbank,14000,Olbrachtova 9,,Praha,Central Bohemia,CZ192837465,CZ202192837,,TRUE
112,98765432,Intesa Sanpaolo,10121,Corso Inghilterra 3,,Turin,Piedmont,IT0987654321,IT0987654321,,TRUE
247,543416789,Nedbank,2001,135 Rivonia Road,,Johannesburg,Gauteng,ZA543216789,ZA543216789,,TRUE
125,876543210,Banque Franco-Lao Ltd.,1000,Lane Xang Ave,,Vientiane,Vientiane Prefecture,LA876543210,LA876543210,,TRUE
49,97654321,Amen Bank,1001,76 Avenue Mohamed V,,Tunis,Tunis,TI987654321,TI987654321,,TRUE
    ');

    $customers = explode("\n", $customersCsv);

    foreach ($customers as $customerCsvData) {
      $customer = explode(",", trim($customerCsvData));

      $idCustomer = $mCustomer->record->recordCreate([
        "id_country" => $customer[0],
        "customer_id" => $customer[1],
        "name" => $customer[2],
        "postal_code" => $customer[3],
        "street_line_1" => $customer[4],
        "street_line_2" => $customer[5],
        "city" => $customer[6],
        "region" => $customer[7],
        "tax_id" => $customer[8],
        "vat_id" => $customer[9],
        "note" => $customer[10],
        "is_active" => rand(0, 1),
        "id_owner" => rand(1, 4),
        "id_responsible" => rand(1, 4),
        "date_created" => date("Y-m-d", rand(1722456000, strtotime("now"))),
      ])['id'];

      $tags = [];
      $tagsCount = (rand(1, 3) == 1 ? rand(1, 2) : 1);
      while (count($tags) < $tagsCount) {
        $idTag = rand(1, 3);
        if (!in_array($idTag, $tags)) $tags[] = $idTag;
      }

      foreach ($tags as $idTag) {
        $mCustomerTag->record->recordCreate([
          "id_customer" => $idCustomer,
          "id_tag" => $idTag,
        ]);
      }
    }
  }

  public function generateContacts(
    \HubletoApp\Community\Contacts\Models\Contact $mContact,
    \HubletoApp\Community\Contacts\Models\ContactTag $mContactTag,
    \HubletoApp\Community\Contacts\Models\Value $mValue,
  ): void {

    $contacts = [
      ["Mechelle", "Stoneman", "Mechelle.Stoneman@dummy.example.com" ],
      ["Tyesha", "Freitag", "Tyesha.Freitag@dummy.example.com" ],
      ["Dean", "Stoecker", "Dean.Stoecker@dummy.example.com" ],
      ["Annelle", "Pickney", "Annelle.Pickney@dummy.example.com" ],
      ["Margareta", "Tacy", "Margareta.Tacy@dummy.example.com" ],
      ["Meghann", "Placencia", "Meghann.Placencia@dummy.example.com" ],
      ["Kendrick", "Cieslak", "Kendrick.Cieslak@dummy.example.com" ],
      ["Polly", "Isenberg", "Polly.Isenberg@dummy.example.com" ],
      ["Evelyne", "Racicot", "Evelyne.Racicot@dummy.example.com" ],
      ["Augustus", "Delaune", "Augustus.Delaune@dummy.example.com" ],
      ["Shawanda", "Client", "Shawanda.Client@dummy.example.com" ],
      ["Loura", "Coffield", "Loura.Coffield@dummy.example.com" ],
      ["Lorriane", "Machin", "Lorriane.Machin@dummy.example.com" ],
      ["Lacey", "Osier", "Lacey.Osier@dummy.example.com" ],
      ["Nicki", "Malchow", "Nicki.Malchow@dummy.example.com" ],
      ["Sidney", "Bodiford", "Sidney.Bodiford@dummy.example.com" ],
      ["Barbie", "Cun", "Barbie.Cun@dummy.example.com" ],
      ["Elden", "Hanshaw", "Elden.Hanshaw@dummy.example.com" ],
      ["Blossom", "Loggins", "Blossom.Loggins@dummy.example.com" ],
      ["Joseph", "Dennie", "Joseph.Dennie@dummy.example.com" ],
      ["Pattie", "Markley", "Pattie.Markley@dummy.example.com" ],
      ["Genevieve", "Spahn", "Genevieve.Spahn@dummy.example.com" ],
      ["Luciano", "Jaworski", "Luciano.Jaworski@dummy.example.com" ],
      ["Noe", "Mahler", "Noe.Mahler@dummy.example.com" ],
      ["Karri", "Bransford", "Karri.Bransford@dummy.example.com" ],
      ["Larae", "Bonney", "Larae.Bonney@dummy.example.com" ],
      ["Sharita", "Fierros", "Sharita.Fierros@dummy.example.com" ],
      ["Frederica", "Perla", "Frederica.Perla@dummy.example.com" ],
      ["Mara", "Elder", "Mara.Elder@dummy.example.com" ],
      ["Enola", "Volz", "Enola.Volz@dummy.example.com" ],
      ["Leslie", "Mccardell", "Leslie.Mccardell@dummy.example.com" ],
      ["Gina", "Coria", "Gina.Coria@dummy.example.com" ],
      ["Marietta", "Taing", "Marietta.Taing@dummy.example.com" ],
      ["Karlyn", "Buchholtz", "Karlyn.Buchholtz@dummy.example.com" ],
      ["Herma", "Renken", "Herma.Renken@dummy.example.com" ],
      ["Gertrud", "Gillispie", "Gertrud.Gillispie@dummy.example.com" ],
      ["Kelsie", "Lavoie", "Kelsie.Lavoie@dummy.example.com" ],
      ["Selena", "Jenney", "Selena.Jenney@dummy.example.com" ],
      ["Teri", "Schooley", "Teri.Schooley@dummy.example.com" ],
      ["Lizette", "Campana", "Lizette.Campana@dummy.example.com" ],
      ["Mayra", "Luby", "Mayra.Luby@dummy.example.com" ],
      ["Luisa", "Finneran", "Luisa.Finneran@dummy.example.com" ],
      ["Genoveva", "Herrod", "Genoveva.Herrod@dummy.example.com" ],
      ["Tandra", "Toon", "Tandra.Toon@dummy.example.com" ],
      ["Zoe", "Mangrum", "Zoe.Mangrum@dummy.example.com" ],
      ["Marquerite", "Salaam", "Marquerite.Salaam@dummy.example.com" ],
      ["Alva", "Fonte", "Alva.Fonte@dummy.example.com" ],
      ["Maudie", "Cage", "Maudie.Cage@dummy.example.com" ],
      ["Hilde", "Greaves", "Hilde.Greaves@dummy.example.com" ],
      ["Christiana", "Rippe", "Christiana.Rippe@dummy.example.com" ],
      ["Bulah", "Warr", "Bulah.Warr@dummy.example.com" ],
      ["Azzie", "Stolte", "Azzie.Stolte@dummy.example.com" ],
      ["Sharri", "Whistler", "Sharri.Whistler@dummy.example.com" ],
      ["Rebecka", "Holliman", "Rebecka.Holliman@dummy.example.com" ],
      ["Bryce", "Muse", "Bryce.Muse@dummy.example.com" ],
      ["Merideth", "Marcus", "Merideth.Marcus@dummy.example.com" ],
      ["Nova", "Boden", "Nova.Boden@dummy.example.com" ],
      ["Granville", "Watchman", "Granville.Watchman@dummy.example.com" ],
      ["Marquerite", "Dearborn", "Marquerite.Dearborn@dummy.example.com" ],
      ["Arielle", "Ketcham", "Arielle.Ketcham@dummy.example.com" ],
      ["Shona", "Buggs", "Shona.Buggs@dummy.example.com" ],
      ["Aleta", "Ciesla", "Aleta.Ciesla@dummy.example.com" ],
      ["Jenni", "Wichman", "Jenni.Wichman@dummy.example.com" ],
      ["Cuc", "Phinney", "Cuc.Phinney@dummy.example.com" ],
      ["Danica", "Fleig", "Danica.Fleig@dummy.example.com" ],
      ["Shaquana", "Emigh", "Shaquana.Emigh@dummy.example.com" ],
      ["Brinda", "Master", "Brinda.Master@dummy.example.com" ],
      ["Janell", "Hinojos", "Janell.Hinojos@dummy.example.com" ],
      ["Karri", "Celestin", "Karri.Celestin@dummy.example.com" ],
      ["Enid", "Bouley", "Enid.Bouley@dummy.example.com" ],
      ["Desire", "Klenke", "Desire.Klenke@dummy.example.com" ],
      ["Brian", "Chamberlain", "Brian.Chamberlain@dummy.example.com" ],
      ["Kristeen", "Farabaugh", "Kristeen.Farabaugh@dummy.example.com" ],
      ["Gilda", "Vanatta", "Gilda.Vanatta@dummy.example.com" ],
      ["Princess", "Bumbrey", "Princess.Bumbrey@dummy.example.com" ],
      ["Earlie", "Townson", "Earlie.Townson@dummy.example.com" ],
      ["Fumiko", "Laliberte", "Fumiko.Laliberte@dummy.example.com" ],
      ["Sherie", "Chason", "Sherie.Chason@dummy.example.com" ],
      ["Gricelda", "Maravilla", "Gricelda.Maravilla@dummy.example.com" ],
      ["Serafina", "Knoll", "Serafina.Knoll@dummy.example.com" ],
      ["Sachiko", "Younkin", "Sachiko.Younkin@dummy.example.com" ],
      ["Jena", "Noles", "Jena.Noles@dummy.example.com" ],
      ["Shelby", "Wiser", "Shelby.Wiser@dummy.example.com" ],
      ["Margareta", "Spies", "Margareta.Spies@dummy.example.com" ],
      ["Dinorah", "Furey", "Dinorah.Furey@dummy.example.com" ],
      ["Kiyoko", "Lechuga", "Kiyoko.Lechuga@dummy.example.com" ],
      ["Danny", "Kreger", "Danny.Kreger@dummy.example.com" ],
      ["Nolan", "Fenwick", "Nolan.Fenwick@dummy.example.com" ],
      ["Lesli", "Unrein", "Lesli.Unrein@dummy.example.com" ],
      ["Donya", "Bartle", "Donya.Bartle@dummy.example.com" ],
      ["Palma", "Flanery", "Palma.Flanery@dummy.example.com" ],
      ["Kenny", "Clothier", "Kenny.Clothier@dummy.example.com" ],
      ["Kristen", "Lossing", "Kristen.Lossing@dummy.example.com" ],
      ["Karoline", "Felix", "Karoline.Felix@dummy.example.com" ],
      ["Alan", "Voll", "Alan.Voll@dummy.example.com" ],
      ["Glenda", "Woolfolk", "Glenda.Woolfolk@dummy.example.com" ],
      ["Alyson", "Hosack", "Alyson.Hosack@dummy.example.com" ],
      ["Francis", "Sines", "Francis.Sines@dummy.example.com" ],
      ["Riley", "Bagnall", "Riley.Bagnall@dummy.example.com" ],
      ["Carmina", "Camp", "Carmina.Camp@dummy.example.com" ],
      ["Alethia", "Tiemann", "Alethia.Tiemann@dummy.example.com" ],
      ["Deborah", "Molinar", "Deborah.Molinar@dummy.example.com" ],
      ["Marvella", "Huckstep", "Marvella.Huckstep@dummy.example.com" ],
      ["Sallie", "Briley", "Sallie.Briley@dummy.example.com" ],
      ["Scottie", "Backer", "Scottie.Backer@dummy.example.com" ],
      ["Beatriz", "Kinsey", "Beatriz.Kinsey@dummy.example.com" ],
      ["Mason", "Carrow", "Mason.Carrow@dummy.example.com" ],
      ["Regenia", "Blish", "Regenia.Blish@dummy.example.com" ],
      ["Necole", "Faria", "Necole.Faria@dummy.example.com" ],
      ["Samantha", "Hadfield", "Samantha.Hadfield@dummy.example.com" ],
      ["Lida", "Sing", "Lida.Sing@dummy.example.com" ],
      ["Bette", "Church", "Bette.Church@dummy.example.com" ],
      ["Illa", "Friscia", "Illa.Friscia@dummy.example.com" ],
      ["Magdalena", "Clabaugh", "Magdalena.Clabaugh@dummy.example.com" ],
      ["Sol", "Lemley", "Sol.Lemley@dummy.example.com" ],
      ["Angelyn", "Nave", "Angelyn.Nave@dummy.example.com" ],
      ["Lorie", "Hempstead", "Lorie.Hempstead@dummy.example.com" ],
      ["Darlena", "Brubaker", "Darlena.Brubaker@dummy.example.com" ],
      ["Ivory", "Almonte", "Ivory.Almonte@dummy.example.com" ],
      ["Keva", "Sauage", "Keva.Sauage@dummy.example.com" ],
      ["Krystin", "Morita", "Krystin.Morita@dummy.example.com" ],
      ["Margarito", "Hintzen", "Margarito.Hintzen@dummy.example.com" ],
      ["Alanna", "Gillispie", "Alanna.Gillispie@dummy.example.com" ],
      ["Alayna", "Rosenblatt", "Alayna.Rosenblatt@dummy.example.com" ],
      ["Deeann", "Thomsen", "Deeann.Thomsen@dummy.example.com" ],
      ["Guy", "Moulton", "Guy.Moulton@dummy.example.com" ],
      ["Ming", "Scudder", "Ming.Scudder@dummy.example.com" ],
      ["Mickey", "Espino", "Mickey.Espino@dummy.example.com" ],
      ["Mellissa", "Mortimore", "Mellissa.Mortimore@dummy.example.com" ],
      ["Lesia", "Stoute", "Lesia.Stoute@dummy.example.com" ],
      ["Pauletta", "Murton", "Pauletta.Murton@dummy.example.com" ],
      ["Solomon", "Chamberlin", "Solomon.Chamberlin@dummy.example.com" ],
      ["Neville", "Yocom", "Neville.Yocom@dummy.example.com" ],
      ["Vera", "Edmiston", "Vera.Edmiston@dummy.example.com" ],
      ["Lawerence", "Amburn", "Lawerence.Amburn@dummy.example.com" ],
      ["Cedrick", "Agnew", "Cedrick.Agnew@dummy.example.com" ],
      ["Melinda", "Kuchta", "Melinda.Kuchta@dummy.example.com" ],
      ["Alma", "Gelinas", "Alma.Gelinas@dummy.example.com" ],
      ["Francis", "Sikes", "Francis.Sikes@dummy.example.com" ],
      ["Minerva", "Giles", "Minerva.Giles@dummy.example.com" ],
      ["Nikki", "Iskra", "Nikki.Iskra@dummy.example.com" ],
      ["Lore", "Coil", "Lore.Coil@dummy.example.com" ],
      ["Marlena", "Craner", "Marlena.Craner@dummy.example.com" ],
      ["Darius", "Trojacek", "Darius.Trojacek@dummy.example.com" ],
      ["Sade", "Gasaway", "Sade.Gasaway@dummy.example.com" ],
      ["Sybil", "Rahman", "Sybil.Rahman@dummy.example.com" ],
      ["Zoraida", "Sumner", "Zoraida.Sumner@dummy.example.com" ],
      ["Raina", "Mccrae", "Raina.Mccrae@dummy.example.com" ],
      ["Elsa", "Mcspadden", "Elsa.Mcspadden@dummy.example.com" ],
      ["Bernadine", "Chung", "Bernadine.Chung@dummy.example.com" ],
      ["Francie", "Frase", "Francie.Frase@dummy.example.com" ],
      ["Mariana", "Vavra", "Mariana.Vavra@dummy.example.com" ],
      ["Nakita", "Primer", "Nakita.Primer@dummy.example.com" ],
      ["Aletha", "Hardesty", "Aletha.Hardesty@dummy.example.com" ],
      ["Dwain", "Sargeant", "Dwain.Sargeant@dummy.example.com" ],
      ["Thea", "Hubbs", "Thea.Hubbs@dummy.example.com" ],
      ["Caleb", "Peters", "Caleb.Peters@dummy.example.com" ],
      ["Sparkle", "Kaestner", "Sparkle.Kaestner@dummy.example.com" ],
      ["Narcisa", "Hsieh", "Narcisa.Hsieh@dummy.example.com" ],
      ["Sherwood", "Vanalstyne", "Sherwood.Vanalstyne@dummy.example.com" ],
      ["Jeanice", "Joy", "Jeanice.Joy@dummy.example.com" ],
      ["Bert", "Riter", "Bert.Riter@dummy.example.com" ],
      ["Dorotha", "Aldinger", "Dorotha.Aldinger@dummy.example.com" ],
      ["Anisha", "Thomson", "Anisha.Thomson@dummy.example.com" ],
      ["Rufus", "Amerine", "Rufus.Amerine@dummy.example.com" ],
      ["Roslyn", "Alaimo", "Roslyn.Alaimo@dummy.example.com" ],
      ["Noelle", "Raybon", "Noelle.Raybon@dummy.example.com" ],
      ["Shanae", "Hanger", "Shanae.Hanger@dummy.example.com" ],
      ["William", "Hopf", "William.Hopf@dummy.example.com" ],
      ["Adolfo", "Bella", "Adolfo.Bella@dummy.example.com" ],
      ["Xenia", "Schubert", "Xenia.Schubert@dummy.example.com" ],
      ["Brenton", "Tokarski", "Brenton.Tokarski@dummy.example.com" ],
      ["Hal", "Bender", "Hal.Bender@dummy.example.com" ],
      ["Geraldine", "Border", "Geraldine.Border@dummy.example.com" ],
      ["Setsuko", "Pardo", "Setsuko.Pardo@dummy.example.com" ],
      ["Meghan", "Sydnor", "Meghan.Sydnor@dummy.example.com" ],
      ["Lavern", "Gard", "Lavern.Gard@dummy.example.com" ],
      ["Cyrus", "Beckham", "Cyrus.Beckham@dummy.example.com" ],
      ["Leeanne", "Fortunato", "Leeanne.Fortunato@dummy.example.com" ],
      ["Nilda", "Deyoung", "Nilda.Deyoung@dummy.example.com" ],
      ["Marylee", "Greenburg", "Marylee.Greenburg@dummy.example.com" ],
      ["Gay", "Aubert", "Gay.Aubert@dummy.example.com" ],
      ["Janel", "Carley", "Janel.Carley@dummy.example.com" ],
      ["Damaris", "Nestle", "Damaris.Nestle@dummy.example.com" ],
      ["Jeanine", "Hoerr", "Jeanine.Hoerr@dummy.example.com" ],
      ["Wiley", "Scotto", "Wiley.Scotto@dummy.example.com" ],
      ["Dian", "Cobian", "Dian.Cobian@dummy.example.com" ],
      ["Brendan", "Zilnicki", "Brendan.Zilnicki@dummy.example.com" ],
      ["Mana", "Seegmiller", "Mana.Seegmiller@dummy.example.com" ],
      ["Flavia", "Nitta", "Flavia.Nitta@dummy.example.com" ],
      ["Humberto", "Ware", "Humberto.Ware@dummy.example.com" ],
      ["Ned", "Permenter", "Ned.Permenter@dummy.example.com" ],
      ["Albertina", "Junkin", "Albertina.Junkin@dummy.example.com" ],
      ["Rosia", "Duron", "Rosia.Duron@dummy.example.com" ],
      ["Kena", "Stallings", "Kena.Stallings@dummy.example.com" ],
      ["Lolita", "Pringle", "Lolita.Pringle@dummy.example.com" ],
      ["Kristen", "Gilley", "Kristen.Gilley@dummy.example.com" ],
      ["Genaro", "Koga", "Genaro.Koga@dummy.example.com" ],
      ["Suzanna", "Putman", "Suzanna.Putman@dummy.example.com" ],
      ["Candy", "Konieczny", "Candy.Konieczny@dummy.example.com" ],
      ["Ozella", "Conner", "Ozella.Conner@dummy.example.com" ],
      ["Leonel", "Hock", "Leonel.Hock@dummy.example.com" ],
      ["Vannesa", "Millard", "Vannesa.Millard@dummy.example.com" ],
      ["Melonie", "Villacorta", "Melonie.Villacorta@dummy.example.com" ],
      ["Dorine", "Zeitler", "Dorine.Zeitler@dummy.example.com" ],
      ["Bridget", "Conyers", "Bridget.Conyers@dummy.example.com" ],
      ["Jacklyn", "Dyment", "Jacklyn.Dyment@dummy.example.com" ],
      ["Felicita", "Maclachlan", "Felicita.Maclachlan@dummy.example.com" ],
      ["Herbert", "Gamache", "Herbert.Gamache@dummy.example.com" ],
      ["Antonietta", "Llewellyn", "Antonietta.Llewellyn@dummy.example.com" ],
      ["Pennie", "Alling", "Pennie.Alling@dummy.example.com" ],
      ["Laree", "Kay", "Laree.Kay@dummy.example.com" ],
      ["Antonio", "Navarette", "Antonio.Navarette@dummy.example.com" ],
      ["Steve", "Mainor", "Steve.Mainor@dummy.example.com" ],
      ["Gertrud", "Sather", "Gertrud.Sather@dummy.example.com" ],
      ["Nan", "Beverley", "Nan.Beverley@dummy.example.com" ],
      ["Walter", "Belford", "Walter.Belford@dummy.example.com" ],
      ["Colby", "Hobart", "Colby.Hobart@dummy.example.com" ],
      ["Patrick", "Nocera", "Patrick.Nocera@dummy.example.com" ],
      ["Hilary", "Modeste", "Hilary.Modeste@dummy.example.com" ],
      ["Elke", "Licht", "Elke.Licht@dummy.example.com" ],
      ["Faustina", "Dunson", "Faustina.Dunson@dummy.example.com" ],
      ["Tara", "Clingman", "Tara.Clingman@dummy.example.com" ],
      ["Elva", "Hochmuth", "Elva.Hochmuth@dummy.example.com" ],
      ["Opal", "Groover", "Opal.Groover@dummy.example.com" ],
      ["Maryln", "Ferris", "Maryln.Ferris@dummy.example.com" ],
      ["Katrina", "Almon", "Katrina.Almon@dummy.example.com" ],
      ["Stephane", "Labelle", "Stephane.Labelle@dummy.example.com" ],
      ["Christene", "Gloria", "Christene.Gloria@dummy.example.com" ],
      ["Araceli", "Majewski", "Araceli.Majewski@dummy.example.com" ],
      ["Marilou", "Funderburg", "Marilou.Funderburg@dummy.example.com" ],
      ["America", "Tocci", "America.Tocci@dummy.example.com" ],
      ["Erich", "Ragin", "Erich.Ragin@dummy.example.com" ],
      ["Neoma", "Pellegrino", "Neoma.Pellegrino@dummy.example.com" ],
      ["Sid", "Lamore", "Sid.Lamore@dummy.example.com" ],
      ["Stella", "Morman", "Stella.Morman@dummy.example.com" ],
      ["Ha", "Durrah", "Ha.Durrah@dummy.example.com" ],
      ["Chantel", "Absher", "Chantel.Absher@dummy.example.com" ],
      ["Torri", "Wert", "Torri.Wert@dummy.example.com" ],
      ["Michelina", "Holscher", "Michelina.Holscher@dummy.example.com" ],
      ["Verlene", "Arviso", "Verlene.Arviso@dummy.example.com" ],
      ["Lois", "Tew", "Lois.Tew@dummy.example.com" ],
      ["Miguelina", "Hoyte", "Miguelina.Hoyte@dummy.example.com" ],
      ["Evelina", "Willaert", "Evelina.Willaert@dummy.example.com" ],
      ["Ranae", "Topp", "Ranae.Topp@dummy.example.com" ],
      ["Carolann", "Veasley", "Carolann.Veasley@dummy.example.com" ],
      ["Jeannette", "Gravelle", "Jeannette.Gravelle@dummy.example.com" ],
      ["Lenita", "Slevin", "Lenita.Slevin@dummy.example.com" ],
      ["Valentina", "Quinby", "Valentina.Quinby@dummy.example.com" ],
      ["Latoyia", "Bushway", "Latoyia.Bushway@dummy.example.com" ],
      ["Keitha", "Wold", "Keitha.Wold@dummy.example.com" ],
      ["Flora", "Plascencia", "Flora.Plascencia@dummy.example.com" ],
      ["Azucena", "Herder", "Azucena.Herder@dummy.example.com" ],
      ["Kathi", "Lilienthal", "Kathi.Lilienthal@dummy.example.com" ],
      ["Andrea", "Saine", "Andrea.Saine@dummy.example.com" ],
      ["Shawna", "Riviera", "Shawna.Riviera@dummy.example.com" ],
      ["Nolan", "Harwell", "Nolan.Harwell@dummy.example.com" ],
      ["Jennell", "Leverich", "Jennell.Leverich@dummy.example.com" ],
      ["Birgit", "Puente", "Birgit.Puente@dummy.example.com" ],
      ["Gerry", "Medal", "Gerry.Medal@dummy.example.com" ],
      ["Palma", "Sample", "Palma.Sample@dummy.example.com" ],
      ["Annalisa", "Rotolo", "Annalisa.Rotolo@dummy.example.com" ],
      ["Eilene", "Jolly", "Eilene.Jolly@dummy.example.com" ],
      ["Kathey", "Keep", "Kathey.Keep@dummy.example.com" ],
    ];

    $cities = [
      "Tokyo",
      "New York",
      "London",
      "Paris",
      "Beijing",
      "Mumbai",
      "Shanghai",
      "São Paulo",
      "Delhi",
      "Cairo",
    ];

    $streets = [
      "1st Avenue",
      "5th Avenue",
      "Oxford Street",
      "Champs-Élysées",
      "Wangfujing Street",
      "Colaba Causeway",
      "Nanjing Road",
      "Avenida Paulista",
      "Connaught Place",
      "Al-Muqarrama Street",
    ];

    $postalCodes = [
      "10001", // New York
      "SW1A 2AA", // London
      "75008", // Paris
      "100081", // Beijing
      "400001", // Mumbai
      "200001", // Delhi
      "01001", // São Paulo
      "02441", // Cairo
      "16000", // Tokyo
      "200000", // Shanghai
    ];

    $regions = [
      "New York State",
      "Greater London",
      "Île-de-France",
      "Beijing Municipality",
      "Maharashtra",
      "National Capital Territory of Delhi",
      "São Paulo State",
      "Cairo Governorate",
      "Kantō Region",
      "Shanghai Municipality",
    ];

    $isPrimary = true;

    $salutations = ["Mr.", "Mrs.", "Miss"];
    $titlesBefore = ["", "Dr.", "MSc."];
    $titlesAfter = ["", "MBA", "PhD."];

    foreach ($contacts as $contact) {
      $idContact = $mContact->record->recordCreate([
        "id_customer" => rand(1, 100),
        "salutation" => $salutations[rand(0, 2)],
        "title_before" => $titlesBefore[rand(0, 2)],
        "first_name" => $contact[0],
        "last_name" => $contact[1],
        "title_after" => $titlesAfter[rand(0, 2)],
        "is_primary" => true,
        "is_active" => true,
        "date_created" => date("Y-m-d", rand(strtotime("-1 month"), strtotime("+1 month"))),
      ])['id'];

      $mValue->record->recordCreate([
        "id_contact" => $idContact,
        "type" => "email",
        "value" => $contact[2],
        "id_category" => rand(1, 2),
      ]);

      $mValue->record->recordCreate([
        "id_contact" => $idContact,
        "type" => "url",
        "value" => 'https://www.example.com',
        "id_category" => rand(1, 2),
      ]);

      $phoneNumber = "+1 1" . rand(0, 3) . rand(4, 8) . " " . rand(0, 9) . rand(0, 9) . rand(0, 9) . " " . rand(0, 9) . rand(0, 9) . rand(0, 9);
      $mValue->record->recordCreate([
        "id_contact" => $idContact,
        "type" => "number",
        "value" => $phoneNumber,
        "id_category" => rand(1, 2),
      ]);

      $tags = [];
      $tagsCount = (rand(1, 3) == 1 ? rand(1, 2) : 1);
      while (count($tags) < $tagsCount) {
        $idTag = rand(1, 6);
        if (!in_array($idTag, $tags)) $tags[] = $idTag;
      }

      foreach ($tags as $idTag) {
        $mContactTag->record->recordCreate([
          "id_contact" => $idContact,
          "id_tag" => $idTag,
        ]);
      }

      $isPrimary = false;
    }
  }

  public function generateActivities(
    \HubletoApp\Community\Customers\Models\Customer $mCustomer,
    \HubletoApp\Community\Customers\Models\CustomerActivity $mCustomerActivity,
  ): void {

    $activityTypes = ["Meeting", "Bussiness Trip", "Call", "Email"];
    $minutes = ["00", "15", "30", "45"];
    $customers = $mCustomer->record->all();

    foreach ($customers as $customer) {
      $activityCount = rand(0, 2);

      for ($i = 0; $i < $activityCount; $i++) {
        $date = date("Y-m-d", rand(strtotime("-1 month"), strtotime("+1 month")));
        $randomHour = str_pad((string) rand(6,18), 2, "0", STR_PAD_LEFT);
        $randomMinute = $minutes[rand(0,3)];
        $timeString = $date." ".$randomHour.":".$randomMinute.":00";
        $time = date("H:i:s", strtotime($timeString));

        $randomSubject = $activityTypes[rand(0, 3)];
        $activityType = null;

        switch ($randomSubject) {
          case $activityTypes[0]:
            $activityType = 1;
            break;
          case $activityTypes[1]:
            $activityType = 2;
            break;
          case $activityTypes[2]:
            $activityType = 3;
            break;
          case $activityTypes[3]:
            $activityType = 4;
            break;
        }

        $activityId = $mCustomerActivity->record->recordCreate([
          "id_activity_type" => $activityType,
          "subject" => $randomSubject,
          "date_start" => $date,
          "completed" => rand(0, 1),
          "id_owner" => rand(1, 4),
          "id_customer" => $customer->id,
          "id_contact" => null,
        ]);
      }
    }
  }

  public function generateLeads(
    \HubletoApp\Community\Customers\Models\Customer $mCustomer,
    \HubletoApp\Community\Leads\Models\Lead $mLead,
    \HubletoApp\Community\Leads\Models\LeadHistory $mLeadHistory,
    \HubletoApp\Community\Leads\Models\LeadTag $mLeadTag,
    \HubletoApp\Community\Leads\Models\LeadActivity $mLeadActivity,
  ): void {


    $customers = $mCustomer->record
      ->with("CONTACTS")
      ->get()
    ;

    $titles = ["TV", "Internet", "Fiber", "Landline", "Marketing", "Virtual Server"];
    $identifierPrefixes = ["US", "EU", "AS"];

    foreach ($customers as $customer) {
      if ($customer->CONTACTS->count() < 1) continue;

      $contact = $customer->CONTACTS->first();

      $leadDateCreatedTs = (int) rand(strtotime("-1 month"), strtotime("+1 month"));
      $leadDateCreated = date("Y-m-d H:i:s", $leadDateCreatedTs);
      $leadDateClose = date("Y-m-d H:i:s", $leadDateCreatedTs + rand(4, 6)*24*3600);

      $idLead = $mLead->record->recordCreate([
        "identifier" => $identifierPrefixes[rand(0,2)] . rand(1,3000),
        "title" => $titles[rand(0, count($titles)-1)],
        "id_customer" => $customer->id,
        "id_contact" => $contact->id,
        "price" => rand(10, 100) * rand(1, 5) * 1.12,
        "id_currency" => 1,
        "date_expected_close" => $leadDateClose,
        "id_owner" => rand(1, 4),
        "source_channel" => rand(1,7),
        "is_archived" => false,
        "status" => (rand(0, 10) == 5 ? $mLead::STATUS_LOST : $mLead::STATUS_NEW),
        "date_created" => $leadDateCreated,
        "score" => rand(1, 10),
      ])['id'];

      $mLeadHistory->record->recordCreate([
        "description" => "Lead created",
        "change_date" => date("Y-m-d", rand(strtotime("-1 month"), strtotime("+1 month"))),
        "id_lead" => $idLead
      ]);

      $mLeadActivity->record->recordCreate([
        "subject" => "Follow-up call",
        "date_start" => $leadDateCreated,
        "time_start" => rand(10, 15) . ':00',
        "all_day" => rand(1, 5) == 1,
        "id_lead" => $idLead,
        "id_contact" => 1,
        "id_activity_type" => 1,
        "id_owner" => rand(1, 4),
      ]);

      $tags = [];
      $tagsCount = (rand(1, 3) == 1 ? rand(1, 2) : 1);
      while (count($tags) < $tagsCount) {
        $idTag = rand(1, 4);
        if (!in_array($idTag, $tags)) $tags[] = $idTag;
      }

      foreach ($tags as $idTag) {
        $mLeadTag->record->recordCreate([
          "id_lead" => $idLead,
          "id_tag" => $idTag,
        ]);
      }
    }

  }

  public function generateDeals(
    \HubletoApp\Community\Leads\Models\Lead $mLead,
    \HubletoApp\Community\Leads\Models\LeadHistory $mLeadHistory,
    \HubletoApp\Community\Leads\Models\LeadTag $mLeadTag,
    \HubletoApp\Community\Deals\Models\Deal $mDeal,
    \HubletoApp\Community\Deals\Models\DealHistory $mDealHistory,
    \HubletoApp\Community\Deals\Models\DealTag $mDealTag,
    \HubletoApp\Community\Deals\Models\DealActivity $mDealActivity
  ): void {

    $leads = $mLead->record->get();

    foreach ($leads as $lead) { // @phpstan-ignore-line
      if (rand(1, 3) != 1) continue; // negenerujem deal pre vsetky leads

      $pipeline = 1;
      $result = (rand(0, 10) == 5 ? $mDeal::RESULT_LOST : $mDeal::RESULT_WON);
      if ($pipeline === 1) $pipelineStep = rand(1,3);
      else $pipelineStep = rand(4,7);

      $dealDateCreatedTs = rand(strtotime("-1 month"), strtotime("+1 month"));
      $dealDateCreated = date("Y-m-d H:i:s", $dealDateCreatedTs);
      $dealDateClose = date("Y-m-d H:i:s", strtotime("+1 month", $dealDateCreatedTs));

      $idDeal = $mDeal->record->recordCreate([
        "identifier" => $lead->identifier,
        "title" => $lead->title,
        "id_customer" => $lead->id_customer,
        "id_contact" => $lead->id_contact,
        "price" => $lead->price,
        "id_currency" => $lead->id_currency,
        "date_expected_close" => $dealDateClose,
        "id_owner" => $lead->id_owner,
        "source_channel" => $lead->source_channel,
        "is_archived" => $lead->is_archived,
        "id_pipeline" => $pipeline,
        "id_pipeline_step" => $pipelineStep,
        "id_lead" => $lead->id,
        "deal_result" => $result,
        "date_created" => $dealDateCreated,
        "date_result_update" => $result != 2 ? $dealDateClose : null,
      ])['id'];

      $mLeadHistory->record->recordCreate([
        "description" => "Converted to a deal",
        "change_date" => date("Y-m-d", rand(strtotime("-1 month"), strtotime("+1 month"))),
        "id_lead" => $lead->id
      ]);

      $leadHistories = $mLeadHistory->record
        ->where("id_lead", $lead->id)
        ->get()
      ;

      foreach ($leadHistories as $leadHistory) { // @phpstan-ignore-line
        $mDealHistory->record->recordCreate([
          "description" => $leadHistory->description,
          "change_date" => $leadHistory->change_date,
          "id_deal" => $idDeal
        ]);
      }

      $mDealActivity->record->recordCreate([
        "subject" => "Follow-up call",
        "date_start" => $dealDateCreated,
        "time_start" => rand(10, 15) . ':00',
        "all_day" => rand(1, 5) == 1,
        "id_deal" => $idDeal,
        "id_contact" => 1,
        "id_activity_type" => 1,
        "id_owner" => rand(1, 4),
      ]);

      $mDealTag->record->recordCreate([
        "id_deal" => $idDeal,
        "id_tag" => rand(1,5)
      ]);
    }
  }

  public function generateProducts(
    \HubletoApp\Community\Products\Models\Product $mProduct,
    \HubletoApp\Community\Products\Models\Group $mGroup,
    \HubletoApp\Community\Products\Models\Supplier $mSupplier,
  ): void {

    $mGroup->record->recordCreate([
      "title" => "Food"
    ]);
    $mGroup->record->recordCreate([
      "title" => "Furniture"
    ]);
    $mGroup->record->recordCreate([
      "title" => "Dry foods"
    ]);
    $mGroup->record->recordCreate([
      "title" => "Liquids"
    ]);
    $mGroup->record->create([
      "title" => "Service"
    ]);

    $mCountry = new Country($this->main);

    $mSupplier->record->recordCreate([
      "vat_id" => "GB123562563",
      "title" => "Fox Foods",
      "id_country" => $mCountry->record->inRandomOrder()->first()->id,
    ]);
    $mSupplier->record->recordCreate([
      "vat_id" => "CZ123562563",
      "title" => "Bořek Furniture",
      "id_country" => $mCountry->record->inRandomOrder()->first()->id,
    ]);
    $mSupplier->record->recordCreate([
      "vat_id" => "FR123562563",
      "title" => "Denise's Dry Goods",
      "id_country" => $mCountry->record->inRandomOrder()->first()->id,
    ]);


    $products = [
      ["Wine - Masi Valpolocell",94.27,62.93,23,"l"],
      ["Eggplant Italian",21.98,86.56,23,"ml"],
      ["Carrots - Mini, Stem On",76.44,56.95,23,"kg"],
      ["Lentils - Green Le Puy",42.94,98.78,23,"l"],
      ["Ice - Clear, 300 Lb For Carving",51.43,70.54,23,"ml"],
      ["Chicken - Leg / Back Attach",82.7,96.13,23,"l"],
      ["Thyme - Dried",96.11,76.39,23,"l"],
      ["Lettuce - Belgian Endive",35.14,89.06,23,"bottle"],
      ["Pasta - Rotini, Dry",4.42,88.99,23,"l"],
      ["Coffee Cup 8oz 5338cd",25.64,77.44,23,"mg"],
      ["Wine - Magnotta, White",7.21,89.8,23,"dc"],
      ["Sauerkraut",41.14,71.11,23,"bottle"],
      ["Yams",58.91,70.92,23,"l"],
      ["Salt - Celery",54.01,90.84,23,"bottle"],
      ["Bar Mix - Lemon",49.62,61.33,23,"kg"],
      ["Raspberries - Fresh",78.84,74.08,23,"l"],
      ["Lambcasing",71.23,58.71,23,"dc"],
      ["Sauce - Chili",14.92,92.16,23,"ml"],
      ["Chef Hat 20cm",62.76,71.59,23,"mg"],
      ["Wine - Sake",96.35,68.66,23,"bottle"],
      ["Chevril",20.34,88.6,23,"ml"],
      ["Milk - Buttermilk",26.1,74.32,23,"kg"],
      ["Cream - 35%",59.74,68.28,23,"bottle"],
      ["Liqueur - Melon",88.46,85.78,23,"l"],
      ["Beer - Muskoka Cream Ale",53.6,62.33,23,"l"],
      ["Beets - Candy Cane, Organic",29.0,95.1,23,"dc"],
      ["Oven Mitt - 13 Inch",57.49,89.41,23,"ml"],
    ];

    foreach ($products as $product) {
      $mProduct->record->create([
        "title" => $product[0],
        "unit_price" => $product[1],
        "margin" => $product[2],
        "vat" => $product[3],
        "unit" => $product[4],
        "id_product_group" => rand(1,4),
        "id_supplier" => rand(1,3),
        "type" => 1,
      ]);
    }

    $serviceNames = ["Cloud Storage", "Plugins", "Subscription", "Virtual Server", "Marketing", "Premium Package"];

    //Create all services
    foreach ($serviceNames as $serviceName) {
      $mProduct->record->create([
        "title" => $serviceName,
        "unit_price" => rand(10,100),
        "margin" => rand(10,40),
        "vat" => 25,
        "id_product_group" => 5,
        "id_supplier" => rand(1,3),
        "type" => 2,
      ]);
    }
  }
}

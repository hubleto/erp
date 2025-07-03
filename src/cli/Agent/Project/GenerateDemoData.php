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
      "first_name" => "Richard",
      "last_name" => "Manstall",
      "nick" => "chief",
      "email" => "chief@hubleto.com",
      "id_default_company" => $idCompany,
      "is_active" => true,
      "login" => "chief",
      "password" => password_hash("chief", PASSWORD_DEFAULT),
    ])['id'];

    $idUserManager = $mUser->record->recordCreate([
      "first_name" => "Jeeve",
      "last_name" => "Stobs",
      "nick" => "manager",
      "email" => "manager@hubleto.com",
      "id_default_company" => $idCompany,
      "is_active" => true,
      "login" => "manager",
      "password" => password_hash("manager", PASSWORD_DEFAULT),
    ])['id'];

    $idUserEmployee = $mUser->record->recordCreate([
      "first_name" => "Fedora",
      "last_name" => "Debian",
      "nick" => "employee",
      "email" => "employee@hubleto.com",
      "id_default_company" => $idCompany,
      "is_active" => true,
      "login" => "employee",
      "password" => password_hash("employee", PASSWORD_DEFAULT),
    ])['id'];

    $idUserAssistant = $mUser->record->recordCreate([
      "first_name" => "Hop",
      "last_name" => "Gracer",
      "nick" => "assistant",
      "email" => "assistant@hubleto.com",
      "id_default_company" => $idCompany,
      "is_active" => false,
      "login" => "assistant",
      "password" => password_hash("assistant", PASSWORD_DEFAULT),
    ])['id'];

    $idUserExternal = $mUser->record->recordCreate([
      "first_name" => "Chaplie",
      "last_name" => "Charlin",
      "nick" => "external",
      "email" => "external@hubleto.com",
      "id_default_company" => $idCompany,
      "is_active" => false,
      "login" => "external",
      "password" => password_hash("external", PASSWORD_DEFAULT),
      "language" => "en",
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
    $mSupplier = new \HubletoApp\Community\Suppliers\Models\Supplier($this->main);

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
      $this->generateProducts($mProduct, $mGroup, $mSupplier);
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
        "id_manager" => rand(1, 4),
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
        "id_customer" => rand(1, 13),
        "salutation" => $salutations[rand(0, 2)],
        "title_before" => $titlesBefore[rand(0, 2)],
        "first_name" => $contact[0],
        "last_name" => $contact[1],
        "title_after" => $titlesAfter[rand(0, 2)],
        "is_primary" => true,
        "is_valid" => true,
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

    $mCampaign = new \HubletoApp\Community\Campaigns\Models\Campaign($this->main);
    $mCampaign->record->recordCreate(["name" => "Newsletter subscribers", "target_audience" => "Website visitors filling 'Subscribe to our newsletter'.", "color" => "#AB149E" ]);
    $mCampaign->record->recordCreate(["name" => "Cold calling - SMEs", "target_audience" => "SMEs reached out by cold calling.", "color" => "#68CCCA" ]);

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
        "id_campaign" => rand(1, 2),
        "id_customer" => $customer->id,
        "id_contact" => $contact->id,
        "price" => rand(10, 100) * rand(1, 5) * 1.12,
        "id_currency" => 1,
        "date_expected_close" => $leadDateClose,
        "id_owner" => rand(1, 4),
        "source_channel" => rand(1,7),
        "is_archived" => false,
        "status" => (rand(0, 10) == 5 ? $mLead::STATUS_CLOSED : $mLead::STATUS_CONTACTED),
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

    $mPipeline = new \HubletoApp\Community\Pipeline\Models\Pipeline($this->main);
    $pipeline = $mPipeline->record->prepareReadQuery()->where('id', 1)->first()->toArray();

    foreach ($leads as $lead) { // @phpstan-ignore-line
      if (rand(1, 3) != 1) continue; // negenerujem deal pre vsetky leads

      $pStepsRandom = $pipeline['STEPS'];
      shuffle($pStepsRandom);
      $pStep = reset($pStepsRandom);

      $dealDateCreatedTs = rand(strtotime("-1 month"), strtotime("-1 day"));
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
        "id_pipeline" => $pipeline['id'],
        "id_pipeline_step" => $pStep['id'],
        "id_lead" => $lead->id,
        "deal_result" => $pStep['set_result'] ?? 0,
        "date_created" => $dealDateCreated,
        "date_result_update" => $pStep['set_result'] != \HubletoApp\Community\Deals\Models\Deal::RESULT_UNKNOWN ? $dealDateClose : null,
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
    \HubletoApp\Community\Suppliers\Models\Supplier $mSupplier,
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

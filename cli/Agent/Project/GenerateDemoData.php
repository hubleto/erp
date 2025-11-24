<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent\Project;

use Hubleto\App\Community\Settings\Models\Company;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Auth\Models\UserRole;
use Hubleto\App\Community\Auth\Models\UserHasRole;
use Hubleto\App\Community\Settings\PermissionsManager;

class GenerateDemoData extends \Hubleto\Erp\Cli\Agent\Command
{
  public $faker;
  public function run(): void
  {

    $this->faker = \Faker\Factory::create();;

    /** @var PermissionsManager */
    $permissionsManager = $this->getService(PermissionsManager::class);

    $permissionsManager->DANGEROUS__grantAllPermissions();

    $this->terminal()->cyan("Generating demo data...\n");

    $mCompany = $this->getService(Company::class);
    $mUser = $this->getService(User::class);
    $mUserRole = $this->getService(UserRole::class);
    $mUserHasRole = $this->getService(UserHasRole::class);

    $mCompany->record->where('id', 1)->update([
      'tax_id' => $this->faker->randomNumber(5, true).$this->faker->randomNumber(5, true),
      'vat_id' => $this->faker->randomNumber(5, true).$this->faker->randomNumber(5, true),
      'street_1' => $this->faker->streetAddress(),
      'street_2' => '',
      'zip' => $this->faker->postcode(),
      'city' => $this->faker->city(),
      'country' => $this->faker->country(),
    ]);

    $idCompany = 1; // plati za predpokladu, ze tento command sa spusta hned po CommandInit

    $mUser = $this->getService(User::class);

    $idUserChiefOfficer = $mUser->record->recordCreate([
      "type" => User::TYPE_CHIEF_OFFICER,
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
      "type" => User::TYPE_MANAGER,
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
      "type" => User::TYPE_EMPLOYEE,
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
      "type" => User::TYPE_ASSISTANT,
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
      "type" => User::TYPE_EXTERNAL,
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

    $mUserHasRole = $this->getModel(\Hubleto\App\Community\Auth\Models\UserHasRole::class);
    $mUserHasRole->record->recordCreate([
      "id_user" => $idUserChiefOfficer,
      "id_role" => \Hubleto\App\Community\Auth\Models\UserRole::ROLE_CHIEF_OFFICER,
    ]);

    $mUserHasRole = $this->getModel(\Hubleto\App\Community\Auth\Models\UserHasRole::class);
    $mUserHasRole->record->recordCreate([
      "id_user" => $idUserManager,
      "id_role" => \Hubleto\App\Community\Auth\Models\UserRole::ROLE_MANAGER,
    ]);

    $mUserHasRole = $this->getModel(\Hubleto\App\Community\Auth\Models\UserHasRole::class);
    $mUserHasRole->record->recordCreate([
      "id_user" => $idUserEmployee,
      "id_role" => \Hubleto\App\Community\Auth\Models\UserRole::ROLE_EMPLOYEE,
    ]);

    $mUserHasRole = $this->getModel(\Hubleto\App\Community\Auth\Models\UserHasRole::class);
    $mUserHasRole->record->recordCreate([
      "id_user" => $idUserAssistant,
      "id_role" => \Hubleto\App\Community\Auth\Models\UserRole::ROLE_ASSISTANT,
    ]);

    $mUserHasRole = $this->getModel(\Hubleto\App\Community\Auth\Models\UserHasRole::class);
    $mUserHasRole->record->recordCreate([
      "id_user" => $idUserExternal,
      "id_role" => \Hubleto\App\Community\Auth\Models\UserRole::ROLE_EXTERNAL,
    ]);

    $mProfile = $this->getModel(\Hubleto\App\Community\Invoices\Models\Profile::class);
    $mProfile->record->recordCreate(['name' => 'Test Profile 1']);

    //Documents
    $mDocuments = $this->getModel(\Hubleto\App\Community\Documents\Models\Document::class);

    //Customers & Contacts
    $mCustomer            = $this->getModel(\Hubleto\App\Community\Customers\Models\Customer::class);
    $mContact             = $this->getModel(\Hubleto\App\Community\Contacts\Models\Contact::class);
    $mContactTag          = $this->getModel(\Hubleto\App\Community\Contacts\Models\ContactTag::class);
    $mValue               = $this->getModel(\Hubleto\App\Community\Contacts\Models\Value::class);
    $mCustomerActivity    = $this->getModel(\Hubleto\App\Community\Customers\Models\CustomerActivity::class);
    $mCustomerDocument    = $this->getModel(\Hubleto\App\Community\Customers\Models\CustomerDocument::class);
    $mCustomerTag         = $this->getModel(\Hubleto\App\Community\Customers\Models\CustomerTag::class);

    //Leads
    $mLead = $this->getModel(\Hubleto\App\Community\Leads\Models\Lead::class);
    $mLeadHistory  = $this->getModel(\Hubleto\App\Community\Leads\Models\LeadHistory::class);
    $mLeadTag = $this->getModel(\Hubleto\App\Community\Leads\Models\LeadTag::class);
    $mLeadActivity = $this->getModel(\Hubleto\App\Community\Leads\Models\LeadActivity::class);
    $mLeadDocument = $this->getModel(\Hubleto\App\Community\Leads\Models\LeadDocument::class);

    //Deals
    $mDeal         = $this->getModel(\Hubleto\App\Community\Deals\Models\Deal::class);
    $mDealHistory  = $this->getModel(\Hubleto\App\Community\Deals\Models\DealHistory::class);
    $mDealTag      = $this->getModel(\Hubleto\App\Community\Deals\Models\DealTag::class);
    $mDealActivity = $this->getModel(\Hubleto\App\Community\Deals\Models\DealActivity::class);

    if (
      $this->appManager()->isAppInstalled("Hubleto\App\Community\Documents") &&
      $this->appManager()->isAppInstalled("Hubleto\App\Community\Customers") &&
      $this->appManager()->isAppInstalled("Hubleto\App\Community\Contacts")
    ) {
      $this->generateCustomers($mCustomer, $mCustomerTag);
      $this->generateContacts($mContact, $mContactTag, $mValue);
    }

    $this->generateActivities($mCustomer, $mCustomerActivity);

    if (
      $this->appManager()->isAppInstalled("Hubleto\App\Community\Customers") &&
      $this->appManager()->isAppInstalled("Hubleto\App\Community\Documents") &&
      $this->appManager()->isAppInstalled("Hubleto\App\Community\Deals") &&
      $this->appManager()->isAppInstalled("Hubleto\App\Community\Leads")
    ) {
      $this->generateLeads($mCustomer, $mLead, $mLeadHistory, $mLeadTag, $mLeadActivity);
      $this->generateDeals($mLead, $mLeadHistory, $mLeadTag, $mDeal, $mDealHistory, $mDealTag, $mDealActivity);
    }

    foreach ($this->appManager()->getInstalledAppNamespaces() as $appNamespace => $appConfig) {
      $app = $this->appManager()->getApp($appNamespace);
      if ($app) {
        $this->terminal()->cyan("  {$appNamespace}\n");
        $app->generateDemoData();
      }
    }

    $this->terminal()->cyan("Demo data generated. Administrator email (login) is now 'demo@hubleto.com' and password is 'demo'.\n");

    $permissionsManager->revokeGrantAllPermissions();
  }

  public function generateInvoiceProfiles(): void
  {
    $mProfile = $this->getModel(\Hubleto\App\Community\Invoices\Models\Profile::class);
    $mProfile->install();
    $mProfile->record->recordCreate([
      "name" => "Test Invoice Profile"
    ]);
  }

  public function generateCustomers(
    \Hubleto\App\Community\Customers\Models\Customer $mCustomer,
    \Hubleto\App\Community\Customers\Models\CustomerTag $mCustomerTag,
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
        if (!in_array($idTag, $tags)) {
          $tags[] = $idTag;
        }
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
    \Hubleto\App\Community\Contacts\Models\Contact $mContact,
    \Hubleto\App\Community\Contacts\Models\ContactTag $mContactTag,
    \Hubleto\App\Community\Contacts\Models\Value $mValue,
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
        if (!in_array($idTag, $tags)) {
          $tags[] = $idTag;
        }
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
    \Hubleto\App\Community\Customers\Models\Customer $mCustomer,
    \Hubleto\App\Community\Customers\Models\CustomerActivity $mCustomerActivity,
  ): void {

    $activityTypes = ["Meeting", "Bussiness Trip", "Call", "Email"];
    $minutes = ["00", "15", "30", "45"];
    $customers = $mCustomer->record->all();

    foreach ($customers as $customer) {
      $activityCount = rand(0, 2);

      for ($i = 0; $i < $activityCount; $i++) {
        $date = date("Y-m-d", rand(strtotime("-1 month"), strtotime("+1 month")));
        $randomHour = str_pad((string) rand(6, 18), 2, "0", STR_PAD_LEFT);
        $randomMinute = $minutes[rand(0, 3)];
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
    \Hubleto\App\Community\Customers\Models\Customer $mCustomer,
    \Hubleto\App\Community\Leads\Models\Lead $mLead,
    \Hubleto\App\Community\Leads\Models\LeadHistory $mLeadHistory,
    \Hubleto\App\Community\Leads\Models\LeadTag $mLeadTag,
    \Hubleto\App\Community\Leads\Models\LeadActivity $mLeadActivity,
  ): void {

    $mCampaign = $this->getModel(\Hubleto\App\Community\Campaigns\Models\Campaign::class);
    $mCampaign->record->recordCreate(["name" => "Newsletter subscribers", "target_audience" => "Website visitors filling 'Subscribe to our newsletter'.", "color" => "#AB149E" ]);
    $mCampaign->record->recordCreate(["name" => "Cold calling - SMEs", "target_audience" => "SMEs reached out by cold calling.", "color" => "#68CCCA" ]);

    $customers = $mCustomer->record
      ->with("CONTACTS")
      ->get()
    ;

    $titles = ["TV", "Internet", "Fiber", "Landline", "Marketing", "Virtual Server"];
    $identifierPrefixes = ["US", "EU", "AS"];

    foreach ($customers as $customer) {
      if ($customer->CONTACTS->count() < 1) {
        continue;
      }

      $contact = $customer->CONTACTS->first();

      $leadDateCreatedTs = (int) rand(strtotime("-1 month"), strtotime("+1 month"));
      $leadDateCreated = date("Y-m-d H:i:s", $leadDateCreatedTs);
      $leadDateClose = date("Y-m-d H:i:s", $leadDateCreatedTs + rand(4, 6) * 24 * 3600);

      $idLead = $mLead->record->recordCreate([
        "identifier" => $identifierPrefixes[rand(0, 2)] . rand(1, 3000),
        "title" => $titles[rand(0, count($titles) - 1)],
        "id_campaign" => rand(1, 2),
        "id_customer" => $customer->id,
        "id_contact" => $contact->id,
        "price" => rand(10, 100) * rand(1, 5) * 1.12,
        "id_currency" => 1,
        "date_expected_close" => $leadDateClose,
        "id_owner" => rand(1, 4),
        "source_channel" => rand(1, 7),
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
        if (!in_array($idTag, $tags)) {
          $tags[] = $idTag;
        }
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
    \Hubleto\App\Community\Leads\Models\Lead $mLead,
    \Hubleto\App\Community\Leads\Models\LeadHistory $mLeadHistory,
    \Hubleto\App\Community\Leads\Models\LeadTag $mLeadTag,
    \Hubleto\App\Community\Deals\Models\Deal $mDeal,
    \Hubleto\App\Community\Deals\Models\DealHistory $mDealHistory,
    \Hubleto\App\Community\Deals\Models\DealTag $mDealTag,
    \Hubleto\App\Community\Deals\Models\DealActivity $mDealActivity,
  ): void {

    $leads = $mLead->record->get();

    $mWorkflow = $this->getModel(\Hubleto\App\Community\Workflow\Models\Workflow::class);
    $workflow = $mWorkflow->record->prepareReadQuery()->where('id', 1)->first()->toArray();

    foreach ($leads as $lead) { // @phpstan-ignore-line
      if (rand(1, 3) != 1) {
        continue;
      } // negenerujem deal pre vsetky leads

      $pStepsRandom = $workflow['STEPS'];
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
        "id_workflow" => $workflow['id'],
        "id_workflow_step" => $pStep['id'],
        "id_lead" => $lead->id,
        "deal_result" => $pStep['set_result'] ?? 0,
        "date_created" => $dealDateCreated,
        "date_result_update" => $pStep['set_result'] != \Hubleto\App\Community\Deals\Models\Deal::RESULT_UNKNOWN ? $dealDateClose : null,
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
        "id_tag" => rand(1, 5)
      ]);
    }
  }
}

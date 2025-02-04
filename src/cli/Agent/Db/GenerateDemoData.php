<?php

namespace HubletoMain\Cli\Agent\Db;

use HubletoApp\Community\Settings\Models\Country;
use HubletoApp\Community\Settings\Models\Permission;
use HubletoApp\Community\Settings\Models\Profile;
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

    $mProfile = new \HubletoApp\Community\Settings\Models\Profile($this->main);
    $mUser = new \HubletoApp\Community\Settings\Models\User($this->main);
    $mUserRole = new \HubletoApp\Community\Settings\Models\UserRole($this->main);
    $mUserHasRole = new \HubletoApp\Community\Settings\Models\UserHasRole($this->main);

    $idProfile = 1; // plati za predpokladu, ze tento command sa spusta hned po CommandInit

    $mUser = new \HubletoApp\Community\Settings\Models\User($this->main);
    $idUserSalesManager = $mUser->recordCreate([
      "first_name" => "Sales",
      "last_name" => "Manager",
      "email" => "test@user.sk",
      "id_active_profile" => $idProfile,
      "is_active" => true,
      "login" => "sales.manager",
      "password" => password_hash("sales.manager", PASSWORD_DEFAULT),
    ]);

    $mUserHasRole = new \HubletoApp\Community\Settings\Models\UserHasRole($this->main);
    $mUserHasRole->recordCreate([
      "id_user" => $idUserSalesManager,
      "id_role" => \HubletoApp\Community\Settings\Models\UserRole::ROLE_SALES_MANAGER,
    ]);

    $mInvoiceProfile = new \HubletoApp\Community\Settings\Models\InvoiceProfile($this->main);
    $mInvoiceProfile->recordCreate(['name' => 'Test Profile 1']);

    //Documents
    $mDocuments = new \HubletoApp\Community\Documents\Models\Document($this->main);

    //Services
    $mService = new \HubletoApp\Community\Services\Models\Service($this->main);

    //Customers
    $mPerson             = new \HubletoApp\Community\Customers\Models\Person($this->main);
    $mCompany            = new \HubletoApp\Community\Customers\Models\Company($this->main);
    $mAddress            = new \HubletoApp\Community\Customers\Models\Address($this->main);
    $mContact            = new \HubletoApp\Community\Customers\Models\Contact($this->main);
    $mCompanyActivity    = new \HubletoApp\Community\Customers\Models\CompanyActivity($this->main);
    $mCompanyDocument    = new \HubletoApp\Community\Customers\Models\CompanyDocument($this->main);
    $mCompanyTag         = new \HubletoApp\Community\Customers\Models\CompanyTag($this->main);
    $mPersonTag          = new \HubletoApp\Community\Customers\Models\PersonTag($this->main);

    //Invoices
    $mInvoice = new \HubletoApp\Community\Invoices\Models\Invoice($this->main);
    $mInvoiceItem = new \HubletoApp\Community\Invoices\Models\InvoiceItem($this->main);

    //Leads
    $mLead = new \HubletoApp\Community\Leads\Models\Lead($this->main);
    $mLeadHistory  = new \HubletoApp\Community\Leads\Models\LeadHistory($this->main);
    $mLeadTag = new \HubletoApp\Community\Leads\Models\LeadTag($this->main);
    $mLeadServices = new \HubletoApp\Community\Leads\Models\LeadService($this->main);
    $mLeadActivity = new \HubletoApp\Community\Leads\Models\LeadActivity($this->main);
    $mLeadDocument = new \HubletoApp\Community\Leads\Models\LeadDocument($this->main);

    //Deals
    $mDeal         = new \HubletoApp\Community\Deals\Models\Deal($this->main);
    $mDealHistory  = new \HubletoApp\Community\Deals\Models\DealHistory($this->main);
    $mDealTag      = new \HubletoApp\Community\Deals\Models\DealTag($this->main);
    $mDealServices = new \HubletoApp\Community\Deals\Models\DealService($this->main);
    $mDealActivity = new \HubletoApp\Community\Deals\Models\DealActivity($this->main);
    $mDealDocument = new \HubletoApp\Community\Deals\Models\DealDocument($this->main);

    //Shop
    $mProduct = new \HubletoApp\Community\Products\Models\Product($this->main);
    $mGroup = new \HubletoApp\Community\Products\Models\Group($this->main);
    $mSupplier = new \HubletoApp\Community\Products\Models\Supplier($this->main);

    $this->generateCompanies($mCompany, $mCompanyTag);
    $this->generatePersons($mPerson, $mPersonTag, $mContact, $mAddress);
    //$this->generateActivities($mCompany, $mActivity, $mCompanyActivity);
    $this->generateServices($mCompany, $mService);
    $this->generateLeads($mCompany, $mLead, $mLeadHistory, $mLeadTag, $mLeadActivity);
    $this->generateDeals($mLead, $mLeadHistory, $mLeadTag, $mDeal, $mDealHistory, $mDealTag, $mDealActivity);
    $this->generateProducts($mProduct,$mGroup, $mSupplier);

    $this->cli->cyan("Demo data generated. Administrator email (login) is now 'demo@hubleto.com' and password is 'demo'.\n");

    $this->main->permissions->revokeGrantAllPermissions();
  }

  public function generateInvoiceProfiles(): void
  {
    $mInvoiceProfile = new \HubletoApp\Community\Settings\Models\InvoiceProfile($this->main);
    $mInvoiceProfile->install();
    $mInvoiceProfile = $mInvoiceProfile->recordCreate([
      "name" => "Test Invoice Profile"
    ]);
  }

  public function generateTags(): void
  {

    $mTag = new \HubletoApp\Community\Settings\Models\Tag($this->main);

    $mTag->recordCreate([
      'name' => "Category 1",
    ]);
    $mTag->recordCreate([
      'name' => "Category 2",
    ]);
    $mTag->recordCreate([
      'name' => "Category 3",
    ]);
  }

  public function generateCompanies(
    \HubletoApp\Community\Customers\Models\Company $mCompany,
    \HubletoApp\Community\Customers\Models\CompanyTag $mCompanyTag,
  ): void {

    $companiesCsv = trim('
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

    $companies = explode("\n", $companiesCsv);

    foreach ($companies as $companyCsvData) {
      $company = explode(",", trim($companyCsvData));

      $idCompany = $mCompany->recordCreate([
        "id_country" => $company[0],
        "company_id" => $company[1],
        "name" => $company[2],
        "postal_code" => $company[3],
        "street_line_1" => $company[4],
        "street_line_2" => $company[5],
        "city" => $company[6],
        "region" => $company[7],
        "tax_id" => $company[8],
        "vat_id" => $company[9],
        "note" => $company[10],
        "is_active" => 1,
        "id_user" => 1,
        "date_created" => date("Y-m-d", rand(1722456000, strtotime("now"))),
      ]);

      $tagCount = rand(0, 3);
      for ($i = 0; $i < $tagCount; $i++) {
        $mCompanyTag->recordCreate([
          "id_company" => $idCompany,
          "id_tag" => rand(1, 3)
        ]);
      }
    }
  }

  public function generatePersons(
    \HubletoApp\Community\Customers\Models\Person $mPerson,
    \HubletoApp\Community\Customers\Models\PersonTag $mPersonTag,
    \HubletoApp\Community\Customers\Models\Contact $mContact,
    \HubletoApp\Community\Customers\Models\Address $mAddress,
  ): void {

    $persons = [
      [ 1, 100, "Ján", "Novák", "", true, true, "ján.novák@gmail.com" ],
      [ 2, 99, "Mária", "Kováčová", "", false, true, "mária.kováčová@gmail.com" ],
      [ 3, 98, "Peter", "Horváth", "", true, false, "peter.horváth@gmail.com" ],
      [ 4, 97, "Katarína", "Varga", "", false, true, "katarína.varga@gmail.com" ],
      [ 5, 96, "Michal", "Tóth", "", true, true, "michal.tóth@gmail.com" ],
      [ 6, 95, "Tomáš", "László", "", true, false, "tomáš.lászló@gmail.com" ],
      [ 7, 94, "Robert", "Marek", "", false, true, "robert.marek@gmail.com" ],
      [ 8, 93, "Lucia", "Králová", "", true, true, "lucia.králová@gmail.com" ],
      [ 9, 92, "Pavel", "Filo", "", true, false, "pavel.filo@gmail.com" ],
      [ 10, 91, "Sofia", "Urbanová", "", false, true, "sofia.urbanová@gmail.com" ],
      [ 11, 90, "Martin", "Kováč", "", true, true, "martin.kováč@gmail.com" ],
      [ 12, 89, "Ivana", "Szabóová", "", false, true, "ivana.szabóová@gmail.com" ],
      [ 13, 88, "Jakub", "Molnár", "", true, false, "jakub.molnár@gmail.com" ],
      [ 14, 87, "Natália", "Holubová", "", false, true, "natália.holubová@gmail.com" ],
      [ 15, 86, "Samuel", "Bíro", "", true, true, "samuel.bíro@gmail.com" ],
      [ 16, 85, "Veronika", "Poláková", "", false, false, "veronika.poláková@gmail.com" ],
      [ 17, 84, "Lukáš", "Papp", "", true, true, "lukáš.papp@gmail.com" ],
      [ 18, 83, "Andrej", "Nagy", "", false, true, "andrej.nagy@gmail.com" ],
      [ 19, 82, "Petra", "Ráczová", "", true, false, "petra.ráczová@gmail.com" ],
      [ 20, 81, "Alžbeta", "Kocsisová", "", false, true, "alžbeta.kocsisová@gmail.com" ],
      [ 21, 80, "Nina", "Szarková", "", true, true, "nina.szarková@gmail.com" ],
      [ 22, 79, "Patrik", "Baláž", "", false, false, "patrik.baláž@gmail.com" ],
      [ 23, 78, "Iveta", "Molnárová", "", true, true, "iveta.molnárová@gmail.com" ],
      [ 24, 77, "Roman", "Tóth", "", false, true, "roman.tóth@gmail.com" ],
      [ 25, 76, "Filip", "Vojtko", "", true, false, "filip.vojtko@gmail.com" ],
      [ 26, 75, "Dávid", "Csicsai", "", false, true, "dávid.csicsai@gmail.com" ],
      [ 27, 74, "Viktor", "Kováč", "", true, true, "viktor.kováč@gmail.com" ],
      [ 28, 73, "Silvia", "Palkovičová", "", false, false, "silvia.palkovičová@gmail.com" ],
      [ 29, 72, "Jaroslav", "Kočiš", "", true, true, "jaroslav.kočiš@gmail.com" ],
      [ 30, 71, "Tereza", "Farkašová", "", false, true, "tereza.farkašová@gmail.com" ],
      [ 31, 70, "Adrián", "Ondruš", "", true, false, "adrián.ondruš@gmail.com" ],
      [ 32, 69, "Zuzana", "Kurucová", "", false, true, "zuzana.kurucová@gmail.com" ],
      [ 33, 68, "Richard", "Bartók", "", true, true, "richard.bartók@gmail.com" ],
      [ 34, 67, "Stanislav", "Nemec", "", false, false, "stanislav.nemec@gmail.com" ],
      [ 35, 66, "Lenka", "Varga", "", true, true, "lenka.varga@gmail.com" ],
      [ 36, 65, "Erika", "Cibulková", "", false, true, "erika.cibulková@gmail.com" ],
      [ 37, 64, "Vladimír", "Takáč", "", true, false, "vladimír.takáč@gmail.com" ],
      [ 38, 63, "Nikola", "Král", "", false, true, "nikola.král@gmail.com" ],
      [ 39, 62, "Dušan", "Papp", "", true, true, "dušan.papp@gmail.com" ],
      [ 40, 61, "Radka", "Horváthová", "", false, false, "radka.horváthová@gmail.com" ],
      [ 41, 60, "Peter", "Balog", "", true, true, "peter.balog@gmail.com" ],
      [ 42, 59, "Michal", "Urban", "", false, true, "michal.urban@gmail.com" ],
      [ 43, 58, "Marian", "Filo", "", true, false, "marian.filo@gmail.com" ],
      [ 44, 57, "Ivana", "Nagyová", "", false, true, "ivana.nagyová@gmail.com" ],
      [ 45, 56, "Štefan", "Kovács", "", true, true, "štefan.kovács@gmail.com" ],
      [ 46, 55, "Lucia", "Bírová", "", false, false, "lucia.bírová@gmail.com" ],
      [ 47, 54, "Jozef", "Tóth", "", true, true, "jozef.tóth@gmail.com" ],
      [ 48, 53, "Kristína", "Ráczová", "", false, true, "kristína.ráczová@gmail.com" ],
      [ 49, 52, "Ondrej", "Vojtko", "", true, false, "ondrej.vojtko@gmail.com" ],
      [ 50, 51, "Dominika", "Molnárová", "", false, true, "dominika.molnárová@gmail.com" ],
      [ 51, 50, "Erik", "Kováč", "", true, true, "erik.kováč@gmail.com" ],
      [ 52, 49, "Martina", "Szabóová", "", false, false, "martina.szabóová@gmail.com" ],
      [ 53, 48, "Monika", "Králová", "", true, true, "monika.králová@gmail.com" ],
      [ 54, 47, "Pavol", "Nagy", "", false, true, "pavol.nagy@gmail.com" ],
      [ 55, 46, "Adriana", "Mareková", "", true, false, "adriana.mareková@gmail.com" ],
      [ 56, 45, "Filip", "László", "", false, true, "filip.lászló@gmail.com" ],
      [ 57, 44, "Peter", "Csicsai", "", true, true, "peter.csicsai@gmail.com" ],
      [ 58, 43, "Andrea", "Urbanová", "", false, false, "andrea.urbanová@gmail.com" ],
      [ 59, 42, "Dominik", "Bartók", "", true, true, "dominik.bartók@gmail.com" ],
      [ 60, 41, "Katarína", "Balážová", "", false, true, "katarína.balážová@gmail.com" ],
      [ 61, 40, "Veronika", "Horváthová", "", true, false, "veronika.horváthová@gmail.com" ],
      [ 62, 39, "Marek", "Varga", "", false, true, "marek.varga@gmail.com" ],
      [ 63, 38, "Michal", "Szarka", "", true, true, "michal.szarka@gmail.com" ],
      [ 64, 37, "Anna", "Nagyová", "", false, false, "anna.nagyová@gmail.com" ],
      [ 65, 36, "Tomáš", "Molnár", "", true, true, "tomáš.molnár@gmail.com" ],
      [ 66, 35, "Peter", "Tóth", "", false, true, "peter.tóth@gmail.com" ],
      [ 67, 34, "Ján", "Nemec", "", true, false, "ján.nemec@gmail.com" ],
      [ 68, 33, "Iveta", "Kovácsová", "", false, true, "iveta.kovácsová@gmail.com" ],
      [ 69, 32, "Zuzana", "Balážová", "", true, true, "zuzana.balážová@gmail.com" ],
      [ 70, 31, "Martin", "Varga", "", false, false, "martin.varga@gmail.com" ],
      [ 71, 30, "Zuzana", "Urbanová", "", true, true, "zuzana.urbanová@gmail.com" ],
      [ 72, 29, "Silvia", "Szabóová", "", false, true, "silvia.szabóová@gmail.com" ],
      [ 73, 28, "Martin", "Takáč", "", true, false, "martin.takáč@gmail.com" ],
      [ 74, 27, "Mária", "Bartók", "", false, true, "mária.bartók@gmail.com" ],
      [ 75, 26, "Daniel", "Kováč", "", true, true, "daniel.kováč@gmail.com" ],
      [ 76, 25, "Jakub", "Szarka", "", false, false, "jakub.szarka@gmail.com" ],
      [ 77, 24, "Jozef", "Marek", "", true, true, "jozef.marek@gmail.com" ],
      [ 78, 23, "Pavol", "Filo", "", false, true, "pavol.filo@gmail.com" ],
      [ 79, 22, "Michal", "László", "", true, false, "michal.lászló@gmail.com" ],
      [ 80, 21, "Roman", "Nemec", "", false, true, "roman.nemec@gmail.com" ],
      [ 81, 20, "Katarína", "Králová", "", true, true, "katarína.králová@gmail.com" ],
      [ 82, 19, "Lucia", "Tóthová", "", false, false, "lucia.tóthová@gmail.com" ],
      [ 83, 18, "Ján", "Horváth", "", true, true, "ján.horváth@gmail.com" ],
      [ 84, 17, "Anna", "Kovácsová", "", false, true, "anna.kovácsová@gmail.com" ],
      [ 85, 16, "Filip", "Molnár", "", true, false, "filip.molnár@gmail.com" ],
      [ 86, 15, "Michal", "Varga", "", false, true, "michal.varga@gmail.com" ],
      [ 87, 14, "Peter", "Kováč", "", true, true, "peter.kováč@gmail.com" ],
      [ 88, 13, "Martin", "Bartók", "", false, false, "martin.bartók@gmail.com" ],
      [ 89, 12, "Marek", "Nagy", "", true, true, "marek.nagy@gmail.com" ],
      [ 90, 11, "Andrej", "Rácz", "", false, true, "andrej.rácz@gmail.com" ],
      [ 91, 10, "Tereza", "Horváthová", "", true, false, "tereza.horváthová@gmail.com" ],
      [ 92, 9, "Richard", "Bíro", "", false, true, "richard.bíro@gmail.com" ],
      [ 93, 8, "Vladimír", "Papp", "", true, true, "vladimír.papp@gmail.com" ],
      [ 94, 7, "Erik", "Vojtko", "", false, false, "erik.vojtko@gmail.com" ],
      [ 95, 6, "Pavol", "Král", "", true, true, "pavol.král@gmail.com" ],
      [ 96, 5, "Michal", "Nagy", "", false, true, "michal.nagy@gmail.com" ],
      [ 97, 4, "Ivana", "Szarka", "", true, false, "ivana.szarka@gmail.com" ],
      [ 98, 3, "Adrián", "Takáč", "", false, true, "adrián.takáč@gmail.com" ],
      [ 99, 2, "Jozef", "Nagy", "", true, true, "jozef.nagy@gmail.com" ],
      [ 100, 1, "Kristína", "Mareková", "", false, false, "kristína.mareková@gmail.com" ],
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

    foreach ($persons as $person) {
      $idPerson = $mPerson->recordCreate([
        "id_company" => rand(1, 100),
        "first_name" => $person[2],
        "last_name" => $person[3],
        "note" => $person[4],
        "is_main" => rand(0, 1),
        "is_active" => 1,
        "date_created" => date("Y-m-d", rand(strtotime("-1 month"), strtotime("+1 month"))),
      ]);

      $mContact->recordCreate([
        "id_person" => $idPerson,
        "type" => "email",
        "value" => str_replace("'", "", (string) iconv('UTF-8', 'ASCII//TRANSLIT', $person[7])),
        "id_contact_type" => rand(1,2),
      ]);

      $phoneNumber = "+421 9" . rand(0, 3) . rand(4, 8) . " " . rand(0, 9) . rand(0, 9) . rand(0, 9) . " " . rand(0, 9) . rand(0, 9) . rand(0, 9);
      $mContact->recordCreate([
        "id_person" => $idPerson,
        "type" => "number",
        "value" => $phoneNumber,
        "id_contact_type" => rand(1,2),
      ]);

      $tagCount = rand(0, 3);
      for ($i = 0; $i < $tagCount; $i++) {
        $mPersonTag->recordCreate([
          "id_person" => $idPerson,
          "id_tag" => rand(1, 3)
        ]);
      }

      $mAddress->recordCreate([
        "id_person" => $idPerson,
        "street_line_1" => $streets[rand(0, 9)],
        "street_line_2" => "",
        "postal_code" => $postalCodes[rand(0, 9)],
        "city" => $cities[rand(0, 9)],
        "region" => $regions[rand(0, 9)],
        "id_country" => rand(1, 100)
      ]);
    }
  }

  // public function generateActivities(
  //   \HubletoApp\Community\Customers\Models\Company $mCompany,
  //   \HubletoApp\Community\Customers\Models\Activity $mActivity,
  //   \HubletoApp\Community\Customers\Models\CompanyActivity $mCompanyActivity,
  // ): void {

  //   $activityTypes = ["Meeting", "Bussiness Trip", "Call", "Email"];
  //   $minutes = ["00", "15", "30", "45"];
  //   $companies = $mCompany->eloquent->all();

  //   foreach ($companies as $company) {
  //     $activityCount = rand(1, 5);

  //     for ($i = 0; $i < $activityCount; $i++) {
  //       $date = date("Y-m-d", rand(strtotime("-1 month"), strtotime("+1 month")));
  //       $randomHour = str_pad((string) rand(6,18), 2, "0", STR_PAD_LEFT);
  //       $randomMinute = $minutes[rand(0,3)];
  //       $timeString = $date." ".$randomHour.":".$randomMinute.":00";
  //       $time = date("H:i:s", strtotime($timeString));

  //       $randomSubject = $activityTypes[rand(0, 3)];
  //       $activityType = null;

  //       switch ($randomSubject) {
  //         case $activityTypes[0]:
  //           $activityType = 1;
  //           break;
  //         case $activityTypes[1]:
  //           $activityType = 2;
  //           break;
  //         case $activityTypes[2]:
  //           $activityType = 3;
  //           break;
  //         case $activityTypes[3]:
  //           $activityType = 4;
  //           break;
  //       }

  //       $activityId = $mActivity->recordCreate([
  //         "id_activity_type" => $activityType,
  //         "subject" => $randomSubject,
  //         "date_start" => $date,
  //         "completed" => rand(0, 1),
  //         "id_user" => 1,
  //         "id_company" => $company->id,
  //         "id_person" => null,
  //       ]);

  //       $mCompanyActivity->recordCreate([
  //         "id_company" => $company->id,
  //         "id_activity" => $activityId
  //       ]);
  //     }
  //   }
  // }

  public function generateServices(
    \HubletoApp\Community\Customers\Models\Company $mCompany,
    \HubletoApp\Community\Services\Models\Service $mService
  ): void {

    $companies = $mCompany->eloquent->all();
    $serviceNames = ["Clound Storage", "Plugins", "Subscription", "Virtual Server", "Marketing", "Premium Package"];

    //Create all services
    foreach ($serviceNames as $serviceName) {
      $mService->recordCreate([
        "name" => $serviceName,
        "price" => rand(10,100),
        "id_currency" => 1,
      ]);
    }

    //Generate Billing Accounts and connect Services to them
    /* foreach ($companies as $company) {
      $NOServices = rand(1, count($serviceNames));
      $idBillingAccount = $mBillingAccount->recordCreate([
        "id_company" => $company->id,
        "description" => "Fakturácia",
      ]);

      for ($i = 0; $i < $NOServices; $i++) {
        $mBillingAccountService->recordCreate([
          "id_billing_account" => $idBillingAccount,
          "id_service" => rand(1, count($serviceNames) - 1)
        ]);
      }
    }
    foreach ($companies as $company) {
      $NOServices = rand(1, count($serviceNames));
      $idBillingAccount = $mBillingAccount->recordCreate([
        "id_company" => $company->id,
        "description" => "Objednávky",
      ]);

      for ($i = 0; $i < $NOServices; $i++) {
        $mBillingAccountService->recordCreate([
          "id_billing_account" => $idBillingAccount,
          "id_service" => rand(1, count($serviceNames) - 1)
        ]);
      }
    } */
  }

  public function generateLeads(
    \HubletoApp\Community\Customers\Models\Company $mCompany,
    \HubletoApp\Community\Leads\Models\Lead $mLead,
    \HubletoApp\Community\Leads\Models\LeadHistory $mLeadHistory,
    \HubletoApp\Community\Leads\Models\LeadTag $mLeadTag,
    \HubletoApp\Community\Leads\Models\LeadActivity $mLeadActivity,
  ): void {


    $companies = $mCompany->eloquent
      ->with("PERSONS")
      ->get()
    ;

    $titles = ["TV", "Internet", "Fiber", "Landline", "Marketing", "Vitual Server"];
    $sources = ["Advertisement", "Phone Call", "Email", "Newsletter"];

    foreach ($companies as $company) {
      if ($company->PERSONS->count() < 1) continue;

      $person = $company->PERSONS->first();

      $leadDateCreatedTs = (int) rand(strtotime("-1 month"), strtotime("+1 month"));
      $leadDateCreated = date("Y-m-d", $leadDateCreatedTs);
      $leadDateClose = date("Y-m-d", $leadDateCreatedTs + rand(4, 6)*24*3600);

      $idLead = $mLead->recordCreate([
        "title" => $titles[rand(0, count($titles)-1)],
        "id_company" => $company->id,
        "id_person" => $person->id,
        "price" => rand(10,100),
        "id_currency" => rand(1,3),
        "date_expected_close" => $leadDateClose,
        "id_user" => 1,
        "source_channel" => $sources[rand(0, count($sources)-1)],
        "is_archived" => false,
        "id_lead_status" => rand(1,4),
        "date_created" => $leadDateCreated,
      ]);

      $mLeadHistory->recordCreate([
        "description" => "Lead created",
        "change_date" => date("Y-m-d", rand(strtotime("-1 month"), strtotime("+1 month"))),
        "id_lead" => $idLead
      ]);

      $mLeadActivity->recordCreate([
        "subject" => "Follow-up call",
        "date_start" => $leadDateCreated,
        "id_lead" => $idLead,
        "id_person" => 1,
        "id_activity_type" => 1,
        "id_user" => 1,
      ]);

      $mLeadTag->recordCreate([
        "id_lead" => $idLead,
        "id_tag" => rand(1,3)
      ]);
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

    $leads = $mLead->eloquent->get();

    foreach ($leads as $lead) { // @phpstan-ignore-line
      $pipeline = rand(1,2);
      if ($pipeline === 1) $pipelineStep = rand(1,3);
      else $pipelineStep = rand(4,7);

      $dealDateCreatedTs = (int) rand(strtotime("-1 month"), strtotime("+1 month"));
      $dealDateCreated = date("Y-m-d", $dealDateCreatedTs);
      $dealDateClose = date("Y-m-d", $dealDateCreatedTs + rand(4, 6)*24*3600);

      $idDeal = $mDeal->recordCreate([
        "title" => $lead->title,
        "id_company" => $lead->id_company,
        "id_person" => $lead->id_person,
        "price" => $lead->price,
        "id_currency" => $lead->id_currency,
        "date_expected_close" => $dealDateClose,
        "id_user" => $lead->id_user,
        "source_channel" => $lead->source_channel,
        "is_archived" => $lead->is_archived,
        "id_pipeline" => $pipeline,
        "id_pipeline_step" => $pipelineStep,
        "id_lead" => $lead->id,
        "id_deal_status" => rand(1,4),
        "date_created" => $dealDateCreated,
      ]);

      $mLeadHistory->recordCreate([
        "description" => "Converted to a deal",
        "change_date" => date("Y-m-d", rand(strtotime("-1 month"), strtotime("+1 month"))),
        "id_lead" => $lead->id
      ]);

      $leadHistories = $mLeadHistory->eloquent
        ->where("id_lead", $lead->id)
        ->get()
      ;

      foreach ($leadHistories as $leadHistory) { // @phpstan-ignore-line
        $mDealHistory->recordCreate([
          "description" => $leadHistory->description,
          "change_date" => $leadHistory->change_date,
          "id_deal" => $idDeal
        ]);
      }

      $leadTag = $mLeadTag->eloquent
        ->where("id_lead", $lead->id)
        ->first()
      ;

      $mDealActivity->recordCreate([
        "subject" => "Follow-up call",
        "date_start" => $dealDateCreated,
        "id_deal" => $idDeal,
        "id_person" => 1,
        "id_activity_type" => 1,
        "id_user" => 1,
      ]);

      $mDealTag->recordCreate([
        "id_deal" => $idDeal,
        "id_tag" => $leadTag->id_tag
      ]);
    }
  }

  public function generateProducts(
    \HubletoApp\Community\Products\Models\Product $mProduct,
    \HubletoApp\Community\Products\Models\Group $mGroup,
    \HubletoApp\Community\Products\Models\Supplier $mSupplier,
  ): void {

    $mGroup->recordCreate([
      "title" => "Food"
    ]);
    $mGroup->recordCreate([
      "title" => "Furniture"
    ]);
    $mGroup->recordCreate([
      "title" => "Dry foods"
    ]);
    $mGroup->recordCreate([
      "title" => "Liquids"
    ]);

    $mCountry = new Country($this->main);

    $mSupplier->recordCreate([
      "vat_id" => "GB123562563",
      "title" => "Fox Foods",
      "id_country" => $mCountry->eloquent->inRandomOrder()->first()->id,
    ]);
    $mSupplier->recordCreate([
      "vat_id" => "CZ123562563",
      "title" => "Bořek Furniture",
      "id_country" => $mCountry->eloquent->inRandomOrder()->first()->id,
    ]);
    $mSupplier->recordCreate([
      "vat_id" => "FR123562563",
      "title" => "Denise's Dry Goods",
      "id_country" => $mCountry->eloquent->inRandomOrder()->first()->id,
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
      $idProduct = $mProduct->recordCreate([
        "title" => $product[0],
        "unit_price" => $product[1],
        "margin" => $product[2],
        "tax" => $product[3],
        "unit" => $product[4],
        "id_product_group" => rand(1,4),
        "id_supplier" => rand(1,3),
      ]);
    }
  }
}

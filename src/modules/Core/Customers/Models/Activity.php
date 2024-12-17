<?php

namespace CeremonyCrmMod\Core\Customers\Models;

use CeremonyCrmMod\Core\Settings\Models\ActivityType;
use CeremonyCrmMod\Core\Settings\Models\User;
use CeremonyCrmMod\Sales\Deals\Models\Deal;
use CeremonyCrmMod\Sales\Leads\Models\LeadActivity;
use CeremonyCrmMod\Sales\Deals\Models\DealActivity;
use CeremonyCrmMod\Sales\Deals\Models\DealHistory;
use CeremonyCrmMod\Sales\Leads\Models\Lead;
use CeremonyCrmMod\Sales\Leads\Models\LeadHistory;

class Activity extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'activities';
  public string $eloquentClass = Eloquent\Activity::class;
  public ?string $lookupSqlValue = '{%TABLE%}.subject';

  public array $relations = [
    'COMPANY_ACTIVITY' => [ self::HAS_ONE, CompanyActivity::class, 'id_activity', 'id' ],
    'LEAD_ACTIVITY' => [ self::HAS_ONE, LeadActivity::class, 'id_activity', 'id' ],
    'DEAL_ACTIVITY' => [ self::HAS_ONE, DealActivity::class, 'id_activity', 'id' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id' ],
    'ACTIVITY_TYPE' => [ self::HAS_ONE, ActivityType::class, 'id', 'id_activity_type'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_activity_type' => [
        'type' => 'lookup',
        'title' => $this->translate('Activity type'),
        'model' => \CeremonyCrmMod\Core\Settings\Models\ActivityType::class,
        'foreignKeyOnUpdate' => 'SET NULL',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => true,
      ],
      'subject' => [
        'type' => 'varchar',
        'title' => $this->translate('Subject'),
        'required' => true,
      ],
      'date_start' => [
        'type' => 'date',
        'title' => 'Start Date',
        'required' => true,
      ],
      'time_start' => [
        'type' => 'time',
        'title' => 'Start Time',
        'required' => false,
      ],
      'date_end' => [
        'type' => 'date',
        'title' => 'End Date',
        'required' => false,
      ],
      'time_end' => [
        'type' => 'time',
        'title' => 'End Time',
        'required' => false,
      ],
      'all_day' => [
        'type' => 'boolean',
        'title' => 'All day',
        'required' => false,
      ],
      'completed' => [
        'type' => 'boolean',
        'title' => 'Completed',
        'required' => false,
      ],
      'id_user' => [
        'type' => 'lookup',
        'title' => 'Created by',
        'model' => \CeremonyCrmMod\Core\Settings\Models\User::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => false,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Activities';
    $description['ui']['addButtonText'] = 'Add Activity';
    $description['ui']['showHeader'] = true;
    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description['defaultValues']['id_user'] = $this->app->auth->user["id"];
    $description['includeRelations'] = [
      "COMPANY_ACTIVITY",
      "LEAD_ACTIVITY",
      "DEAL_ACTIVITY",
      "USER",
      "ACTIVITY_TYPE"
    ];
    $description['ui']['addButtonText'] = $this->translate('Add activity');
    return $description;
  }

  public function onAfterCreate(array $record, $returnValue)
  {
    $mActivityCompany = new CompanyActivity($this->app);
    $mLead = new Lead($this->app);
    $mDeal = new Deal($this->app);
    $mLeadActivity = new LeadActivity($this->app);
    $mDealActivity = new DealActivity($this->app);
    $mLeadHistory = new LeadHistory($this->app);
    $mDealHistory = new DealHistory($this->app);

    if (isset($record["creatingForModel"])) {
      if ($record["creatingForModel"] == "Company") {

        $mActivityCompany->eloquent->create([
          "id_activity" => $record["id"],
          "id_company" => $record["creatingForId"]
        ]);

      } else if ($record["creatingForModel"] == "Lead") {

        $lead = $mLead->eloquent->find($record["creatingForId"]);
        $mActivityCompany->eloquent->create([
          "id_activity" => $record["id"],
          "id_company" => $lead->id_company
        ]);
        $mLeadActivity->eloquent->create([
          "id_activity" => $record["id"],
          "id_lead" => $record["creatingForId"]
        ]);

        $mLeadHistory->eloquent->create([
          "change_date" => date("Y-m-d"),
          "id_lead" => $record["creatingForId"],
          "description" => "Activity ". $record["subject"] ." created"
        ]);

      } else if ($record["creatingForModel"] == "Deal") {

        $deal = $mDeal->eloquent->find($record["creatingForId"]);

        $mActivityCompany->eloquent->create([
          "id_activity" => $record["id"],
          "id_company" => $deal->id_company
        ]);
        $mDealActivity->eloquent->create([
          "id_activity" => $record["id"],
          "id_deal" => $record["creatingForId"]
        ]);

        $mDealHistory->eloquent->create([
          "change_date" => date("Y-m-d"),
          "id_deal" => $record["creatingForId"],
          "description" => "Activity ". $record["subject"] ." created"
        ]);
      }
    }
    return $record;
  }

}

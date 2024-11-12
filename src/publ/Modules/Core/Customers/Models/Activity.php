<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

use CeremonyCrmApp\Modules\Core\Settings\Models\ActivityType;
use CeremonyCrmApp\Modules\Core\Settings\Models\User;
use CeremonyCrmApp\Modules\Sales\Sales\Models\LeadActivity;
use CeremonyCrmApp\Modules\Sales\Sales\Models\DealActivity;

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
        'title' => 'Type',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/ActivityType',
        'foreignKeyOnUpdate' => 'SET NULL',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => true,
      ],
      'subject' => [
        'type' => 'varchar',
        'title' => 'Activity subject',
        'required' => true,
      ],
      'date_start' => [
        'type' => 'date',
        'title' => 'Start Date',
        'required' => true,
      ],
      'time_start' => [
        'type' => 'time',
        'title' => 'Time Start',
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
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/User',
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
      $description['defaultValues']['id_user'] = $this->app->user;
      $description['includeRelations'] = ["COMPANY_ACTIVITY", "LEAD_ACTIVITY", "DEAL_ACTIVITY", "USER", "ACTIVITY_TYPE"];
      return $description;
  }

  public function onAfterCreate(array $record, $returnValue)
  {
    if (isset($record["creatingForModel"])) {
      if ($record["creatingForModel"] == "Company") {
        $mActvityCompany = new CompanyActivity($this->app);
        $mActvityCompany->eloquent->create([
          "id_activity" => $record["id"],
          "id_company" => $record["creatingForId"]
        ]);
      } else if ($record["creatingForModel"] == "Lead") {
        $mLeadActivity = new LeadActivity($this->app);
        $mLeadActivity->eloquent->create([
          "id_activity" => $record["id"],
          "id_lead" => $record["creatingForId"]
        ]);
      } else if ($record["creatingForModel"] == "Deal") {
        $mDealActivity = new DealActivity($this->app);
        $mDealActivity->eloquent->create([
          "id_activity" => $record["id"],
          "id_deal" => $record["creatingForId"]
        ]);
      }
    }
    return $record;
  }

}

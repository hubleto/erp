<?php

namespace HubletoMain\Core\Models;

use \HubletoApp\Community\Settings\Models\UserRole;

class Model extends \ADIOS\Core\Model {
  public \HubletoMain $main;

  public bool $isExtendableByCustomColumns = false;

  public array $conversionRelations = [];
  public string $permission = '';
  public array $rolePermissions = []; // example: [ [UserRole::ROLE_CHIEF_OFFICER => [true, true, true, true]] ]

  function __construct(\HubletoMain $main)
  {
    $this->main = $main;

    $reflection = new \ReflectionClass($this);
    preg_match('/^(.*?)\\\Models\\\(.*?)$/', $reflection->getName(), $m);
    $this->translationContext = $m[1] . '\\Loader::Models\\' . $m[2];

    parent::__construct($main);

  }

  public function describeColumns(): array
  {
    $customColumns = [];
    if ($this->isExtendableByCustomColumns) {
      $customColumnsCfg = $this->getConfigAsArray('customColumns');
      foreach ($customColumnsCfg as $colName => $colCfg) {
        $customColumn = null;
        $colClass = $colCfg['class'] ?? '';
        if (class_exists($colClass)) {
          $customColumns[$colName] = (new $colClass($this, ''))->loadFromArray($colCfg)->setProperty('isCustom', true);
        }
      }
    }
    return array_merge(parent::describeColumns(), $customColumns);
  }

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $description = parent::describeForm();

    // model-based permissions sa uz nepouzivaju
    // pouzivaju sa record-based permissions, vid recordManager->getPermissions()
    $description->permissions = [
      'canRead' => true,
      'canCreate' => true,
      'canUpdate' => true,
      'canDelete' => true,
    ];

    return $description;
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $tag = $this->main->urlParamAsString('tag');

    // model-based permissions sa uz nepouzivaju
    // pouzivaju sa record-based permissions, vid recordManager->getPermissions()
    $description->permissions = [
      'canRead' => true,
      'canCreate' => true,
      'canUpdate' => true,
      'canDelete' => true,
    ];

    $description->ui['moreActions'] = [
      [ 'title' => 'Export to CSV', 'type' => 'stateChange', 'state' => 'showExportCsvScreen', 'value' => true ],
    ];

    if (!empty($tag)) {
      $description->ui['moreActions'][] = [ 'title' => 'Columns', 'type' => 'stateChange', 'state' => 'showColumnConfigScreen', 'value' => true ];
    }

    // getConfig - zistit, ake stlpce sa maju zobrazit / skryt
    // + vypocitat $description->columns (v principe asi unset() pre stlpce, ktore sa maju skryt)

    if (!empty($tag)) {
      $allColumnsConfig = @json_decode($this->getConfigAsString('tableColumns'), true);

      if (isset($allColumnsConfig[$tag])) {
        foreach ($allColumnsConfig[$tag] as $colName => $colConfig) {
          if (isset($description->columns[$colName])) {
            if (!($colConfig['visible'] ?? false)) {
              unset($description->columns[$colName]);
            }
          }
        }
      } else {
        foreach ($description->columns as $colName => $column) {
          if (!$column->getProperty('defaultVisibility')) unset($description->columns[$colName]);
        }
      }
    }

    return $description;
  }

  public function diffRecords(array $record1, array $record2): array
  {
    $diff = [];
    foreach ($this->getColumns() as $colName => $column) {
      $v1 = $record1[$colName] ?? null;
      $v2 = $record2[$colName] ?? null;
      if ($v1 != $v2) $diff[$colName] = [ $v1, $v2 ];
    }

    return $diff;

  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    $user = $this->main->auth->getUser();
    if (isset($savedRecord['id_owner']) && $savedRecord['id_owner'] != $user['id']) {
      $notificationsApp = $this->main->apps->community('Notifications');
      $diff = $this->diffRecords($originalRecord, $savedRecord);

      if ($notificationsApp && count($diff) > 0) {

        $body =
          'User ' . $user['email'] . ' updated ' . $this->shortName . ":\n"
          . json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        ;

        $notificationsApp->send(
          \HubletoMain::NOTIFICATION_CATEGORY_RECORD_UPDATED, // category
          [$this->shortName, $this->fullName],
          (int) $savedRecord['id_owner'], // to
          $this->shortName . ' updated', // subject
          $body
        );
      }
    }

    return $savedRecord;
  }

}
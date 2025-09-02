<?php

namespace Hubleto\App\Community\Developer\Controllers;

class DbUpdates extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'developer', 'content' => $this->translate('Developer') ],
      [ 'url' => 'db-updater', 'content' => $this->translate('DB Updater') ],
    ]);
  }

  public function getNecessaryUpdates(): array
  {
    $necessaryUpdates = [];
    $apps = $this->appManager()->getEnabledApps();

    $tmpTables = $this->db()->fetchAll("show tables");
    $tables = [];
    foreach ($tmpTables as $tmp) {
      $tmpTable = reset($tmp);
      $tmpColumns = $this->db()->fetchAll("show columns from `{$tmpTable}`");
      foreach ($tmpColumns as $tmpColumn) {
        $tables[$tmpTable][$tmpColumn['Field']] = $tmpColumn;
      }
    }

    $updateCounter = 1;

    foreach ($apps as $app) {
      $mClasses = $app->getAvailableModelClasses();
      foreach ($mClasses as $mClass) {
        $mObj = $this->getService($mClass);

        // table is missing
        if (!isset($tables[$mObj->table])) {
          $necessaryUpdates['mt-' . ($updateCounter++)] = [
            'type' => 'missing-table',
            'description' => 'Table ' . $mObj->table . ' from ' . $mObj->fullName . ' is missing.',
            'sql' => $mObj->getSqlCreateTableCommands(),
            'bgClass' => 'bg-yellow-50',
          ];
        } else {
          foreach ($mObj->getColumns() as $colName => $column) {
            // column is missing
            $colDefinition = $column->toArray();
            if (!isset($tables[$mObj->table][$colName]) && $colDefinition['type'] !== 'virtual') {
              $sql = [
                'alter table `' . $mObj->table . '` add ' . $column->sqlCreateString($mObj->table, $colName)
              ];
              $indexString = $column->sqlIndexString($mObj->table, $colName);
              if (!empty($indexString)) {
                $sql[] = 'alter table `' . $mObj->table . '` add ' . $indexString;
              }
              $necessaryUpdates['mc-' . ($updateCounter++)] = [
                'type' => 'missing-column',
                'description' => 'Column ' . $mObj->table . '.' . $colName . ' from ' . $mObj->fullName . ' is missing.',
                'sql' => $sql,
                'bgClass' => 'bg-blue-50',
              ];

              // column has changed
            } else {
              // todo
            }
          }
        }
      }
    }

    return $necessaryUpdates;
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $necessaryUpdates = $this->getNecessaryUpdates();
    $runLog = [];

    if ($this->router()->isUrlParam('updatesToRun')) {
      $updatesToRun = $this->router()->urlParamAsArray('updatesToRun');
      $sqls = [];
      foreach ($updatesToRun as $updateUid) {
        if (isset($necessaryUpdates[$updateUid])) {
          $sqls = array_merge($sqls, $necessaryUpdates[$updateUid]['sql']);
        } else {
          $runLog[] = 'Update ' . $updateUid . ' not found.';
        }
      }

      try {
        $runLog[] = 'Starting transaction.';
        $this->db()->startTransaction();

        foreach ($sqls as $sql) {
          $runLog[] = $sql;
          $this->db()->execute($sql);
        }

        $runLog[] = 'Commiting.';
        $this->db()->commit();
      } catch (\Throwable $e) {
        $runLog[] = 'ERROR when running above SQL. Rolling back.';
        $runLog[] = $e->getMessage();
        $this->db()->rollback();
      }

      $necessaryUpdates = $this->getNecessaryUpdates();
    }

    $this->viewParams['necessaryUpdates'] = $necessaryUpdates;
    $this->viewParams['runLog'] = $runLog;

    $this->setView('@Hubleto:App:Community:Developer/DbUpdates.twig');
  }

}

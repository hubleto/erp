<?php

namespace HubletoMain\Hook\Default;

class NotifyUpdatedRecord extends \HubletoMain\Core\Hook
{

  public function run(string $event, array $args): void
  {
    if ($event == 'model:record-updated') {
      $notificationsApp = $this->main->apps->community('Notifications');
      if ($notificationsApp) {
        list($model, $originalRecord, $savedRecord) = $args;

        $user = $this->main->auth->getUser();
        if (isset($savedRecord['id_owner']) && $savedRecord['id_owner'] != $user['id']) {
          $diff = $model->diffRecords($originalRecord, $savedRecord);

          if (count($diff) > 0) {

            $body =
              'User ' . $user['email'] . ' updated ' . $model->shortName . ":\n"
              . json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            ;

            $notificationsApp->send(
              945, // category
              [$model->shortName, $model->fullName],
              (int) $savedRecord['id_owner'], // to
              $model->shortName . ' updated', // subject
              $body
            );
          }
        }
      }
    }
  }

}
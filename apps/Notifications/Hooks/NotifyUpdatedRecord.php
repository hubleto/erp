<?php declare(strict_types=1);

namespace Hubleto\App\Community\Notifications\Hooks;

class NotifyUpdatedRecord extends \Hubleto\Erp\Hook
{

  public function run(string $event, array $args): void
  {
    if ($event == 'model:record-updated') {
      /** @var \Hubleto\App\Community\Notifications\Loader $notificationsApp */
      $notificationsApp = $this->getAppManager()->getApp(\Hubleto\App\Community\Notifications\Loader::class);

      list($model, $originalRecord, $savedRecord) = $args;

      $user = $this->getAuthProvider()->getUser();
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
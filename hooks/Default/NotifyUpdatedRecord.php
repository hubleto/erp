<?php declare(strict_types=1);

namespace HubletoMain\Hook\Default;

class NotifyUpdatedRecord extends \HubletoMain\Hook
{

  public function run(string $event, array $args): void
  {
    if ($event == 'model:record-updated') {
      /** @var \HubletoApp\Community\Notifications\Loader $notificationsApp */
      $notificationsApp = $this->getAppManager()->getApp(\HubletoApp\Community\Notifications\Loader::class);

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
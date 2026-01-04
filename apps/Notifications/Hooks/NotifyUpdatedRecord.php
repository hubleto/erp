<?php declare(strict_types=1);

namespace Hubleto\App\Community\Notifications\Hooks;

use Hubleto\App\Community\Notifications\Sender;

class NotifyUpdatedRecord extends \Hubleto\Erp\Hook
{

  public function run(string $event, array $args): void
  {
    if ($event == 'model:on-after-update') {

      $user = $this->authProvider()->getUser();

      /** @var Sender */
      $sender = $this->getService(Sender::class);

      $model = $args['model'] ?? '';
      $originalRecord = (array) $args['originalRecord'] ?? [];
      $savedRecord = (array) $args['savedRecord'] ?? [];
      $idOwner = (int) ($savedRecord['id_owner'] ?? 0);
      $idManager = (int) ($savedRecord['id_manager'] ?? 0);

      $diff = $model->diffRecords($originalRecord, $savedRecord);

      if (count($diff) > 0) {

        $body =
          'User ' . $user['email'] . ' updated ' . $model->shortName . ":\n"
          . json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        ;

        if ($idOwner > 0) {
          $sender->send(
            945, // category
            [$model->shortName, $model->fullName],
            $idOwner, // to
            $model->shortName . ' updated', // subject
            $body,
            $this->env()->projectUrl . '/' . $model->getItemDetailUrl((int) $savedRecord['id']) // url
          );
        }

        if ($idManager > 0) {
          $sender->send(
            945, // category
            [$model->shortName, $model->fullName],
            $idManager, // to
            $model->shortName . ' updated', // subject
            $body,
            $this->env()->projectUrl . '/' . $model->getItemDetailUrl((int) $savedRecord['id']) // url
          );
        }
      }
    }
  }

}
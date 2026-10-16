<?php declare(strict_types=1);

namespace Hubleto\App\Community\Notifications\EventListeners;

use Hubleto\App\Community\Notifications\Sender;
use Hubleto\Framework\Interfaces\ModelInterface;

class NotifyUpdatedRecord extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{

  public function onModelAfterUpdate(ModelInterface $model, array $originalRecord, array $savedRecord): void
  {
    $user = $this->authProvider()->getUser();

    /** @var Sender */
    $sender = $this->getService(Sender::class);

    $idOwner = (int) ($savedRecord['id_owner'] ?? 0);
    $idManager = (int) ($savedRecord['id_manager'] ?? 0);
    $recordId = (int) ($savedRecord['id'] ?? 0);

    $diff = $model->diffRecords($originalRecord, $savedRecord);

    if (count($diff) > 0) {

      $body =
        $this->translate('User {{ email }} updated {{ model }}:', ['email' => $user['email'], 'model' => $model->shortName]) . "\n"
        . json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
      ;

      if ($idOwner > 0) {
        $sender->send(
          945, // category
          [$model->shortName, $model->fullName],
          $model->fullName,
          $recordId,
          $idOwner, // to
          $this->translate('{{ model }} updated', ['model' => $model->shortName]), // subject
          $body,
          $this->env()->projectUrl . '/' . $model->getRecordDetailUrl($savedRecord) // url
        );
      }

      if ($idManager > 0) {
        $sender->send(
          945, // category
          [$model->shortName, $model->fullName],
          $model->fullName,
          $recordId,
          $idManager, // to
          $this->translate('{{ model }} updated', ['model' => $model->shortName]), // subject
          $body,
          $this->env()->projectUrl . '/' . $model->getRecordDetailUrl($savedRecord) // url
        );
      }
    }
  }

}
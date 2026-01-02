<?php

namespace Hubleto\App\Community\Notifications\Models\RecordManagers;


use Hubleto\App\Community\Auth\Models\RecordManagers\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends \Hubleto\Erp\RecordManager
{
  public $table = 'notifications';

  /** @return BelongsTo<User, covariant Customer> */
  public function FROM(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_from', 'id');
  }

  /** @return BelongsTo<User, covariant Customer> */
  public function TO(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_to', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $folder = $hubleto->router()->urlParamAsString('folder');
    $idUser = $hubleto->getService(\Hubleto\Framework\AuthProvider::class)->getUserId();

    switch ($folder) {
      case 'inbox': $query->where('id_to', $idUser);
        break;
      case 'sent': $query->where('id_from', $idUser);
        break;
    };

    return $query;
  }

  // public function recordCreate(array $record, $useProvidedRecordId = false): array
  // {
  //   $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

  //   /** @var \Hubleto\App\Community\Notifications\Loader $notificationsApp */
  //   $notificationsApp = $hubleto->appManager(\Hubleto\App\Community\Notifications\Loader::class);

  //   $message = $notificationsApp->send(
  //     $record['id_to'] ?? '',
  //     $record['subject'] ?? '',
  //     $record['body'] ?? '',
  //     $record['color'] ?? '',
  //     (int) ($record['priority'] ?? 0),
  //   );

  //   $record['id'] = $message['id'] ?? 0;

  //   return $record;
  // }
}

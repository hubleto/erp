<?php

namespace HubletoApp\Community\Notifications\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Notification extends \Hubleto\Framework\RecordManager
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

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $main = \HubletoMain\Loader::getGlobalApp();

    $query = parent::prepareReadQuery($query, $level);

    $folder = $main->urlParamAsString('folder');
    $idUser = $main->auth->getUserId();

    switch ($folder) {
      case 'inbox': $query->where('id_to', $idUser);
        break;
      case 'sent': $query->where('id_from', $idUser);
        break;
    };

    return $query;
  }

  public function recordCreate(array $record): array
  {
    $main = \HubletoMain\Loader::getGlobalApp();
    $notificationsApp = $main->apps->community('Notifications');

    $message = $notificationsApp->send(
      $record['id_to'] ?? '',
      $record['subject'] ?? '',
      $record['body'] ?? '',
      $record['color'] ?? '',
      (int) ($record['priority'] ?? 0),
    );

    $record['id'] = $message['id'] ?? 0;

    return $record;
  }
}

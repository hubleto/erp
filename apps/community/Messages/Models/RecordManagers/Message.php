<?php

namespace HubletoApp\Community\Messages\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Message extends \HubletoMain\Core\RecordManager
{
  public $table = 'messages';

  // /** @return BelongsTo<User, covariant Customer> */
  // public function OWNER(): BelongsTo {
  //   return $this->belongsTo(User::class, 'id_owner', 'id');
  // }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $main = \ADIOS\Core\Helper::getGlobalApp();
    $folder = $main->urlParamAsString('folder');
    $idUser = $main->auth->getUserId();

    $query = parent::prepareReadQuery($query, $level)
      ->leftJoin('messages_index as midx', 'midx.id_message', '=', 'messages.id')
    ;

    $user = $main->auth->getUser();

    switch ($folder) {
      case 'inbox':
        $query->where(function($q) use ($idUser) {
          $q->where('midx.id_to', $idUser);
          $q->orWhere('midx.id_cc', $idUser);
          $q->orWhere('midx.id_bcc', $idUser);
        });
      break;
      case 'sent':
        $query->where('midx.id_from', $idUser);
      break;
    };


    return $query;
  }

  public function recordCreate(array $record): array
  {
    $main = \ADIOS\Core\Helper::getGlobalApp();
    $messagesApp = $main->apps->community('Messages');
    
    $message = $messagesApp->send(
      $record['to'] ?? '',
      $record['cc'] ?? '',
      $record['bcc'] ?? '',
      $record['subject'] ?? '',
      $record['body'] ?? '',
      $record['color'] ?? '',
      (int) ($record['priority'] ?? 0),
    );

    $record['id'] = $message['id'] ?? 0;

    return $record;
  }
}

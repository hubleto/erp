<?php

namespace HubletoApp\Community\Mail\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Mail extends \Hubleto\Framework\RecordManager
{
  public $table = 'mails';

  // /** @return BelongsTo<User, covariant Customer> */
  // public function OWNER(): BelongsTo {
  //   return $this->belongsTo(User::class, 'id_owner', 'id');
  // }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $main = \HubletoMain\Loader::getGlobalApp();
    $folder = $main->urlParamAsString('folder');
    $idUser = $main->auth->getUserId();

    $query = parent::prepareReadQuery($query, $level)
      ->leftJoin('mails_index as midx', 'midx.id_mail', '=', 'mails.id')
    ;

    $user = $main->auth->getUser();

    switch ($folder) {
      case 'inbox':
        $query->where(function ($q) use ($idUser) {
          $q->where('midx.id_to', $idUser);
          $q->orWhere('midx.id_cc', $idUser);
          $q->orWhere('midx.id_bcc', $idUser);
        });
        break;
      case 'outbox':
        $query->where('is_draft', false)->whereNull('datetime_sent');
        break;
      case 'drafts':
        $query->where('is_draft', true);
        break;
      case 'templates':
        $query->where('is_template', true);
        break;
      case 'sent':
        $query->where('midx.id_from', $idUser);
        break;
    };


    return $query;
  }

}

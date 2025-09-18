<?php

namespace Hubleto\App\Community\OAuth\Models;

use Hubleto\Framework\Db\Column\Varchar;

class RefreshToken extends \Hubleto\Erp\Model
{
  public string $table = 'oauth_access_tokens';
  public string $recordManagerClass = RecordManagers\RefreshToken::class;
  public ?string $lookupSqlValue = 'concat("RefreshToken #", {%TABLE%}.id)';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'access_token' => (new Varchar($this, $this->translate('Access Token')))->setDefaultVisible(),
      'access_token' => (new Varchar($this, $this->translate('Refresh Token')))->setDefaultVisible(),
      'expires_at' => (new Varchar($this, $this->translate('Expires At')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add RefreshToken';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

}

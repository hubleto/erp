<?php

namespace HubletoApp\Community\OAuth\Models;

use Hubleto\Framework\Db\Column\Varchar;

class RefreshToken extends \Hubleto\Framework\Models\Model
{
  public string $table = 'oauth_access_tokens';
  public string $recordManagerClass = RecordManagers\RefreshToken::class;
  public ?string $lookupSqlValue = 'concat("RefreshToken #", {%TABLE%}.id)';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'access_token' => (new Varchar($this, $this->translate('Access Token')))->setProperty('defaultVisibility', true),
      'access_token' => (new Varchar($this, $this->translate('Refresh Token')))->setProperty('defaultVisibility', true),
      'expires_at' => (new Varchar($this, $this->translate('Expires At')))->setProperty('defaultVisibility', true),
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

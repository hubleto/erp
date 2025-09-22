<?php

namespace Hubleto\App\Community\OAuth\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Boolean;

class AccessToken extends \Hubleto\Erp\Model
{
  public string $table = 'oauth_access_tokens';
  public string $recordManagerClass = RecordManagers\AccessToken::class;
  public ?string $lookupSqlValue = 'concat("AccessToken #", {%TABLE%}.id)';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'access_token' => (new Varchar($this, $this->translate('Access Token')))->setDefaultVisible(),
      'expires_at' => (new Varchar($this, $this->translate('User Id')))->setDefaultVisible(),
      'client_id' => (new Varchar($this, $this->translate('Client Id')))->setDefaultVisible(),
      'scopes' => (new Varchar($this, $this->translate('Scopes')))->setDefaultVisible(),
      'revoked' => (new Boolean($this, $this->translate('Revoked')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add AccessToken';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

}

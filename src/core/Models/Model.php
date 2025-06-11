<?php

namespace HubletoMain\Core\Models;

use \HubletoApp\Community\Settings\Models\UserRole;

class Model extends \ADIOS\Core\Model {
  public \HubletoMain $main;

  public array $conversionRelations = [];
  public string $permission = '';
  public array $rolePermissions = []; // example: [ [UserRole::ROLE_CHIEF_OFFICER => [true, true, true, true]] ]

  function __construct(\HubletoMain $main)
  {
    $this->main = $main;

    $reflection = new \ReflectionClass($this);
    preg_match('/^(.*?)\\\Models\\\(.*?)$/', $reflection->getName(), $m);
    $this->translationContext = $m[1] . '\\Loader::Models\\' . $m[2];

    parent::__construct($main);

  }

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $description = parent::describeForm();

    // model-based permissions sa uz nepouzivaju
    // pouzivaju sa record-based permissions, vid recordManager->getPermissions()
    $description->permissions = [
      'canRead' => true,
      'canCreate' => true,
      'canUpdate' => true,
      'canDelete' => true,
    ];

    return $description;
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    // model-based permissions sa uz nepouzivaju
    // pouzivaju sa record-based permissions, vid recordManager->getPermissions()
    $description->permissions = [
      'canRead' => true,
      'canCreate' => true,
      'canUpdate' => true,
      'canDelete' => true,
    ];

    return $description;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    $messagesApp = $this->main->apps->community('Messages');

    $diff = \ADIOS\Core\Helper::arrayDiffRecursive($originalRecord, $savedRecord);

    if ($messagesApp && count($diff) > 0) {

      $user = $this->main->auth->getUser();

      if (isset($savedRecord['id_owner'])) {
        $ownerEmail = $savedRecord['id_owner'];
        $body =
          'User ' . $user['email'] . ' updated ' . $this->shortName . "\n"
        ;

        $messagesApp->send(
          $ownerEmail, // to
          '', // cc
          '', // bcc
          $this->shortName . ' updated', // subject
          $body
        );
      }
    }

    return $savedRecord;
  }

}
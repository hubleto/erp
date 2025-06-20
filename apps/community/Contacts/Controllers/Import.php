<?php

namespace HubletoApp\Community\Contacts\Controllers;

class Import extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'contacts', 'content' => $this->translate('Contacts') ],
      [ 'url' => 'import', 'content' => $this->translate('Import') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $mContact = new \HubletoApp\Community\Contacts\Models\Contact($this->main);
    $mValue = new \HubletoApp\Community\Contacts\Models\Value($this->main);

    $contactsFile = $this->main->uploadedFile('contactsFile');
    if (is_array($contactsFile) && is_file($contactsFile['tmp_name'])) {
      if (($handle = fopen($contactsFile['tmp_name'], "r")) !== FALSE) {
        $rowIdx = 0;
        while (($row = fgetcsv($handle, 0, ";")) !== FALSE) {
          if ($rowIdx++ == 0) continue;

          $firstName = '';
          $middleName = '';
          $lastName = '';
          $values = [];
          for ($i = 0; $i < count($row); $i++) {
            $v = trim($row[$i]);
            if (empty($v)) continue;
            if ($i == 0) $firstName = $v;
            elseif ($i == 1) $middleName = $v;
            elseif ($i == 2) $lastName = $v;
            else $values[] = $v;
          }

          $contact = $mContact->record
            ->with('VALUES')
            ->whereHas('VALUES', function($q) use ($values) {
              $q->where(function($qq) use ($values) {
                foreach ($values as $value) $qq->orWhere('value', $value);
              });
            })
            ->first()
            ?->toArray()
          ;

          $idContact = (int) ($contact['id'] ?? 0);

          if ($idContact == 0) {
            $idContact = $mContact->record->recordCreate(["first_name" => $firstName, "middle_name" => $middleName, "last_name" => $lastName])['id'];
          }

          foreach ($values as $value) {
            if (!$mValue->record->where("id_contact", $idContact)->where("value", $value)->first()) {
              $mValue->record->recordCreate(["id_contact" => $idContact, "value" => $value]);
            }
          }

        }
        fclose($handle);
      }
    }

    $this->setView('@HubletoApp:Community:Contacts/Import.twig');
  }

}
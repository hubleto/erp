<?php

namespace HubletoApp\Community\Contacts\Controllers;

class Import extends \HubletoMain\Controller
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

    $mContact = $this->getModel(\HubletoApp\Community\Contacts\Models\Contact::class);
    $mContactTag = $this->getModel(\HubletoApp\Community\Contacts\Models\ContactTag::class);
    $mTag = $this->getModel(\HubletoApp\Community\Contacts\Models\Tag::class);
    $mValue = $this->getModel(\HubletoApp\Community\Contacts\Models\Value::class);

    $log = [];
    $importFinished = false;
    $checkImport = $this->getRouter()->urlParamAsBool("checkImport");
    $tag = $this->getRouter()->urlParamAsString("tag");

    $theTag = $mTag->record->whereLike('name', $tag)->first()?->toArray();

    if (!is_array($theTag)) {
      $theTag = $mTag->record->recordCreate(['name' => $tag]);
    }

    $contactsFile = $this->getRouter()->getUploadedFile('contactsFile');
    if (is_array($contactsFile) && is_file($contactsFile['tmp_name'])) {
      if (($handle = fopen($contactsFile['tmp_name'], "r")) !== false) {
        $rowIdx = 0;
        while (($row = fgetcsv($handle, 0, ";")) !== false) {
          if ($rowIdx++ == 0) {
            continue;
          }

          $row = array_map(function ($str) { return iconv("Windows-1250", "UTF-8", $str); }, $row);

          $firstName = '';
          $middleName = '';
          $lastName = '';
          $values = [];
          for ($i = 0; $i < count($row); $i++) {
            $v = trim($row[$i]);
            if (empty($v)) {
              continue;
            }
            if ($i == 0) {
              $firstName = $v;
            } elseif ($i == 1) {
              $middleName = $v;
            } elseif ($i == 2) {
              $lastName = $v;
            } else {
              $values[] = $v;
            }
          }

          if (count($values) == 0) {
            $log[] = "No contacts for `{$firstName}, {$middleName}, {$lastName}` found. Skipping.";
            continue;
          }

          $log[] = "Importing `{$firstName}, {$middleName}, {$lastName}` with contacts: " . join(", ", $values);

          if (empty($firstName) && empty($middleName) && empty($lastName)) {
            $log[] = "  [WARNING] Contact has no name.";
          }

          $contact = $mContact->record
            ->with('VALUES')
            ->whereHas('VALUES', function ($q) use ($values) {
              $q->where(function ($qq) use ($values) {
                foreach ($values as $value) {
                  if (!empty($value)) {
                    $qq->orWhere('value', $value);
                  }
                }
              });
            })
            ->first()
            ?->toArray()
          ;

          $idContact = (int) ($contact['id'] ?? 0);

          if ($idContact > 0) {
            $log[] = "  Contact with one of these values (" . join(", ", $values) . ") have been found with ID = {$idContact}. Skipping.";
          } else {
            if ($checkImport) {
              //
            } else {
              $idContact = $mContact->record->recordCreate([
                "first_name" => $firstName,
                "middle_name" => $middleName,
                "last_name" => $lastName,
                "is_valid" => true,
              ])['id'];
            }
            $log[] = "  Added contact: `{$firstName}, {$middleName}, {$lastName}`.";
          }

          if ($checkImport || $idContact > 0) {
            foreach ($values as $value) {
              if (!$mValue->record->where("id_contact", $idContact)->where("value", $value)->first()) {
                if ($checkImport) {
                  //
                } else {
                  $mValue->record->recordCreate(["id_contact" => $idContact, "value" => $value]);
                }
                $log[] = "  Added value for contact: {$value}";
              }

              if (!$mContactTag->record->where("id_contact", $idContact)->where("id_tag", $theTag['id'])->first()) {
                if ($checkImport) {
                  //
                } else {
                  $mContactTag->record->recordCreate(["id_contact" => $idContact, "id_tag" => $theTag['id']]);
                }
                $log[] = "  Added tag for contact ID {$idContact}: {$theTag['id']}, {$theTag['name']}";
              }

            }
          }

        }
        fclose($handle);
      }

      $importFinished = true;
    }

    $this->viewParams['log'] = $log;
    $this->viewParams['importFinished'] = $importFinished;
    $this->viewParams['checkImport'] = $checkImport;

    $this->setView('@HubletoApp:Community:Contacts/Import.twig');
  }

}

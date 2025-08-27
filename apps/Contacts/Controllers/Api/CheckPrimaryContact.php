<?php

namespace HubletoApp\Community\Contacts\Controllers\Api;

use HubletoApp\Community\Contacts\Models\Contact;
use HubletoApp\Community\Contacts\Models\ContactTag;
use HubletoApp\Community\Contacts\Models\Tag;

class CheckPrimaryContact extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idContact = $this->getRouter()->urlParamAsInteger("idContact");
    $idCustomer = $this->getRouter()->urlParamAsInteger("idCustomer");
    $tags = $this->getRouter()->urlParamAsArray("tags");

    if ($idContact == null || $idCustomer == null) {
      return [
        "result" => false,
        "error" => $this->getTranslator()->translate("Some request data were missing")
      ];
    }
    if ($tags == null) {
      return [ "result" => true ];
    }

    $mContact = $this->getService(Contact::class);
    $mTag = $this->getService(Tag::class);
    $mContactTag = $this->getService(ContactTag::class);

    // get the tags of the primary contacts in the customer
    $primaryContactTagIds = $mContact->record
      ->where("is_primary", 1)
      ->where("id_customer", $idCustomer)
      ->join($mContactTag->table, "{$mContactTag->table}.id_contact", "=", "{$mContact->table}.id")
    ;
    // check if we are eveluating the primary contact for an existing contact
    // if yes, then we need to skip the evaluated contact
    if ($idContact > 0) {
      $primaryContactTagIds = $primaryContactTagIds->where("{$mContact->table}.id", "!=", $idContact);
    }
    $primaryContactTagIds = $primaryContactTagIds->pluck("{$mContactTag->table}.id_tag")->toArray();

    // if no contact was found, return
    if ($primaryContactTagIds == null) {
      return [ "result" => true ];
    }

    // check if there are any matching tags
    // within the primary contacts of the customer and the evaluated contact
    $matches = array_intersect($tags, $primaryContactTagIds);

    // if there is not a match, return
    if ($matches == null) {
      return [ "result" => true ];
    } else {
      // return the names of the matching tags
      $matchesNames = $mTag->record
        ->whereIn("id", $matches)
        ->pluck("name")
        ->toArray()
      ;
      $existingTagNames = implode(", ", $matchesNames);
      return [
        "result" => false,
        "error" => $this->getTranslator()->translate("There already exists a primary contact for this customer for these tags:"),
        "names" => $existingTagNames
      ];
    }
  }
}

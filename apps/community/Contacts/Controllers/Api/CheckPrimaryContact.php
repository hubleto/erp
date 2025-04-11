<?php

namespace HubletoApp\Community\Contacts\Controllers\Api;

use HubletoApp\Community\Contacts\Models\Person;
use HubletoApp\Community\Contacts\Models\PersonTag;
use HubletoApp\Community\Contacts\Models\Tag;

class CheckPrimaryContact extends \HubletoMain\Core\Controller {
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $idPerson = $this->main->urlParamAsInteger("idPerson");
    $idCustomer = $this->main->urlParamAsInteger("idCustomer");
    $tags = $this->main->urlParamAsArray("tags");

    if ($idPerson == null || $idCustomer == null)
      return [
        "result" => false,
        "error" => $this->main->translate("Some request data were missing")
      ];
    if ($tags == null) return [ "result" => true ];

    $mPerson = new Person($this->main);
    $mPersonTag = new Tag($this->main);
    $mCrossPersonTag = new PersonTag($this->main);

    // get the tags of the primary contacts in the customer
    $primaryPersonTagIds = $mPerson->eloquent
      ->where("is_primary", 1)
      ->where("id_customer", $idCustomer)
      ->join($mCrossPersonTag->table, "{$mCrossPersonTag->table}.id_person", "=", "{$mPerson->table}.id")
    ;
    // check if we are eveluating the primary contact for an existing person
    // if yes, then we need to skip the eveluated person
    if ($idPerson > 0) $primaryPersonTagIds = $primaryPersonTagIds->where("{$mPerson->table}.id", "!=", $idPerson);
    $primaryPersonTagIds = $primaryPersonTagIds->pluck("{$mCrossPersonTag->table}.id_tag")->toArray();

    // if no person was found, return
    if ($primaryPersonTagIds == null) return [ "result" => true ];

    // check if there are any matching tags
    // within the primary contacts of the customer and the evalueted person
    $matches = array_intersect($tags, $primaryPersonTagIds);

    // if there is not a match, return
    if ($matches == null) return [ "result" => true ];
    else {
      // return the names of the matching tags
      $matchesNames = $mPersonTag->eloquent
        ->whereIn("id", $matches)
        ->pluck("name")
        ->toArray()
      ;
      $existingTagNames = implode(", ", $matchesNames);
      return [
        "result" => false,
        "error" => $this->main->translate("There already exists a primary contact person for this customer for these tags:"),
        "names" => $existingTagNames
      ];
    }
  }
}

<?php

namespace HubletoMain\Core;

class Helper
{
  public \HubletoMain $main;
  public \ADIOS\Core\Loader $app;

  public function __construct(\HubletoMain $main, \ADIOS\Core\Loader $app)
  {
    $this->main = $main;
    $this->app = $app;
  }

  /** Function serves as a delete funtion for the Tag Input
   * @param array $recordTags The IDs of the saved tags
   * @param string $crossTagModelName The string of the model from which the tag entries will be deleted
   * @param string $lookupColumnName Name of the lookup column in the cross tag model
   * @param int $lookupId The id of the lookup
   */
  public function deleteTags(
    array $recordTagsIds,
    string $crossTagModelName,
    string $lookupColumnName,
    int $lookupId
  ): void {
    $mCrossTag = $this->app->getModel($crossTagModelName);
    $existingTagsIds = $mCrossTag->record
      ->where($lookupColumnName, $lookupId)
      ->pluck("id")
      ->toArray()
    ;
    $differences = array_diff($existingTagsIds, $recordTagsIds);
    $mCrossTag->record
      ->whereIn("id", $differences)
      ->where($lookupColumnName, $lookupId)
      ->delete()
    ;
  }
}

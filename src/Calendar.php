<?php declare(strict_types=1);

namespace Hubleto\Erp;

class Calendar extends \Hubleto\Erp\Core
{

  /**
   * Specifies what Activity Form component will be opened and what title should be used for a new button in the `FormActivitySelector.tsx` component
   * @var array{"title": string, "formComponent": string}
   * */
  public array $calendarConfig = [
    "title" => "",
    "addNewActivityButtonText" => "",
    "formComponent" => ""
  ];

  protected \Hubleto\Framework\Interfaces\AppInterface $app;
  protected string $color = 'blue';

  public function setApp(\Hubleto\Framework\Interfaces\AppInterface $app): void
  {
    $this->app = $app;
  }

  public function getApp(): \Hubleto\Framework\Interfaces\AppInterface
  {
    return $this->app;
  }

  public function setColor(string $color): void
  {
    $this->color = $color;
  }

  public function getColor(): string
  {
    return $this->color;
  }

  /**
   * Loads specified event info.
   *
   * @param int $id
   * 
   * @return array
   * 
   */
  public function loadEvent(int $id): array
  {
    return [];
  }

  /**
   * Loads event from calendar between dateStart and dateEnd.
   *
   * @param string $dateStart
   * @param string $dateEnd
   * @param array $filter
   * 
   * @return array
   * 
   */
  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    return [];
  }

  public function convertActivitiesToEvents(string $source, array $activities, \Closure $detailsCallback): array
  {
    return [];
  }

}

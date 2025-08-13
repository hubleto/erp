<?php declare(strict_types=1);

namespace HubletoMain;

class Calendar
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

  public function __construct(public \Hubleto\Framework\Loader $main)
  {
  }

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

  public function loadEvent(int $id): array
  {
    return [];
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    return [];
  }

  public function convertActivitiesToEvents(string $source, array $activities, \Closure $detailsCallback): array
  {
    return [];
  }

}

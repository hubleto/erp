<?php

namespace Hubleto\App\Community\PriceList\Services;

/**
 * Rounding policies used for prices and amounts.
 *
 * A policy is an array with keys:
 *   policy   (int)   one of the POLICY_* constants
 *   step     (float) step for nearest/up/down rounding (0 = use decimals)
 *   ending   (float) price ending for POLICY_ENDS_IN (e.g., 0.99)
 *   decimals (int|null) decimals of the final value
 */
class Rounding extends \Hubleto\Erp\Core
{
  public const POLICY_NONE = 0;
  public const POLICY_NEAREST = 1;
  public const POLICY_UP = 2;
  public const POLICY_DOWN = 3;
  public const POLICY_ENDS_IN = 4;

  /**
   * Rounds the value according to the policy.
   *
   * @param float $value
   * @param array|null $policy
   *
   * @return float
   *
   */
  public function round(float $value, ?array $policy = null): float
  {
    $policy = array_merge(['policy' => self::POLICY_NONE, 'step' => 0.0, 'ending' => 0.0, 'decimals' => null], $policy ?? []);
    $step = (float) $policy['step'];
    $decimals = $policy['decimals'] === null ? null : (int) $policy['decimals'];

    switch ((int) $policy['policy']) {
      case self::POLICY_NEAREST:
        $result = $step > 0 ? $this->roundToStep($value, $step, PHP_ROUND_HALF_UP) : round($value, $decimals ?? 2, PHP_ROUND_HALF_UP);
      break;
      case self::POLICY_UP:
        $result = $step > 0 ? $this->ceilToStep($value, $step) : ceil(round($value, 8));
      break;
      case self::POLICY_DOWN:
        $result = $step > 0 ? $this->floorToStep($value, $step) : floor(round($value, 8));
      break;
      case self::POLICY_ENDS_IN:
        $result = $this->roundEndsIn($value, (float) $policy['ending']);
      break;
      default:
        $result = $value;
    }

    return round($result, $decimals ?? 8, PHP_ROUND_HALF_UP);
  }

  /** Rounds a money value to the given number of decimals (half up). */
  public function roundMoney(float $value, int $decimals): float
  {
    return round($value, $decimals, PHP_ROUND_HALF_UP);
  }

  /** Rounds to the nearest multiple of the step. */
  public function roundToStep(float $value, float $step, int $mode = PHP_ROUND_HALF_UP): float
  {
    if ($step <= 0) return $value;
    return round(round($value / $step, 8), 0, $mode) * $step;
  }

  /** Rounds up to a multiple of the step. */
  public function ceilToStep(float $value, float $step): float
  {
    if ($step <= 0) return $value;
    return ceil(round($value / $step, 8)) * $step;
  }

  /** Rounds down to a multiple of the step. */
  public function floorToStep(float $value, float $step): float
  {
    if ($step <= 0) return $value;
    return floor(round($value / $step, 8)) * $step;
  }

  /**
   * Rounds to the nearest price that ends with the given fraction, e.g. 0.99 (12.47 becomes 12.99, 12.10 becomes 11.99).
   */
  public function roundEndsIn(float $value, float $ending): float
  {
    $base = floor($value);
    $upper = $base + $ending;
    $lower = $base - 1 + $ending;

    return (abs($upper - $value) <= abs($value - $lower)) ? $upper : $lower;
  }

  /** Cash rounding of a payable amount to a step (0 = off). */
  public function cashRound(float $value, float $step): float
  {
    if ($step <= 0) return $value;
    return $this->roundToStep($value, $step);
  }
}

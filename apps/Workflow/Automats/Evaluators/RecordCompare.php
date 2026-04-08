<?php

namespace Hubleto\App\Community\Workflow\Automats\Evaluators;

use Hubleto\App\Community\Workflow\Interfaces\AutomatEvaluatorInterface;
use Hubleto\Erp\Core;

class RecordCompare extends Core implements AutomatEvaluatorInterface
{

  /**
   * [Description for matches]
   *
   * @param array $condition
   * 
   * @return void
   * 
   */
  public function matches(array $condition): bool
  {
    $updatedRecord = $condition['updatedRecord'] ?? '';
    $column = $condition['column'] ?? '';
    $operator = $condition['operator'] ?? 'equals';
    $rightOperand = $condition['value'] ?? null;

    $match = true;

    $dots = substr_count($column, '.');

    if ($dots == 0) {
      $leftOperand = $updatedRecord[$column] ?? null;
    } else if ($dots == 1) {
      [$tmp1, $tmp2] = explode('.', $column, 2);
      $leftOperand = $updatedRecord[$tmp1][$tmp2] ?? null;
    } else if ($dots == 2) {
      [$tmp1, $rest] = explode('.', $column, 2);
      [$tmp2, $tmp3] = explode('.', $rest, 2);
      $leftOperand = $updatedRecord[$tmp1][$tmp2][$tmp3] ?? null;
    } else {
      $leftOperand = null;
    }

    switch ($operator) {
      case '=': if ($leftOperand != $rightOperand) $match = false; break;
      case '!=': if ($leftOperand == $rightOperand) $match = false; break;
      case '>': if ($leftOperand <= $rightOperand) $match = false; break;
      case '>=': if ($leftOperand < $rightOperand) $match = false; break;
      case '<': if ($leftOperand >= $rightOperand) $match = false; break;
      case '<=': if ($leftOperand > $rightOperand) $match = false; break;
      default: $match = false; break;
    }

    return $match;
  }

}
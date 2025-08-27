<?php

namespace HubletoApp\Community\Notifications;

class Digest extends \Hubleto\Framework\Core
{

  /**
   * [Description for getDailyDigestForUser]
   *
   * @param array $user
   * 
   * @return [type]
   * 
   */
  public function getDailyDigestForUser(array $user)
  {
    $dailyDigest = [];
    $digestHtml = '';

    $apps = $this->getAppManager()->getEnabledApps();
    foreach ($apps as $appNamespace => $app) {
      $appDigest = [];

      $dailyDigestClass = '\\' . $appNamespace . '\\Controllers\\Api\\DailyDigest';
      if (class_exists($dailyDigestClass)) {
        $dailyDigestController = $this->getService($dailyDigestClass);
        $dailyDigestController->user = $user;
        $appDigest = $dailyDigestController->response();
      }

      if (count($appDigest) > 0) {
        $dailyDigest[$appNamespace] = $appDigest;
      }
    }

    if (count($dailyDigest) > 0) {
      $digestHtml = '<h1>Hi ' . ($user['nick'] ?? ($user['first_name'] ?? '')) . ', here is your daily digest</h1>';
      foreach ($dailyDigest as $appNamespace => $items) {
        $digestHtml .= "<h3>{$appNamespace}</h3>";
        foreach ($items as $item) {
          $digestHtml .= "
            <div style='font-size:11pt;margin-bottom:0.25em;padding:0.25em;border:1px solid #EEEEEE;border-left:0.5em solid {$item['color']}'>
              <b>" . htmlspecialchars($item['category']) . "</b>
              <a href='{$this->getEnv()->projectUrl}/{$item['url']}'>" . htmlspecialchars($item['text']) . "</a><br/>
              <small>" . htmlspecialchars($item['description']) . "</small>
            </div>
          ";
        }
      }
    }

    return $digestHtml;
  }

}

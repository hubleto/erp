<?php

namespace Hubleto\App\Community\Campaigns;

use \Hubleto\Erp\Core;
use \Hubleto\Framework\Env;

class Lib extends Core
{

  /**
   * [Description for getMailPreview]
   *
   * @param array $campaign
   * @param array $contact
   * @param array $vars
   * 
   * @return string
   * 
   */
  public static function getMailPreview(array $campaign, array $recipient): string
  {

    $bodyHtml = Lib::addUtmVariablesToEmailLinks(
      (string) ($campaign['MAIL_TEMPLATE']['body_html'] ?? ''),
      (string) ($campaign['utm_source'] ?? ''),
      (string) ($campaign['utm_campaign'] ?? ''),
      (string) ($campaign['utm_term'] ?? ''),
      (string) ($campaign['utm_content'] ?? ''),
    );

    $emailVariables = [
      'RECIPIENT:salutation' => $recipient['salutation'] ?? '',
      'RECIPIENT:first_name' => $recipient['first_name'] ?? '',
      'RECIPIENT:last_name' => $recipient['last_name'] ?? '',
    ];

    $recipientVariables = @json_decode($recipient['variables'], true);
    if (is_array($recipientVariables)) {
      $emailVariables = array_merge($emailVariables, $recipientVariables);
    }

    $bodyHtml = Lib::replaceVariables($bodyHtml, $emailVariables);

    $bodyHtml = Lib::routeLinksThroughClickTracker(
      $campaign,
      $recipient,
      $bodyHtml,
    );

    $clickTrackerUrl = self::getClickTrackerUrl();

    $botClickData = [
      'cuid' => ($campaign['uid'] ?? ''),
      'rcid' => ($recipient['id'] ?? ''),
      'bot' => true,
    ];

    $botClickDataB64 = base64_encode(json_encode($botClickData));

    $bodyHtml = str_replace(
      '{{ botDetectorHiddenLink }}',
      '<a href="' . $clickTrackerUrl . '?c=' . $botClickDataB64 . '" style="font-size:1px">&nbsp;</a>',
      $bodyHtml
    );

    return $bodyHtml;
  }

  /**
   * [Description for replaceVariables]
   *
   * @param string $body
   * @param array $vars
   * 
   * @return string
   * 
   */
  public static function replaceVariables(string $body, array $vars): string
  {
    foreach ($vars as $vName => $vValue) {
      $body = str_replace('{{ ' . $vName . ' }}', $vValue, $body);
      $body = str_replace('{{' . $vName . '}}', $vValue, $body);
      $body = str_replace('{{ ' . $vName . '}}', $vValue, $body);
      $body = str_replace('{{' . $vName . ' }}', $vValue, $body);
    }

    return $body;
  }

  /**
   * [Description for addUtmVariablesToEmailLinks]
   *
   * @param string $body
   * @param string $utmSource
   * @param string $utmCampaign
   * @param string $utmTerm
   * @param string $utmContent
   * 
   * @return string
   * 
   */
  public static function addUtmVariablesToEmailLinks(
    string $body,
    string $utmSource,
    string $utmCampaign,
    string $utmTerm,
    string $utmContent
  ): string
  {
    $body = str_replace('{{ utmSource }}', urlencode($utmSource), $body);
    $body = str_replace('{{ utmCampaign }}', urlencode($utmCampaign), $body);
    $body = str_replace('{{ utmTerm }}', urlencode($utmTerm), $body);
    $body = str_replace('{{ utmContent }}', urlencode($utmContent), $body);

    return $body;
  }

  public static function getClickTrackerUrl(): string
  {
    return Core::getServiceStatic(Env::class)->projectUrl . '/campaigns/click-tracker';
  }

  /**
   * [Description for routeLinksThroughClickTracker]
   *
   * @param array $campaign
   * @param array $contact
   * @param string $body
   * 
   * @return string
   * 
   */
  public static function routeLinksThroughClickTracker(array $campaign, array $recipient, string $body): string
  {
    $clickTrackerUrl = self::getClickTrackerUrl();

    $body = preg_replace_callback(
      '/(<a\s*)href="([^"]*)"/i',
      function($m) use ($clickTrackerUrl, $campaign, $recipient) {

        $clickData = [
          'cuid' => ($campaign['uid'] ?? ''),
          'rcid' => ($recipient['id'] ?? ''),
          'url' => $m[2],
        ];

        $clickDataB64 = base64_encode(json_encode($clickData));

        return $m[1] . 'href="' . $clickTrackerUrl . '?c=' . $clickDataB64 . '"';
      },
      $body
    );

    $body = preg_replace_callback(
      '/(<a\s*)href=\'([^\']*)\'/i',
      function($m) use ($clickTrackerUrl, $campaign, $recipient) {
        return 
          $m[1] . 'href=\'' . $clickTrackerUrl
          . '?cuid=' . ($campaign['uid'] ?? '')
          . '&rcid=' . ($recipient['id'] ?? '')
          . '&url=' . urlencode($m[2])
          . '\''
        ;
      },
      $body
    );

    return $body;
  }

}
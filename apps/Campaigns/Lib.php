<?php

namespace Hubleto\App\Community\Campaigns;

use \Hubleto\Framework\Core;
use \Hubleto\Framework\Env;

class Lib extends \Hubleto\Framework\Core
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
  public static function getMailPreview(array $campaign, array $contact): string
  {

    $bodyHtml = Lib::addUtmVariablesToEmailLinks(
      (string) ($campaign['MAIL_TEMPLATE']['body_html'] ?? ''),
      (string) ($campaign['utm_source'] ?? ''),
      (string) ($campaign['utm_campaign'] ?? ''),
      (string) ($campaign['utm_term'] ?? ''),
      (string) ($campaign['utm_content'] ?? ''),
    );

    $bodyHtml = Lib::replaceVariables($bodyHtml, [
      'CONTACT:salutation' => $contact['salutation'] ?? '',
      'CONTACT:title_before' => $contact['title_before'] ?? '',
      'CONTACT:first_name' => $contact['first_name'] ?? '',
      'CONTACT:middle_name' => $contact['middle_name'] ?? '',
      'CONTACT:last_name' => $contact['last_name'] ?? '',
      'CONTACT:title_after' => $contact['title_after'] ?? '',
    ]);

    $bodyHtml = Lib::routeLinksThroughCampaignTracker(
      $campaign,
      $contact,
      $bodyHtml,
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

  /**
   * [Description for routeLinksThroughCampaignTracker]
   *
   * @param array $campaign
   * @param array $contact
   * @param string $body
   * 
   * @return string
   * 
   */
  public static function routeLinksThroughCampaignTracker(array $campaign, array $contact, string $body): string
  {
    $trackerUrl = Core::getServiceStatic(Env::class)->projectUrl . '/campaigns/click-tracker';

    $body = preg_replace_callback(
      '/(<a\s*)href="([^"]*)"/i',
      function($m) use ($trackerUrl, $campaign, $contact) {
        return 
          $m[1] . 'href="' . $trackerUrl
          . '?cuid=' . ($campaign['uid'] ?? '')
          . '&cnid=' . ($contact['id'] ?? '')
          . '&url=' . urlencode($m[2])
          . '"'
        ;
      },
      $body
    );

    $body = preg_replace_callback(
      '/(<a\s*)href=\'([^\']*)\'/i',
      function($m) use ($trackerUrl, $campaign, $contact) {
        return 
          $m[1] . 'href=\'' . $trackerUrl
          . '?cuid=' . ($campaign['uid'] ?? '')
          . '&cnid=' . ($contact['id'] ?? '')
          . '&url=' . urlencode($m[2])
          . '\''
        ;
      },
      $body
    );

    return $body;
  }

}
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

    $bodyHtml = Lib::routeLinksThroughCampaignTracker(
      $campaign,
      $contact,
      $bodyHtml,
    );

    return $bodyHtml;
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
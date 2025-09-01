<?php

namespace Hubleto\App\Community\Campaigns;

use \Hubleto\Framework\Core;
use \Hubleto\Framework\Env;

class Lib extends \Hubleto\Framework\Core
{

  public static function addUtmVariablesToEmailLinks(
    string $body,
    string $utmSource,
    string $utmCampaign,
    string $utmTerm,
    string $utmContent
  ): string
  {
    // $utmUrlString = 'utm_source=' . urlencode($utmSource);
    // $utmUrlString .= '&utm_campaign=' . urlencode($utmCampaign);
    // $utmUrlString .= '&utm_term=' . urlencode($utmTerm);
    // $utmUrlString .= '&utm_content=' . urlencode($utmContent);

    // $body = preg_replace('/(<a.*)href="([^"]*)\?([^"]*)"(.*>)/', '$1href="$2?$3&' . $utmUrlString . '"$4', $body);
    // $body = preg_replace('/(<a.*)href="([^"?]*)"([^"]*>)/', '$1href="$2?' . $utmUrlString . '"$3', $body);

    // $body = preg_replace("/(<a.*)href='([^']*)\\?([^']*)'(.*>)/", '$1href=\'$2?$3&' . $utmUrlString . '\'$4', $body);
    // $body = preg_replace("/(<a.*)href='([^'?]*)'([^']*>)/", '$1href=\'$2?' . $utmUrlString . '\'$3', $body);

    $body = str_replace('{{ utmSource }}', urlencode($utmSource), $body);
    $body = str_replace('{{ utmCampaign }}', urlencode($utmCampaign), $body);
    $body = str_replace('{{ utmTerm }}', urlencode($utmTerm), $body);
    $body = str_replace('{{ utmContent }}', urlencode($utmContent), $body);

    return $body;
  }

  public static function routeLinksThroughCampaignTracker(array $campaign, array $contact, string $body): string
  {
    $trackerUrl = Core::getServiceStatic(Env::class)->projectUrl . '/campaigns/tracker';

    $body = preg_replace_callback(
      '/(<a.*)href="([^"]*)"(.*<\/a>)/',
      function($m) use ($trackerUrl, $campaign, $contact) {
        return 
          $m[1] . 'href="' . $trackerUrl
          . '?cuid=' . $campaign['uid']
          . '&cnid=' . $contact['id']
          . '&url=' . urlencode($m[2])
          . '"' . $m[3]
        ;
      },
      $body
    );

    $body = preg_replace_callback(
      '/(<a.*)href=\'([^\']*)\'(.*<\/a>)/',
      function($m) use ($trackerUrl, $campaign, $contact) {
        return 
          $m[1] . 'href=\'' . $trackerUrl
          . '?cuid=' . $campaign['uid']
          . '&cnid=' . $contact['id']
          . '&url=' . urlencode($m[2])
          . '\'' . $m[3]
        ;
      },
      $body
    );

    return $body;
  }

}
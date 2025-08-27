<?php

namespace HubletoApp\Community\Campaigns;

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

}
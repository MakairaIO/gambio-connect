<?php

namespace GXModules\Makaira\GambioConnect\App\Core;

class RequestBuilder
{

  public function __construct(private string $language)
  {
  }

  public function getConstraint($additionalConstraints = null)
  {

    $constraints = [
      'query.shop_id'   =>  "1",
      'query.use_stock'   =>  true,
      'oi.user.agent'   =>  $this->getUserAgent(),
      'oi.user.ip'    =>  $this->getIpAddress(),
      'oi.user.timezone'    =>  $this->getTimeZone(),
      'ab.experiments'    =>  $this->getExperiments(),
    ];

    if ($this->language) {
      $constraints['query.language'] = $this->language;
    }

    if ($additionalConstraints->language) {
      $constraints['query.language'] = $additionalConstraints->language;
    }

    return $constraints;
  }

  private function getUserAgent()
  {
    return "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36";
  }

  private function getIpAddress()
  {
    return '::1';
  }

  private function getTimeZone()
  {
    return '';
  }

  private function getExperiments()
  {
    return null;
  }

}

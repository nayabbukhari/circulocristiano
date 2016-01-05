<?php

class M2_Stripe_RateLimitError extends M2_Stripe_InvalidRequestError
{
  public function __construct($message, $param, $httpStatus=null,
      $httpBody=null, $jsonBody=null
  )
  {
    parent::__construct($message, $httpStatus, $httpBody, $jsonBody);
  }
}
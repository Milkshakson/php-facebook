<?php
use League\OAuth2\Client\Provider\Facebook;
class Facebook2 extends League\OAuth2\Client\Provider\Facebook
{
    public function __construct($config){
        parent::__construct($config);
    }
}
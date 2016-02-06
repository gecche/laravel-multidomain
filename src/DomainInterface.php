<?php

namespace Gecche\Multidomain;

interface DomainInterface
{
    public function getEnvironment();

    public function setEnvironment($environment = null);

	 public function getDomain();

    public function setDomain($domain = null);
	 
}
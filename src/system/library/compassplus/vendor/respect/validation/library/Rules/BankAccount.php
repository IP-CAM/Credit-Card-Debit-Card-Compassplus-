<?php
 namespace Respect\Validation\Rules; use Respect\Validation\Rules\Locale\Factory; class BankAccount extends AbstractWrapper { public function __construct($countryCode, $bank, Factory $factory = null) { if (null === $factory) { $factory = new Factory(); } $this->validatable = $factory->bankAccount($countryCode, $bank); } } 
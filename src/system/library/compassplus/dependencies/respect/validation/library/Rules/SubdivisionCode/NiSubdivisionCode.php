<?php
 namespace Respect\Validation\Rules\SubdivisionCode; use Respect\Validation\Rules\AbstractSearcher; class NiSubdivisionCode extends AbstractSearcher { public $haystack = [ 'AN', 'AS', 'BO', 'CA', 'CI', 'CO', 'ES', 'GR', 'JI', 'LE', 'MD', 'MN', 'MS', 'MT', 'NS', 'RI', 'SJ', ]; public $compareIdentical = true; } 
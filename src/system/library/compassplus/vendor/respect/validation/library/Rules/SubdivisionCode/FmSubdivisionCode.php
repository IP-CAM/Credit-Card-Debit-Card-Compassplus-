<?php
 namespace Respect\Validation\Rules\SubdivisionCode; use Respect\Validation\Rules\AbstractSearcher; class FmSubdivisionCode extends AbstractSearcher { public $haystack = [ 'KSA', 'PNI', 'TRK', 'YAP', ]; public $compareIdentical = true; } 
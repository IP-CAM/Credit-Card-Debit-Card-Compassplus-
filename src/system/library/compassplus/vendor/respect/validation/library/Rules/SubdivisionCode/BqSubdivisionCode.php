<?php
 namespace Respect\Validation\Rules\SubdivisionCode; use Respect\Validation\Rules\AbstractSearcher; class BqSubdivisionCode extends AbstractSearcher { public $haystack = [ 'BO', 'SA', 'SE', ]; public $compareIdentical = true; } 
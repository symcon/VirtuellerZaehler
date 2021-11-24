<?php

declare(strict_types=1);
include_once __DIR__ . '/stubs/Validator.php';
class VirtuellerZaehlerValidationTest extends TestCaseSymconValidation
{
    public function testValidateVirtuellerZaehler(): void
    {
        $this->validateLibrary(__DIR__ . '/..');
    }
    public function testValidateVirtuellerZaehlerModule(): void
    {
        $this->validateModule(__DIR__ . '/../VirtuellerZaehler');
    }
}
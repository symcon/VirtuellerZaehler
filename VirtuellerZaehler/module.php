<?php

declare(strict_types=1);
class VirtuellerZaehler extends IPSModule
{
    public function Create()
    {
        // Do not delete this line
        parent::Create();

        //Register Properties
        $this->RegisterPropertyFloat('max', 0.0);

        //Register Variable
        $this->RegisterVariableFloat('currentCounter', $this->Translate('Current counter'));
        $this->RegisterVariableString('newCounter', $this->Translate('New counter'));

        $this->EnableAction('newCounter');

        //Register Message
        $this->RegisterMessage($this->GetIDForIdent('newCounter'), VM_UPDATE);
    }

    public function ApplyChanges()
    {
        // Do not delete this line
        parent::ApplyChanges();
        $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];

        if (!AC_GetLoggingStatus($archiveID, $this->GetIDForIdent('currentCounter'))) {
            $this->UpdateFormField('logging', 'visible', true);
        } else {
            $this->UpdateFormField('logging', 'visible', false);
        }
    }

    //If the variable is updatetd writeNewCounterValue() will be called
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        if ($Message == VM_UPDATE) {
            $this->writeNewCounterValue($this->GetValue('newCounter'));
        }
    }

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident) {
                case 'newCounter':
                    $this->writeNewCounterValue($Value);
                    break;
            }
    }

    //Check if the new value is valid
    public function writeNewCounterValue(string $Value)
    {
        $currentCounter = $this->GetValue('currentCounter');
        $number = str_replace(',', '.', $Value);
        $newCounter = floatval($number);

        //New value is not negative
        if ($newCounter < 0) {
            echo $this->Translate('The value is negativ');
            return;
        }
        //New value is not lower than old one
        elseif ($newCounter < $currentCounter) {
            echo $this->Translate('The value is too low');
            return;
        } 
        //New Value inside the set threshold
        elseif ($this->ReadPropertyFloat('max') != 0 && $newCounter > ($currentCounter + $this->ReadPropertyFloat('max'))) {
            echo $this->Translate('The value is too high');
            return;
        }

        $currentCounter = $newCounter;
        $this->SetValue('currentCounter', $currentCounter);
        $this->SetValue('newCounter', '');
    }

    //Activate logging
    public function activateLogging()
    {
        $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
        AC_SetLoggingStatus($archiveID, $this->GetIDForIdent('currentCounter'), true);
        echo 'OK';
        $this->UpdateFormField('logging', 'visible', false);
    }
}
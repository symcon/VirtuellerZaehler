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
        $this->RegisterPropertyBoolean('toggleScript', false);

        //Register Variable
        $this->RegisterVariableFloat('currentCounter', $this->Translate('Current counter'));
        $this->RegisterVariableString('newCounter', $this->Translate('New counter'));
        $this->EnableAction('newCounter');

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

        if ($this->ReadPropertyBoolean('toggleScript')) {
            $this->UnregisterMessage($this->GetIDForIdent('newCounter'), VM_UPDATE);
            //Register Script
            $this->RegisterScript('setNewCounter', $this->Translate('Set counter'), "<?php\n\$id=IPS_GetParent(\$_IPS['SELF']);\nVZ_writeNewCounterValue(\$id);");
        } else {
            //Unregister Script
            if ($this->GetIDForIdent('setNewCounter') != 0){
                IPS_DeleteScript($this->GetIDForIdent('setNewCounter'), true);
            }
        }
    }

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
                    {
                        $this->SetValue($Ident, $Value);
                        if (!$this->ReadPropertyBoolean('toggleScript')) {
                            $this->writeNewCounterValue();
                        }
                    }
                    break;
            }
    }

    //Check if the new Value is valid
    public function writeNewCounterValue()
    {
        $currentCounter = $this->GetValue('currentCounter');
        $number = str_replace(',', '.', $this->GetValue('newCounter'));
        $newCounter = floatval($number);

        if ($newCounter < 0) {
            echo $this->Translate('The value is negativ');
            return;
        }
        if ($newCounter < $currentCounter) {
            echo $this->Translate('The value is too low');
            return;
        }
        if ($this->ReadPropertyFloat('max') != 0) {
            if ($newCounter > ($currentCounter + $this->ReadPropertyFloat('max'))) {
                echo $this->Translate('The value is too high');
                return;
            }
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
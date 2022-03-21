<?php

declare(strict_types=1);
class VirtuellerZaehler extends IPSModule
{
    public function Create()
    {
        // Do not delete this line
        parent::Create();

        //Register Properties
        $this->RegisterPropertyFloat('Max', 0.0);
        $this->RegisterPropertyBoolean('ToggleScript', false);

        //Register Variable
        $this->RegisterVariableFloat('CurrentCounter', $this->Translate('Current counter'));
        $this->RegisterVariableString('NewCounter', $this->Translate('New counter'));
        $this->EnableAction('NewCounter');

        if (!IPS_VariableProfileExists('VZ.Confirm')) {
            IPS_CreateVariableProfile('VZ.Confirm', 0);
            IPS_SetVariableProfileAssociation('VZ.Confirm', true, $this->Translate('Confirm'), 'Ok', '0x00FF00');
            IPS_SetVariableProfileAssociation('VZ.Confirm', false, $this->Translate('Denied'), 'Cross', '0xFF0000');
        }

        $this->RegisterMessage($this->GetIDForIdent('NewCounter'), VM_UPDATE);
    }

    public function ApplyChanges()
    {
        // Do not delete this line
        parent::ApplyChanges();
        $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];

        if ($this->ReadPropertyBoolean('ToggleScript')) {
            $this->UnregisterMessage($this->GetIDForIdent('NewCounter'), VM_UPDATE);
            //Register Script
            $this->RegisterScript('SetNewCounter', $this->Translate('Set counter'), "<?php\n\$id=IPS_GetParent(\$_IPS['SELF']);\nVZ_writeNewCounterValue(\$id);");
        } else {
            //Unregister Script
            if (@$this->GetIDForIdent('SetNewCounter') != 0) {
                IPS_DeleteScript($this->GetIDForIdent('SetNewCounter'), true);
            }
        }
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        if ($Message == VM_UPDATE) {
            //$this->writeNewCounterValue($this->GetValue('NewCounter'));
        }
    }

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident) {
                case 'NewCounter':
                    {
                        $this->SetValue($Ident, $Value);
                        if (!$this->ReadPropertyBoolean('ToggleScript')) {
                            $this->writeNewCounterValue();
                        }
                    }
                    break;
                case 'Request':
                    {
                        if ($Value) {
                            $number = $this->GetValue('NewCounter');
                            $this->SetValue('CurrentCounter', $number);
                            $this->SetValue('NewCounter', '');
                            $this->UnregisterVariable('Request');
                        } else {
                            if ($this->GetValue('NewCounter') != '') {
                                $this->UnregisterVariable('Request');
                            }
                        }
                    }
            }
    }

    public function GetConfigurationForm()
    {
        $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
        if (AC_GetLoggingStatus($archiveID, $this->GetIDForIdent('CurrentCounter'))) {
            $visible = false;
        } else {
            $visible = true;
        }

        $data = json_decode(file_get_contents(__DIR__ . '/form.json'));
        $data->actions[0]->visible = $visible;
        return json_encode($data);
    }

    //Check if the new Value is valid
    public function writeNewCounterValue()
    {
        $currentCounter = $this->GetValue('CurrentCounter');
        $number = str_replace(',', '.', $this->GetValue('NewCounter'));
        $newCounter = floatval($number);

        if ($newCounter < 0) {
            echo $this->Translate('The value is negativ');
            return;
        } elseif ($newCounter < $currentCounter) {
            echo $this->Translate('The value is too low');
            return;
        }

        if ($this->ReadPropertyFloat('Max') != 0) {
            if ($newCounter > ($currentCounter + $this->ReadPropertyFloat('Max'))) {
                echo $this->Translate('The value is too high');

                //Request if sure
                $this->RegisterVariableBoolean('Request', $this->Translate('Sure to set counter') . ': ' . $newCounter, 'VZ.Confirm');
                $this->EnableAction('Request');
                return;
            }
        }

        $currentCounter = $newCounter;
        $this->SetValue('CurrentCounter', $currentCounter);
        $this->SetValue('NewCounter', '');
    }

    //Activate logging
    public function activateLogging()
    {
        $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
        AC_SetLoggingStatus($archiveID, $this->GetIDForIdent('CurrentCounter'), true);
        echo  $this->Translate('Das Logging wurde aktiviert');
        $this->UpdateFormField('Logging', 'visible', false);
    }
}
<?php

declare(strict_types=1);
class VirtuellerZaehler extends IPSModule
{
    public function Create()
    {
        // Do not delete this line
        parent::Create();

        //Register Properties
        $this->RegisterPropertyFloat('MaxDifference', 0);
        $this->RegisterPropertyBoolean('ConfirmationScript', false);

        if (!IPS_VariableProfileExists('VZ.Confirm')) {
            IPS_CreateVariableProfile('VZ.Confirm', 0);
            IPS_SetVariableProfileAssociation('VZ.Confirm', true, $this->Translate('Confirm'), 'Ok', '0x00FF00');
            IPS_SetVariableProfileAssociation('VZ.Confirm', false, $this->Translate('Cancel'), 'Cross', '0xFF0000');
        }

        if (!IPS_VariableProfileExists('VZ.NewCounter')) {
            IPS_CreateVariableProfile('VZ.NewCounter', 3);
            IPS_SetVariableProfileIcon('VZ.NewCounter', 'HollowDoubleArrowUp');
        }

        //Register Variable
        $this->RegisterVariableFloat('CurrentCounterReading', $this->Translate('Current counter reading'));
        //NewCounterReading is of type String, because of a bug in the Webfront (Float with action without a profile cannot be changed properly)
        $this->RegisterVariableString('NewCounterReading', $this->Translate('New counter reading'), 'VZ.NewCounter');
        $this->EnableAction('NewCounterReading');
    }

    public function ApplyChanges()
    {
        // Do not delete this line
        parent::ApplyChanges();

        if ($this->ReadPropertyBoolean('ConfirmationScript')) {
            //Register Script
            $this->RegisterScript('SetNewCounterReading', $this->Translate('Set counter reading'), "<?php\nVZ_WriteNewCounterValue(IPS_GetParent(\$_IPS['SELF']));");
        } else {
            //Unregister Script
            if (@$this->GetIDForIdent('SetNewCounterReading') != 0) {
                IPS_DeleteScript($this->GetIDForIdent('SetNewCounterReading'), true);
            }
        }
    }

    public function RequestAction($Ident, $Value)
    {
        switch ($Ident) {
            case 'NewCounterReading':
                {
                    $this->SetValue($Ident, $Value);
                    if (!$this->ReadPropertyBoolean('ConfirmationScript')) {
                        $this->WriteNewCounterValue();
                    }
                    break;
                }
            case 'Request':
                {
                    if ($Value) {
                        $this->SetValue('CurrentCounterReading', $this->GetValue('NewCounterReading'));
                    }
                    $this->SetValue('NewCounterReading', '');
                    $this->UnregisterVariable('Request');
                    break;
                }
        }
    }

    public function GetConfigurationForm()
    {
        $data = json_decode(file_get_contents(__DIR__ . '/form.json'));
        //Digits
        $variable = IPS_GetVariable($this->GetIDForIdent('CurrentCounterReading'));
        if ($variable['VariableCustomProfile'] != '') {
            $data->elements[0]->digits = IPS_GetVariableProfile($variable['VariableCustomProfile'])['Digits'];
        } else {
            $data->elements[0]->digits = 1;
        }

        $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
        $data->actions[0]->visible = !AC_GetLoggingStatus($archiveID, $this->GetIDForIdent('CurrentCounterReading'));
        return json_encode($data);
    }

    //Check if the new Value is valid
    public function WriteNewCounterValue()
    {
        $currentCounter = $this->GetValue('CurrentCounterReading');

        $newCounter = str_replace(',', '.', $this->GetValue('NewCounterReading'));
        if (!is_numeric($newCounter)) {
            echo $this->Translate('The value is not a number');
            return;
        }

        $newCounter = floatval($newCounter);

        if ($newCounter < 0) {
            echo $this->Translate('The value is negative');
            return;
        } elseif ($newCounter < $currentCounter) {
            echo $this->Translate('The value is too low');
            return;
        }

        if ($this->ReadPropertyFloat('MaxDifference') != 0) {
            if ($newCounter > ($currentCounter + $this->ReadPropertyFloat('MaxDifference'))) {
                echo $this->Translate('The value is too high');

                //Request if sure
                $this->RegisterVariableBoolean('Request', $this->Translate('Sure to set counter') . ': ' . $newCounter, 'VZ.Confirm');
                $this->EnableAction('Request');
                return;
            }
        }

        $this->SetValue('CurrentCounterReading', $newCounter);
        $this->SetValue('NewCounterReading', '');
        if (@$this->GetIDForIdent('Request')) {
            $this->UnregisterVariable('Request');
        }
    }

    //Activate logging
    public function ActivateLogging()
    {
        $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
        AC_SetLoggingStatus($archiveID, $this->GetIDForIdent('CurrentCounterReading'), true);
        AC_SetAggregationType($archiveID, $this->GetIDForIdent('CurrentCounterReading'), 1);
        echo $this->Translate('The logging was activate');
        $this->UpdateFormField('Logging', 'visible', false);
    }
}

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

            //Register Script
            $this->RegisterScript('setNewCounter', $this->Translate('Set counter'), "<?php\n\$id=IPS_GetParent(\$_IPS['SELF']);\nVZ_writeNewCounterValue(\$id);");
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

        public function RequestAction($Ident, $Value)
        {
            switch ($Ident) {
                case 'newCounter':
                    $this->SetValue($Ident, $Value);
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
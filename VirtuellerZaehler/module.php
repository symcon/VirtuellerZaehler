<?php
    class VirtuellerZaehler extends IPSModule {
 
        public function Create() {
            // Do not delete this line
            parent::Create();

            //Register Properties
            $this->RegisterPropertyFloat("max", 0.0);

            //Register Variable
            $this->RegisterVariableFloat("Counter", "Counter");       
            $this->RegisterVariableString("newCounter","new Counter");
            $this->EnableAction("newCounter");

            //Register Script
            $this->RegisterScript("clickForValidat", "Click for Validation", "<?php\n\$id=IPS_GetParent(\$_IPS['SELF']);\nVZ_isValid(\$id);" );

        }
 
        public function ApplyChanges() {
            // Do not delete this line
            parent::ApplyChanges();
            $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0]; 

            if(!AC_GetLoggingStatus($archiveID,  $this->GetIDForIdent("Counter")))
            {  
                $this->UpdateFormField("logging", "visible", true);
            }
            else{
                $this->UpdateFormField("logging", "visible", false);
            }
        }

        public function RequestAction($Ident, $Value){
            switch($Ident)
            {
                case 'newCounter':
                    $this->SetValue($Ident, $Value);
                    break;                    
            }
        }
 
        //Check if the new Value is valid
        public function isValid() {
            $currentCounter = $this->GetValue("Counter");
            echo $currentCounter."\n";
            $number = stR_replace(",", ".", $this->GetValue("newCounter"));
            $newCounter = floatval($number);
            echo $newCounter."\n";
            
            if($newCounter < 0){
                echo "The number is negativ";
                return;
            }
            if($newCounter < $currentCounter){
                echo "The number is too low";
                return;
            }
            if ($this->ReadPropertyFloat("max") != 0) {
                if ($newCounter > ($currentCounter+ $this->ReadPropertyFloat("max"))) {
                    echo "The number is too high";
                    return;
                }
            }

            $currentCounter = $newCounter;
            $this->SetValue("Counter", $currentCounter);
            $this->SetValue("newCounter", "");
        }

        //Activate logging
        public function activateLogging()
        {
            $archiveID = IPS_GetInstanceListByModuleID('{43192F0B-135B-4CE7-A0A7-1475603F3060}')[0];
            AC_SetLoggingStatus($archiveID, $this->GetIDForIdent("Counter"), true);
            echo "OK";
            $this->UpdateFormField("logging", "visible", false);
        }
    }
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


            //Register Message
            $this->RegisterMessage( $this->GetIDForIdent("newCounter"), VM_UPDATE);

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

        //If the Variable is updatetd isValid() should be called
        public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
        {
            if($Message == VM_UPDATE)
            {
                $this->isValid($this->GetValue("newCounter"));
            }
        }

        public function RequestAction($Ident, $Value){
            switch($Ident)
            {
                case 'newCounter':
                    $this->isValid($Value);
                    break;                    
            }
        }
 
        //Check if the new Value is valid
        public function isValid( String $Value) {
            $currentCounter = $this->GetValue("Counter");
            $newCounter = floatval($Value);
            
            if($newCounter < -1){
                echo "The number is negativ";
                return;
            }
            if($newCounter< $currentCounter){
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
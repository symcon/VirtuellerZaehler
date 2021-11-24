# Virtueller Zähler
Das Modul stellt eine Eingabemaske für manuell abgelesene Zählerstände bereit. Es übernimmt den manuellen Eintrag, prüft diesen auf Plausibilität und übergibt den Wert an eine Zählervariable, nachdem ein Script zur Bestätigung ausgeführt wird.  
Die Plausibilität wird auf negativ oder zu niedrigen Wert geprüft, sowie optional mit einem Grenzwert.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Skripte](#5-statusvariablen-und-skripte)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Anzeige des zuletzt eingegebenen Zählerstandes
* Eingabemaske für neuen Zählerstand
* Prüfung auf Plausibilität

### 2. Vorraussetzung
- IP-Symcon ab Version 6.0

### 3. Software-Instalation

* Über den Module Store das Modul "Virtueller Zähler" installieren.
* Alternativ über das Module Control folgende URL hinzufügen:
`https://github.com/symcon/VirtuellerZahler`

### 4. Einrichten der Instanzen in IP-Symcon 

- Unter "Instanz hinzufügen" kann das 'Virtueller Zähler'-Modul mithilfe des Schnellfilters gefunden werden.
    - Weitere Informationen zum Hinzufügen von Instanzen in der [Dokumentation der Instanzen](https://www.symcon.de/service/dokumentation/konzepte/instanzen/#Instanz_hinzufügen)

__Konfigurationsseite__:

Name                | Beschreibung
------------------- | ---------------------------------
Grenzwert           | Zahl, um die ein neuer Zählerwert maximal steigen darf
Logging Aktivieren  | Button, welcher das Logging der Zählervariable aktiviert. Er wird ausgeblendet, wenn das logging aktiviert ist.

### 5. Statusvariablen
Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

Name                  | Typ    | Beschreibung
--------------------- | ------ | -------------
Aktueller Zählerstand | Float  | Zeigt den zuletzt eingetragenen Zählerstand an
Neuer Zählerstand     | String | Eingabevariable für neuen Zählerstand


### 6. Webfront

Über das WebFront kann der neue Zählerstand eingegeben werden und das Skript ausgeführt werden, zudem wird der zuletzt übernommene Zählerstand angezeigt. 

### 7. PHP-Befehlsreferenzen
`void VZ_isValid(integer $instanzID);`
Überprüft ob der Wert in Variable "Neuer Zählerstand" negativ ist, der "Neue Zählerstand" kleiner dem "Aktuellen Zählerstand" oder der "Neue Zählerstand" den "Aktuellen Zählerstand" mit dem "Grenzwert" übersteigt. Wenn diese nicht zutreffen wird der neue Wert in "Aktuellen Zählerstand gesetzt. 
`VZ_isValid(12345);`

`void VZ_activateLogging(integer $inszanzID);`
Aktiviert das Logging der Variable "Aktueller Zählerstand" und setzt die Anzeige des Buttons "Logging Aktivieren" auf False. 
`VZ_aktivateLogging(12345);`

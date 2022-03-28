# Schedule

Vertretungsplan auf https://www.gauss-gymnasium.de/plan/.

## Einrichtung

1. Kopieren Sie `settings.example.php` und benennen Sie die Kopie `settings.php`.
1. Ergänzen Sie die fehlenden Einstellungen (`/* Change Me */`) in `settings.php` und konfigurieren sie die restlichen Einstellungen entsprechend Ihrer Bedürfnisse.
1. Erstellen Sie das Verzeichnis für die Plan-Dateien (Standard: `data`, in `settings.php` einstellbar).
1. Veröffentlichen Sie den Plan durch das Kopieren des gesamten Ordners auf einen Server.

### Nutzer einrichten

Nutzer werden in der `settings.php` definiert.

## Vertretungsplan anlegen

*Admin-Rechte nötig*

1. Legen Sie einen Vertretungsplan in z.B. Excel nach dem untenstehenden Schema an
1. Wählen Sie "Plan hinzufügen" im Menu
1. Stellen Sie das Datum es Plans ein
1. Kopieren Sie Plan in den Text-Editor der Seite
1. Drücken Sie "Speichern"

### Tags

Vertretungsstunden und allgemeine Informationen können mit Tags versehen: Besonderen Zeichenketten, die Zellen eine bestimmte Semantik geben. Je nach Tag kann das zu einem anderen Aussehen oder Verhalten führen.

Zuweisung eines Tags: Fügen Sie den Tag irgendwo in der Zelle mit der Information, die getaggt werden soll, ein. Der Tag wird bei der Anzeige herausgelöscht.

Folgende Tags sind verfügbar:

Tag     | Beschreibung                              | Beispiel
--------|-------------------------------------------|-------------
`&neu;` | Hebt eine Information als geändert hervor | `&neu; Aula`: ![Getaggte Inhalte werden hervorgehoben](img/ExampleLessonChanged.png)

### Format eines Vertretungsplans

Ein Vertretungsplan ist von oben nach unten folgendermaßen aufgebaut:

1. Eine Datumszeile: Enthält Datum in erster Zelle (beliebig formatiert)
1. (Optional) Eine oder mehrere leere Zeilen
1. (Optional) Eine oder mehrere Allgemeine-Informationen-Zeilen: Zeile mit allgemeiner Information in erster Zelle, restliche Zellen leer
1. Die Element-Arten aus 2 und 3 können beliebig oft wiederholt werden
1. (Optional) Ein Plan-Block:
    1. Eine Zeile Block-Beginn: In erster Zelle "Stunde", gefolgt von einer beliebigen Anzahl von Zellen mit Klassenstufen, z.B. "5" und "7d" (werden im Plan als ausklappbare Abschnitte mit dem Präfix "Klasse" angezeigt, z.B. "Klasse 5" und "Klasse 7d")
    1. Eine Gruppen-Zeile: Erste Zelle leer, die restlichen können die darüber befindliche Klassenstufe einschränken (z.B. auf bestimmte Lehrkraft). Wenn Zelle leer, wird das im Plan als "Gesamte Klasse" dargestellt
    1. Eine oder mehrere Stunden-Zeilen: Erste Zelle `n` Schulstunden schrittweise aufsteigend ("1." bis "n."), restliche Zellen jeweilige Vertretungsinformation, wenn vorhanden
    4. Der Plan-Block endet, sobald die erste Zelle einer Zeile nicht mehr die nachfolgende Schulstunde beinhaltet
1. Die Elementarten 2-5 können beliebig oft wiederholt werden

Beispiel für einen validen Stundenplan:

![Beispiel für einen Vertretungsplan](img/ExampleSchedule.png)

## JSON API

Schedule verfügt über eine einfache JSON API. Sie kann entweder alle verfügbaren den Vertretungspläne für ein konkretes Datum zurückgeben.
Die Anfrage geht durch einen POST-Request an `api/json.php` mit folgenden Request-Einstellungen.

```
POST-Request

Request-Header
Content-type: application/x-www-form-urlencoded

Request-Body
key=<API key>&date=<YYYY-MM-DD>
```

### Parameter

Parameter | Beschreibung | Wert
----------|--------------|-----
`key` | Authentifizierungs-Key für API | Erforderlich. Typ: `string`
`date` | Datum des gewünschten Vertretungsplans. Falls nicht angegeben, werden alle verfügbaren Pläne zurückgegeben | Optional. Format `YYYY-MM-DD` entsprechen

### Antworten

HTTP-Status-Code | Antwortinhalt | Beschreibung | Lösungsansatz
-----------------|---------------|--------------|----------------
`200` | Angefragte Pläne als JSON | Normalzustand | -
`400` | Fehlerbeschreibung | Das angefragte Datum ist ungültig | Stellen Sie sicher, dass das Datum im Format `YYYY-MM-DD` vorliegt
`401` | Fehlerbeschreibung | Authorisierung fehlgeschlagen. Entweder wurde keiner oder ein falscher API-Key mit der Anfrage verschickt | Überprüfen Sie den mitgeschickten API-Key
`404` | Fehlerbeschreibung | Für dieses Datum existiert kein Plan | -

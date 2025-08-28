# Events

Verity handles two different events. 
A `VerityRequestEvent` is received by Verity and is equal to a `POST` request on `/verity/reports`.
A `VerityEvent` is sent out by Verity after an validation request has been completed.

## VerityEvent

| Property | Description                     |
|----------|---------------------------------|
| `report` | VerityReport that just finished |

## VerityRequestEvent

| Property      | Description                                                           |
|---------------|-----------------------------------------------------------------------|
| `uuid`        | ID used for identification of the report                              |
| `fileName`    | Name of the file to validate                                          |
| `fileHash`    | Sha256 hash of the appended file                                      |
| `profileName` | Profile name (defined in the verity config) used to validate the file |
| `fileContent` | File to validate                                                      |
| `mimetype`    | Mime type of the file to validate                                     |
| `fileSize`    | File size of the file to validate                                     |
| `valid`       | Validity of the file, if already evalulated                           |
| `message`     | Short message about the validity, if already evalulated               |
| `errors`      | Detailed error messages, if already evalulated                        |

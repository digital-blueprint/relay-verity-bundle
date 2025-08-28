# API

## Endpoints
| Endpoint         | Method | Description                                                            | Required body parameter (as multipart/form-data) | Optional body parameter (as multipart/form-data) |
|------------------|--------|------------------------------------------------------------------------|--------------------------------------------------|--------------------------------------------------|
| `/verity/reports`  | POST   | Used to upload a file and request verification against given `profile` | `file`, `uuid`, `profile`                        | `fileHash`                                         |

## Parameters

| Parameter  | Description                                                           | Type   | Possible values         |
|------------|-----------------------------------------------------------------------|--------|-------------------------|
| `file`     | File to validate, as a binary                                         | string | any binary file         |
| `uuid`     | ID used for identification of the report                              | string | any valid UUID          |
| `profile`  | Profile name (defined in the verity config) used to validate the file | string | any valid profile name  |
| `fileHash` | Sha256 hash of the appended file                                      | string | sha256 hash of the file |


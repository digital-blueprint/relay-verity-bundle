# Configuration
## Backends
`backends` define the validator used to check for validity. Currently, there is a [pdfa validator with veraPDF](https://github.com/digital-blueprint/relay-verity-connector-verapdf-bundle) and a [anti-virus validator with clamAV](https://github.com/digital-blueprint/relay-verity-connector-clamav-bundle) available.
Each backend defines the name of the backend, and the used validator as shown in the [example](example).

```yaml
  backends:
    some_profile_name:
      validator: 'Your\Connector\Service\Validator\API'
    some_other_profile_name:
      validator: 'Your\Other\Connector\Service\Validator\API'
```

## Profiles
`profiles` define the profile validation checks and rules that a file has to pass. 
A check defines one check of a given backend with a given config that has to pass to validate the file. The config is passed to the defined validator, thus it is specific to the used connector.
A rule is a composition of multiple checks. A rule can use logic operators to allow advanced tests of checks. `.validity` returns `true` or `false` when the given check was successful or not.

```yaml
      rule: 'your_first_check_name.validity == true && some_other_profile_name.validity == true'
      checks:
        your_first_check_name:
          backend: 'some_profile_name'
          config: 'your-connector-specific-config'
        your_second_check_name:
          backend: 'some_other_profile_name'
          config: 'your-other-connector-specific-config'
```

## Example
```yaml
dbp_relay_verity:
  backends:
    pdfa:
      validator: 'Dbp\Relay\VerityConnectorVerapdfBundle\Service\PDFAValidationAPI'
  profiles:
    archive:
      name: 'Check PDFs for archiving complacency'
      rule: 'pdfa_1b.validity == true && pdfa_2b.validity == true'
      checks:
        pdfa_1b:
          backend: 'pdfa'
          config: '{"flavour": "1b"}'
        pdfa_2b:
          backend: 'pdfa'
          config: '{"flavour": "2b"}'
```
# CHANGELOG

## Unreleased

## v0.1.9
- Add support for Symfony 7

## v0.1.8
- Don't show error messages when uploaded pdf is valid
- Drop support for api-platform 3

## v0.1.7
- Add `denyAccessUnlessGranted` call to `PostValidationReportAction.php`

## v0.1.6
- Fix file upload in Swagger for `/verity/reports`

## v0.1.5
- Change signatures of `VerityProviderInterface`'s and `VeritySerice`'s `validate` function to use a File instead of a fileContent string
- Change signature of `VerityRequestEvent` to use a File instead of a fileContent string

## v0.1.4
- fix typo in verityReport outputFormats
- lockfile updates
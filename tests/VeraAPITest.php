<?php

declare(strict_types=1);

namespace Dbp\Relay\VerityBundle\Tests;

use Dbp\Relay\VerityBundle\Service\PDFAValidationAPI;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class VeraAPITest extends KernelTestCase
{
    public function testValidResult(): void
    {
        $httpClient = new MockHttpClient($this->validMockResponse());
        $veraApi = new PDFAValidationAPI('http://localhost/', $httpClient);

        $this->assertNotNull($veraApi);

        // Parameters in this call do not matter, always get the $validMockResponse.
        // This tests that a valid PDF is processed correctly.
        $result = $veraApi->validate('', 'test-010.txt', 0, sha1(''), '{"flavour": "unit_test"}', 'text/plain');

        $this->assertEquals($result->message, 'PDF file is compliant with Validation Profile requirements.');
    }

    public function testInvalidResult(): void
    {
        $httpClient = new MockHttpClient($this->invalidMockResponse());
        $veraApi = new PDFAValidationAPI('http://localhost/', $httpClient);

        $this->assertNotNull($veraApi);

        // Parameters in this call do not matter, always get the $validMockResponse.
        // This tests that an invalid PDF is processed correctly.
        $result = $veraApi->validate('', 'test-010.txt', 0, sha1(''), '{"flavour": "unit_test"}', 'text/plain');

        $this->assertEquals($result->message, 'PDF file is not compliant with Validation Profile requirements.');
    }

    public function testNotFound(): void
    {
        $httpClient = new MockHttpClient($this->errorMockResponse());
        $veraApi = new PDFAValidationAPI('http://localhost/', $httpClient);

        $this->assertNotNull($veraApi);

        // Parameters in this call do not matter, always get the $validMockResponse.
        // This tests that a network error is processed correctly.
        $result = $veraApi->validate('', 'test-010.txt', 0, sha1(''), '{"flavour": "unit_test"}', 'text/plain');

        $this->assertEquals($result->message, 'Network Error');
    }

    private function validMockResponse(): MockResponse
    {
        return new MockResponse(<<<EOT
            {"report":{"buildInformation":{"releaseDetails":[{
              "id" : "core",
              "version" : "1.26.1",
              "buildDate" : 1715877000000
            },{
              "id" : "verapdf-rest",
              "version" : "1.26.1",
              "buildDate" : 1716563520000
            },{
              "id" : "validation-model",
              "version" : "1.26.1",
              "buildDate" : 1715883120000
            }]},"jobs":[{"itemDetails":{
              "name" : "example_065.pdf",
              "size" : 150068
            },"validationResult":{
              "details" : {
                "passedRules" : 128,
                "failedRules" : 0,
                "passedChecks" : 3306,
                "failedChecks" : 0,
                "ruleSummaries" : [ ]
              },
              "jobEndStatus" : "normal",
              "profileName" : "PDF/A-1B validation profile",
              "statement" : "PDF file is compliant with Validation Profile requirements.",
              "compliant" : true
            },"processingTime":{
              "start" : 1730899811058,
              "finish" : 1730899811896,
              "duration" : "00:00:00.838",
              "difference" : 838
            }}],"batchSummary":{
              "duration" : {
                "start" : 1730899810991,
                "finish" : 1730899811903,
                "duration" : "00:00:00.912",
                "difference" : 912
              },
              "totalJobs" : 1,
              "outOfMemory" : 0,
              "veraExceptions" : 0,
              "validationSummary" : {
                "failedJobCount" : 0,
                "totalJobCount" : 1,
                "compliantPdfaCount" : 1,
                "nonCompliantPdfaCount" : 0,
                "successfulJobCount" : 1
              },
              "featuresSummary" : {
                "failedJobCount" : 0,
                "totalJobCount" : 0,
                "successfulJobCount" : 0
              },
              "repairSummary" : {
                "failedJobCount" : 0,
                "totalJobCount" : 0,
                "successfulJobCount" : 0
              },
              "multiJob" : false,
              "failedParsingJobs" : 0,
              "failedEncryptedJobs" : 0
            }}}
            EOT);
    }

    private function invalidMockResponse(): MockResponse
    {
        return new MockResponse(<<<EOT
            {"report":{"buildInformation":{"releaseDetails":[{
              "id" : "core",
              "version" : "1.26.1",
              "buildDate" : 1715877000000
            },{
              "id" : "verapdf-rest",
              "version" : "1.26.1",
              "buildDate" : 1716563520000
            },{
              "id" : "validation-model",
              "version" : "1.26.1",
              "buildDate" : 1715883120000
            }]},"jobs":[{"itemDetails":{
              "name" : "Testbrief-Danilo_Neuber-20240422.pdf",
              "size" : 99972
            },"validationResult":{
              "details" : {
                "passedRules" : 127,
                "failedRules" : 1,
                "passedChecks" : 869,
                "failedChecks" : 1,
                "ruleSummaries" : [ {
                  "ruleStatus" : "FAILED",
                  "specification" : "ISO 19005-1:2005",
                  "clause" : "6.7.3",
                  "testNumber" : 1,
                  "status" : "failed",
                  "failedChecks" : 1,
                  "description" : "The value of CreationDate entry from the document information dictionary, if present, and its analogous XMP property \"xmp:CreateDate\" shall be equivalent",
                  "object" : "CosInfo",
                  "test" : "doCreationDatesMatch != false",
                  "checks" : [ {
                    "status" : "failed",
                    "context" : "root/trailer[0]/Info[0]",
                    "errorArguments" : [ ]
                  } ]
                } ]
              },
              "jobEndStatus" : "normal",
              "profileName" : "PDF/A-1B validation profile",
              "statement" : "PDF file is not compliant with Validation Profile requirements.",
              "compliant" : false
            },"processingTime":{
              "start" : 1730900541350,
              "finish" : 1730900541419,
              "duration" : "00:00:00.069",
              "difference" : 69
            }}],"batchSummary":{
              "duration" : {
                "start" : 1730900541346,
                "finish" : 1730900541423,
                "duration" : "00:00:00.077",
                "difference" : 77
              },
              "totalJobs" : 1,
              "outOfMemory" : 0,
              "veraExceptions" : 0,
              "validationSummary" : {
                "failedJobCount" : 0,
                "totalJobCount" : 1,
                "compliantPdfaCount" : 0,
                "nonCompliantPdfaCount" : 1,
                "successfulJobCount" : 1
              },
              "featuresSummary" : {
                "failedJobCount" : 0,
                "totalJobCount" : 0,
                "successfulJobCount" : 0
              },
              "repairSummary" : {
                "failedJobCount" : 0,
                "totalJobCount" : 0,
                "successfulJobCount" : 0
              },
              "multiJob" : false,
              "failedParsingJobs" : 0,
              "failedEncryptedJobs" : 0
            }}}
            EOT);
    }

    private function errorMockResponse(): MockResponse
    {
        return new MockResponse('', ['http_code' => 404]);
    }
}

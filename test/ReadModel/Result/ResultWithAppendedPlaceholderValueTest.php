<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\ReadModel\Result;

use Exception;
use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\ReadModel\Result\Found;
use WrongAboutEverything\RadixTree\ReadModel\Result\NonFound;
use WrongAboutEverything\RadixTree\ReadModel\Result\Result;
use WrongAboutEverything\RadixTree\ReadModel\Result\ResultWithAppendedPlaceholderValue;

class ResultWithAppendedPlaceholderValueTest extends TestCase
{
    public function testWithFoundResult()
    {
        $result =
            new ResultWithAppendedPlaceholderValue(
                'vasya',
                new Found(['fedya'], 1)
            );

        $this->assertTrue($result->isFound());
        $this->assertEquals(['vasya', 'fedya'], $result->values());
        $this->assertEquals(1, $result->nodeId());
    }

    public function testWithNonFoundResult()
    {
        $result =
            new ResultWithAppendedPlaceholderValue(
                'vasya',
                new NonFound()
            );

        $this->assertFalse($result->isFound());
        $this->whenResultIsNotFoundAndValuesAreAskedThenExceptionIsThrown($result);
        $this->whenResultIsNotFoundAndNodeIdIsAskedThenExceptionIsThrown($result);
    }

    private function whenResultIsNotFoundAndValuesAreAskedThenExceptionIsThrown(Result $result)
    {
        try {
            $result->values();
        } catch (Exception $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail('An exception should have been thrown');
    }

    private function whenResultIsNotFoundAndNodeIdIsAskedThenExceptionIsThrown(Result $result)
    {
        try {
            $result->nodeId();
        } catch (Exception $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail('An exception should have been thrown');
    }
}
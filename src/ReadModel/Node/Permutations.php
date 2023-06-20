<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

class Permutations
{
    private $input;

    public function __construct(array $input)
    {
        $this->input = $input;
    }

    public function value(): array
    {
        return $this->doValue($this->input);
    }

    private function doValue(array $input): array
    {
        if (count($input) === 2) {
            return [[$input[0], $input[1]], [$input[1], $input[0]]];
        }
        $result = [];
        foreach ($input as $key => $value) {
            $nextInput = $input;
            $newFirstElement = $nextInput[$key];
            unset($nextInput[$key]);
            $result =
                array_merge(
                    $result,
                    array_reduce(
                        $this->doValue(array_values($nextInput)),
                        function (array $acc, array $currentArray) use ($newFirstElement) {
                            $acc[] = array_merge([$newFirstElement], $currentArray);
                            return $acc;
                        },
                        []
                    )
                );
        }

        return $result;
    }
}
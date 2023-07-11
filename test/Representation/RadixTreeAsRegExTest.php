<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\Representation;

use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\Generation\DataItem;
use WrongAboutEverything\RadixTree\Generation\RadixTreeGenerator;
use WrongAboutEverything\RadixTree\Representation\RadixTreeAsRegEx;

class RadixTreeAsRegExTest extends TestCase
{
    public function testKeysMatchRegEx()
    {
        $dataItems = $this->dataItems();

        $regEx = (new RadixTreeAsRegEx((new RadixTreeGenerator($dataItems))->value(), false))->value();

        $this->assertEquals('#^(?|/vasil(?|e(?|/(?|(?|antoine/vakhlakov(*:62))|([^/]+)(*:68)(?|(?|/vakhlakov(*:39)))?)|v(*:63)(?|(?|/(?|(?|baton/(?|(?|vakhlakov/(?|(?|vakhlakov(*:13)(?|(?|/(?|(?|vakhlakov(*:15)(?|(?|/(?|(?|buldakov(*:16))|([^/]+)(*:17))))?)|([^/]+)(*:14))))?)|([^/]+)(*:8)(?|(?|/(?|(?|vakhlakov(*:10)(?|(?|/(?|(?|buldakov(*:11))|([^/]+)(*:12))))?)|([^/]+)(*:9))))?))|([^/]+)(?|/(?|(?|vakhlakov(*:0)(?|(?|/(?|(?|vakhlakov(*:7))|([^/]+)(*:6))))?)|([^/]+)(*:1)(?|(?|/(?|(?|vakhlakov(*:3)(?|(?|/(?|(?|buldakov(*:4))|([^/]+)(*:5))))?)|([^/]+)(*:2))))?)))|makarev(*:41)(?|(?|/([^/]+)(?|/([^/]+)(*:42))|i(*:48)(?|(?|/([^/]+)(?|/([^/]+)(*:38))|ch(*:64)(?|(?|/vasilev(*:65)(?|(?|/makarevich(*:66)(?|(?|/vasilev(*:67)))?))?))?))?))?|anton(*:60)(?|(?|/([^/]+)(*:59)(?|(?|/petrovich(*:58)(?|(?|/([^/]+)(*:57)(?|(?|/vasilyev(*:56)))?))?))?))?)|([^/]+)(*:51)(?|(?|/(?|(?|va(?|khlakov/(?|(?|vakhlakov(*:22)(?|(?|/(?|(?|vakhlakov(*:19)(?|(?|/(?|(?|buldakov(*:20))|([^/]+)(*:21))))?)|([^/]+)(*:27))))?)|([^/]+)(*:23)(?|(?|/(?|(?|vakhlakov(*:24)(?|(?|/(?|(?|buldakov(*:25))|([^/]+)(*:26))))?)|([^/]+)(*:18))))?)|sily/(?|(?|belov(*:44)(?|(?|/(?|(?|htonc(*:47))|([^/]+)(*:45))))?)|([^/]+)(*:43)(?|(?|/htonc(*:46)))?))|anton(*:52)(?|(?|/([^/]+)(*:53)(?|(?|/anton(*:54)(?|(?|/([^/]+)(*:55)))?))?))?)|([^/]+)(?|/(?|(?|vakhlakov(*:33)(?|(?|/(?|(?|vakhlakov(*:35)(?|(?|/(?|(?|buldakov(*:36))|([^/]+)(*:37))))?)|([^/]+)(*:34))))?)|([^/]+)(*:28)(?|(?|/(?|(?|vakhlakov(*:30)(?|(?|/(?|(?|buldakov(*:31))|([^/]+)(*:32))))?)|([^/]+)(*:29))))?)))))?)|s/([^/]+)(?|/tabakovs/([^/]+)(*:49))|ich/([^/]+)(?|/([^/]+)(?|/belov(*:61)))))?)|y/(?|(?|belov(*:50))|([^/]+)(*:40))))$#', $regEx);
        $this->lookUpExistingKeysSuccessfully($dataItems, $regEx);
    }

    public function testRandomKeysDontMatchRegEx()
    {
        $regEx = (new RadixTreeAsRegEx((new RadixTreeGenerator($this->dataItems()))->value(), false))->value();

        $this->assertEquals('#^(?|/vasil(?|e(?|/(?|(?|antoine/vakhlakov(*:62))|([^/]+)(*:68)(?|(?|/vakhlakov(*:39)))?)|v(*:63)(?|(?|/(?|(?|baton/(?|(?|vakhlakov/(?|(?|vakhlakov(*:13)(?|(?|/(?|(?|vakhlakov(*:15)(?|(?|/(?|(?|buldakov(*:16))|([^/]+)(*:17))))?)|([^/]+)(*:14))))?)|([^/]+)(*:8)(?|(?|/(?|(?|vakhlakov(*:10)(?|(?|/(?|(?|buldakov(*:11))|([^/]+)(*:12))))?)|([^/]+)(*:9))))?))|([^/]+)(?|/(?|(?|vakhlakov(*:0)(?|(?|/(?|(?|vakhlakov(*:7))|([^/]+)(*:6))))?)|([^/]+)(*:1)(?|(?|/(?|(?|vakhlakov(*:3)(?|(?|/(?|(?|buldakov(*:4))|([^/]+)(*:5))))?)|([^/]+)(*:2))))?)))|makarev(*:41)(?|(?|/([^/]+)(?|/([^/]+)(*:42))|i(*:48)(?|(?|/([^/]+)(?|/([^/]+)(*:38))|ch(*:64)(?|(?|/vasilev(*:65)(?|(?|/makarevich(*:66)(?|(?|/vasilev(*:67)))?))?))?))?))?|anton(*:60)(?|(?|/([^/]+)(*:59)(?|(?|/petrovich(*:58)(?|(?|/([^/]+)(*:57)(?|(?|/vasilyev(*:56)))?))?))?))?)|([^/]+)(*:51)(?|(?|/(?|(?|va(?|khlakov/(?|(?|vakhlakov(*:22)(?|(?|/(?|(?|vakhlakov(*:19)(?|(?|/(?|(?|buldakov(*:20))|([^/]+)(*:21))))?)|([^/]+)(*:27))))?)|([^/]+)(*:23)(?|(?|/(?|(?|vakhlakov(*:24)(?|(?|/(?|(?|buldakov(*:25))|([^/]+)(*:26))))?)|([^/]+)(*:18))))?)|sily/(?|(?|belov(*:44)(?|(?|/(?|(?|htonc(*:47))|([^/]+)(*:45))))?)|([^/]+)(*:43)(?|(?|/htonc(*:46)))?))|anton(*:52)(?|(?|/([^/]+)(*:53)(?|(?|/anton(*:54)(?|(?|/([^/]+)(*:55)))?))?))?)|([^/]+)(?|/(?|(?|vakhlakov(*:33)(?|(?|/(?|(?|vakhlakov(*:35)(?|(?|/(?|(?|buldakov(*:36))|([^/]+)(*:37))))?)|([^/]+)(*:34))))?)|([^/]+)(*:28)(?|(?|/(?|(?|vakhlakov(*:30)(?|(?|/(?|(?|buldakov(*:31))|([^/]+)(*:32))))?)|([^/]+)(*:29))))?)))))?)|s/([^/]+)(?|/tabakovs/([^/]+)(*:49))|ich/([^/]+)(?|/([^/]+)(?|/belov(*:61)))))?)|y/(?|(?|belov(*:50))|([^/]+)(*:40))))$#', $regEx);
        $this->lookUpRandomNonExistingKeysAndFindNothing($this->dataItems(), $regEx);
    }

    public function testExistingKeysWithSuffixesAndPrefixesDontMatchRegEx()
    {
        $regEx =
            (new RadixTreeAsRegEx(
                (new RadixTreeGenerator(
                    [$this->dataItem('/vasilev/baton/:vakhlakov/vakhlakov', 0)]
                ))
                    ->value(),
                false
            ))
                ->value();

        $this->assertEquals('#^(?|/vasilev/baton/([^/]+)(?|/vakhlakov(*:0)))$#', $regEx);
        $this->lookUpExistingKeysWithRandomPrefixesAndFindNothing($this->dataItems(), $regEx);
        $this->lookUpExistingKeysWithSuffixesAndFindNothing($this->dataItems(), $regEx);
    }

    private function dataItems(): array
    {
        return [
            $this->dataItem('/vasilev/baton/:vakhlakov/vakhlakov', 0),
            $this->dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/:vakhlakov', 2),
            $this->dataItem('/vasilev/baton/:vakhlakov/:vakhlakov', 1),
            $this->dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/vakhlakov', 3),
            $this->dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/vakhlakov/buldakov', 4),
            $this->dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/vakhlakov/:buldakov', 5),
            $this->dataItem('/vasilev/baton/:vakhlakov/vakhlakov/:vakhlakov', 6),
            $this->dataItem('/vasilev/baton/:vakhlakov/vakhlakov/vakhlakov', 7),
            $this->dataItem('/vasilev/baton/vakhlakov/:vakhlakov', 8),
            $this->dataItem('/vasilev/baton/vakhlakov/:vakhlakov/:vakhlakov', 9),
            $this->dataItem('/vasilev/baton/vakhlakov/:vakhlakov/vakhlakov', 10),
            $this->dataItem('/vasilev/baton/vakhlakov/:vakhlakov/vakhlakov/buldakov', 11),
            $this->dataItem('/vasilev/baton/vakhlakov/:vakhlakov/vakhlakov/:buldakov', 12),
            $this->dataItem('/vasilev/baton/vakhlakov/vakhlakov', 13),
            $this->dataItem('/vasilev/baton/vakhlakov/vakhlakov/:vakhlakov', 14),
            $this->dataItem('/vasilev/baton/vakhlakov/vakhlakov/vakhlakov', 15),
            $this->dataItem('/vasilev/baton/vakhlakov/vakhlakov/vakhlakov/buldakov', 16),
            $this->dataItem('/vasilev/baton/vakhlakov/vakhlakov/vakhlakov/:buldakov', 17),
            $this->dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/:vakhlakov', 18),
            $this->dataItem('/vasilev/:baton/vakhlakov/vakhlakov/vakhlakov', 19),
            $this->dataItem('/vasilev/:baton/vakhlakov/vakhlakov/vakhlakov/buldakov', 20),
            $this->dataItem('/vasilev/:baton/vakhlakov/vakhlakov/vakhlakov/:buldakov', 21),
            $this->dataItem('/vasilev/:baton/vakhlakov/vakhlakov', 22),
            $this->dataItem('/vasilev/:baton/vakhlakov/:vakhlakov', 23),
            $this->dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/vakhlakov', 24),
            $this->dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/vakhlakov/buldakov', 25),
            $this->dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/vakhlakov/:buldakov', 26),
            $this->dataItem('/vasilev/:baton/vakhlakov/vakhlakov/:vakhlakov', 27),
            $this->dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov', 28),
            $this->dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/:vakhlakov', 29),
            $this->dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/vakhlakov', 30),
            $this->dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/vakhlakov/buldakov', 31),
            $this->dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/vakhlakov/:buldakov', 32),
            $this->dataItem('/vasilev/:baton/:vakhlakov/vakhlakov', 33),
            $this->dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/:vakhlakov', 34),
            $this->dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/vakhlakov', 35),
            $this->dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/vakhlakov/buldakov', 36),
            $this->dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/vakhlakov/:buldakov', 37),
            $this->dataItem('/vasilev/makarevi/:vasily/:belov', 38),
            $this->dataItem('/vasile/:anton/vakhlakov', 39),
            $this->dataItem('/vasily/:lebov', 40),
            $this->dataItem('/vasilev/makarev', 41),
            $this->dataItem('/vasilev/makarev/:vasily/:belov', 42),
            $this->dataItem('/vasilev/:anton/vasily/:belov', 43),
            $this->dataItem('/vasilev/:anton/vasily/belov', 44),
            $this->dataItem('/vasilev/:anton/vasily/belov/:htonc', 45),
            $this->dataItem('/vasilev/:anton/vasily/:belov/htonc', 46),
            $this->dataItem('/vasilev/:anton/vasily/belov/htonc', 47),
            $this->dataItem('/vasilev/makarevi', 48),
            $this->dataItem('/vasilevs/:antons/tabakovs/:matskyavichus', 49),
            $this->dataItem('/vasily/belov', 50),
            $this->dataItem('/vasilev/:anton', 51),
            $this->dataItem('/vasilev/:anton/anton', 52),
            $this->dataItem('/vasilev/:anton/anton/:petrovich', 53),
            $this->dataItem('/vasilev/:anton/anton/:petrovich/anton', 54),
            $this->dataItem('/vasilev/:anton/anton/:petrovich/anton/:vasilyev', 55),
            $this->dataItem('/vasilev/anton/:anton/petrovich/:anton/vasilyev', 56),
            $this->dataItem('/vasilev/anton/:anton/petrovich/:anton', 57),
            $this->dataItem('/vasilev/anton/:anton/petrovich', 58),
            $this->dataItem('/vasilev/anton/:anton', 59),
            $this->dataItem('/vasilev/anton', 60),
            $this->dataItem('/vasilevich/:makarevich/:vasily/belov', 61),
            $this->dataItem('/vasile/antoine/vakhlakov', 62),
            $this->dataItem('/vasilev', 63),
            $this->dataItem('/vasilev/makarevich', 64),
            $this->dataItem('/vasilev/makarevich/vasilev', 65),
            $this->dataItem('/vasilev/makarevich/vasilev/makarevich', 66),
            $this->dataItem('/vasilev/makarevich/vasilev/makarevich/vasilev', 67),
            $this->dataItem('/vasile/:anton', 68),
        ];
    }

    /**
     * @param DataItem[] $dataItems
     * @param string $regEx
     */
    private function lookUpExistingKeysSuccessfully(array $dataItems, string $regEx)
    {
        foreach ($dataItems as $dataItem) {
            $queryStringAndGeneratedPlaceholders = $this->queryStringAndGeneratedPlaceholders($dataItem->key());
            $matches = [];
            $result = preg_match($regEx, $queryStringAndGeneratedPlaceholders[0], $matches);
            $this->assertEquals(1, $result);
            unset($matches[0]);
            unset($matches['MARK']);
            $this->assertEquals($queryStringAndGeneratedPlaceholders[1], array_values($matches));
        }
    }

    private function lookUpRandomNonExistingKeysAndFindNothing(array $dataItems, string $regEx)
    {
        for ($i = 0; $i < count($dataItems); $i++) {
            $randomString = $this->randomString(60);
            $result = preg_match($regEx, $randomString);
            $this->assertEquals(0, $result);
        }
    }

    private function lookUpExistingKeysWithRandomPrefixesAndFindNothing(array $dataItems, string $regEx)
    {
        foreach ($dataItems as $dataItem) {
            $queryStringAndGeneratedPlaceholders = $this->queryStringAndGeneratedPlaceholders($dataItem->key());
            $result = preg_match($regEx, $this->randomString(5) . $queryStringAndGeneratedPlaceholders[0]);
            $this->assertEquals(0, $result);
        }
    }

    private function lookUpExistingKeysWithSuffixesAndFindNothing(array $dataItems, string $regEx)
    {
        foreach ($dataItems as $dataItem) {
            $queryStringAndGeneratedPlaceholders = $this->queryStringAndGeneratedPlaceholders($dataItem->key());
            $r = $this->randomString(5);
            $result = preg_match($regEx, $queryStringAndGeneratedPlaceholders[0] . '/' . $r);
            if ($result === 1) {
                var_dump($queryStringAndGeneratedPlaceholders[0] . '/' . $r);
            }
            $this->assertEquals(0, $result);
        }
    }

    private function queryStringAndGeneratedPlaceholders(string $key): array
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $generatedPlaceholders = [];
        $queryParts = array_filter(explode('/', $key));
        foreach ($queryParts as &$part) {
            if ($part[0] === ':') {
                $randomValue = substr(str_shuffle($characters), 0, mt_rand(1, 10));
                $generatedPlaceholders[] = $randomValue;
                $part = $randomValue;
            }
        }

        return ['/' . implode('/', $queryParts), $generatedPlaceholders];
    }

    private function randomString(int $maxLength): string
    {
        $characters = 'abcdef/ghijklm/nopqrstu/vwxyzAB/CDEFGHI/JKLMNOPQ/RSTUVW/XYZ0123/456789';
        return substr(str_shuffle($characters), 0, mt_rand(1, $maxLength));
    }

    private function dataItem(string $path, int $index): DataItem
    {
        return new DataItem($path, $index);
    }
}
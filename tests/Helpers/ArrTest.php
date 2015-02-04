<?php

class Test_Helpers_Arr extends \TestCase
{
    protected function get_source_array(array $nested = null)
    {
        $source = [
            'key' => 'value',
            'number' => 132345,
            'mbstring' => 'Тест на русском языке'
        ];

        if (is_array($nested)) {
            $source['nested'] = $nested;
        }

        return $source;
    }

    /**
     * @return array
     */
    protected function getSources()
    {
        return [
            [
                'id' => 1,
                'name' => 'One',
                'category' => 'Test'
            ],
            [
                'id' => 2,
                'name' => 'Two',
                'category' => 'Test'
            ],
            [
                'id' => 3,
                'name' => 'Three',
                'category' => 'Stage'
            ],
            [
                'id' => 4,
                'name' => 'Four',
                'category' => 'Stash'
            ],
            [
                'id' => 5,
                'name' => 'Five',
                'category' => 'Stash'
            ],
            [
                'id' => 6,
                'name' => 'Six',
                'category' => 'Test'
            ]
        ];
    }

    public function testSimilar()
    {
        $nested = [
            'key' => 'value'
        ];

        $source = $this->get_source_array($nested);

        // сверяем два одинаковых массива
        $result = \Cloudstash\Helper\Arr::similar($source, $source);
        $this->assertTrue($result, 'Not similar to equals arrays');

        // сверяем два различных массива
        $result = \Cloudstash\Helper\Arr::similar($source, $nested);
        $this->assertFalse($result, 'Similar to NOT equals arrays');

        $source = ['test', 123, 123.321, true];
        $matcher = ['test', '123', '123.321', 'true'];

        // сверяем с явным указанием типов
        $result = \Cloudstash\Helper\Arr::similar($source, $source, true);
        $this->assertTrue($result, 'Similar to NOT equals arrays (STRICT mode)');

        $result = \Cloudstash\Helper\Arr::similar($source, $matcher, true);
        $this->assertFalse($result, 'Similar are equals arrays (STRICT mode)');

        // сверяем с неявным указанием типов
        $result = \Cloudstash\Helper\Arr::similar($source, $source, false);
        $this->assertTrue($result, 'Similar to NOT equals arrays (NOT STRICT mode)');

        $result = \Cloudstash\Helper\Arr::similar($source, $matcher, false);
        $this->assertTrue($result, 'Similar are equals arrays (NOT STRICT mode)');
    }

    public function testGet()
    {
        $nested = [
            'key' => 'value'
        ];

        $source = $this->get_source_array($nested);

        // получаем по ключу значение (строка)
        $result = \Cloudstash\Helper\Arr::get($source, 'key');
        $this->assertEquals($result, 'value', 'Wrong value for key "key"');

        // получаем по ключу значение (число)
        $result = \Cloudstash\Helper\Arr::get($source, 'number');
        $this->assertEquals($result, 132345, 'Wrong value for key "number"');

        // получаем по ключу значение (многобайтовая строка)
        $result = \Cloudstash\Helper\Arr::get($source, 'mbstring');
        $this->assertEquals($result, 'Тест на русском языке', 'Wrong value for key "mbstring"');

        // при отсутствующем ключе, должен возвращаться null
        $result = \Cloudstash\Helper\Arr::get($source, 'undefined');
        $this->assertEquals($result, null, 'Wrong default value (null)');

        // при отсутствующем ключе, должено вернуться объявленное значение по умолчанию (число)
        $result = \Cloudstash\Helper\Arr::get($source, 'undefined', 1000);
        $this->assertEquals($result, 1000, 'Wrong default value (number)');

        // получаем по ключу вложеный массив
        $result = \Cloudstash\Helper\Arr::get($source, 'nested');
        $result = \Cloudstash\Helper\Arr::similar($result, $nested);
        $this->assertTrue($result, 'Wrong nested array');
    }

    public function testGetByIndex()
    {
        $nested = [
            'key' => 'value'
        ];

        $source = $this->get_source_array($nested);

        // получаем по индексу значение (строка)
        $result = \Cloudstash\Helper\Arr::getByIndex($source, 0);
        $this->assertEquals($result, 'value', 'Wrong value for index 0');

        // получаем по индексу значение (число)
        $result = \Cloudstash\Helper\Arr::getByIndex($source, 1);
        $this->assertEquals($result, 132345, 'Wrong value for index 1');

        // получаем по индексу значение (многобайтовая строка)
        $result = \Cloudstash\Helper\Arr::getByIndex($source, 2);
        $this->assertEquals($result, 'Тест на русском языке', 'Wrong value for index 2');

        // получаем по индексу вложеный массив
        $result = \Cloudstash\Helper\Arr::getByIndex($source, 3);
        $result = \Cloudstash\Helper\Arr::similar($result, $nested);
        $this->assertTrue($result, 'Wrong nested array');

        // при отсутствующем индексе, должен возвращаться null
        $result = \Cloudstash\Helper\Arr::getByIndex($source, 99);
        $this->assertEquals($result, null, 'Wrong default value (null)');

        // при отсутствующем идексе, должено вернуться объявленное значение по умолчанию (число)
        $result = \Cloudstash\Helper\Arr::getByIndex($source, 99, 1000);
        $this->assertEquals($result, 1000, 'Wrong default value (number)');
    }

    public function testGetFirst()
    {
        $source = [
            'first' => 'first value',
            'last' => 'last value'
        ];

        $result = \Cloudstash\Helper\Arr::getFirst($source);
        $this->assertEquals($result, 'first value', 'Wrong value for first element');

        $result = \Cloudstash\Helper\Arr::getFirst([], 1000);
        $this->assertEquals($result, 1000, 'Wrong value for default value, if array is empty');
    }

    public function testGetLast()
    {
        $source = [
            'first' => 'first value',
            'last' => 'last value'
        ];

        $result = \Cloudstash\Helper\Arr::getLast($source);
        $this->assertEquals($result, 'last value', 'Wrong value for last element');

        $result = \Cloudstash\Helper\Arr::getLast([], 1000);
        $this->assertEquals($result, 1000, 'Wrong value for default value, if array is empty');
    }

    public function testGrouping()
    {
        $result = \Cloudstash\Helper\Arr::grouping($this->getSources(), ['name']);
        $this->assertTrue($result == [
                'One' => [
                    [
                        'id' => 1,
                        'name' => 'One',
                        'category' => 'Test'
                    ]
                ],
                'Two' => [
                    [
                        'id' => 2,
                        'name' => 'Two',
                        'category' => 'Test'
                    ]
                ],
                'Three' => [
                    [
                        'id' => 3,
                        'name' => 'Three',
                        'category' => 'Stage'
                    ]
                ],
                'Four' => [
                    [
                        'id' => 4,
                        'name' => 'Four',
                        'category' => 'Stash'
                    ]
                ],
                'Five' => [
                    [
                        'id' => 5,
                        'name' => 'Five',
                        'category' => 'Stash'
                    ]
                ],
                'Six' => [
                    [
                        'id' => 6,
                        'name' => 'Six',
                        'category' => 'Test'
                    ]
                ]
            ], 'Bad assertion with disabled put_in_single option (group [name])');

        $result = \Cloudstash\Helper\Arr::grouping($this->getSources(), ['name'], true);
        $this->assertTrue($result == [
                'One' => [
                    'id' => 1,
                    'name' => 'One',
                    'category' => 'Test'
                ],
                'Two' => [
                    'id' => 2,
                    'name' => 'Two',
                    'category' => 'Test'
                ],
                'Three' => [
                    'id' => 3,
                    'name' => 'Three',
                    'category' => 'Stage'
                ],
                'Four' => [
                    'id' => 4,
                    'name' => 'Four',
                    'category' => 'Stash'
                ],
                'Five' => [
                    'id' => 5,
                    'name' => 'Five',
                    'category' => 'Stash'
                ],
                'Six' => [
                    'id' => 6,
                    'name' => 'Six',
                    'category' => 'Test'
                ]
            ], 'Bad assertion with put_in_single option (group [name])');

        $result = \Cloudstash\Helper\Arr::grouping($this->getSources(), ['category']);
        $this->assertTrue($result == [
                'Test' => [
                    [
                        'id' => 1,
                        'name' => 'One',
                        'category' => 'Test'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Two',
                        'category' => 'Test'
                    ],
                    [
                        'id' => 6,
                        'name' => 'Six',
                        'category' => 'Test'
                    ]
                ],
                'Stage' => [
                    [
                        'id' => 3,
                        'name' => 'Three',
                        'category' => 'Stage'
                    ]
                ],
                'Stash' => [
                    [
                        'id' => 4,
                        'name' => 'Four',
                        'category' => 'Stash'
                    ],
                    [
                        'id' => 5,
                        'name' => 'Five',
                        'category' => 'Stash'
                    ],
                ]
            ], 'Bad assertion with disabled put_in_single option (group [category])');

        $result = \Cloudstash\Helper\Arr::grouping($this->getSources(), ['category', 'name'], true);
        $this->assertTrue($result == [
                'Test' => [
                    'One' => [
                        'id' => 1,
                        'name' => 'One',
                        'category' => 'Test'
                    ],
                    'Two' => [
                        'id' => 2,
                        'name' => 'Two',
                        'category' => 'Test'
                    ],
                    'Six' => [
                        'id' => 6,
                        'name' => 'Six',
                        'category' => 'Test'
                    ]
                ],
                'Stage' => [
                    'Three' => [
                        'id' => 3,
                        'name' => 'Three',
                        'category' => 'Stage'
                    ]
                ],
                'Stash' => [
                    'Four' => [
                        'id' => 4,
                        'name' => 'Four',
                        'category' => 'Stash'
                    ],
                    'Five' => [
                        'id' => 5,
                        'name' => 'Five',
                        'category' => 'Stash'
                    ],
                ]
            ], 'Bad assertion with put_in_single option (group [category, name])');
    }

    public function testFlattenToTree()
    {
        $source = [
            3944 => 'VI/Авто',
            3945 => 'VI/Авто/Отечественные авто',
            3946 => 'VI/Авто/Иномарки',
            3947 => 'VI/Рынок/Фондовый',
            3948 => 'AiData/Магазин/Книги',
            3949 => 'AiData/Магазин/Колёса,Шины'
        ];

        $tree = \Cloudstash\Helper\Arr::flattenToTree($source, '/');

        $wait = [
            'VI' => [
                '__tree' => [
                    'Авто' => [
                        '__value' => 3944,
                        '__tree' => [
                            'Отечественные авто' => [
                                '__value' => 3945
                            ],
                            'Иномарки' => [
                                '__value' => 3946
                            ],
                        ]
                    ],
                    'Рынок' => [
                        '__tree' => [
                            'Фондовый' => [
                                '__value' => 3947
                            ],
                        ]
                    ]
                ]
            ],
            'AiData' => [
                '__tree' => [
                    'Магазин' => [
                        '__tree' => [
                            'Книги' => [
                                '__value' => 3948
                            ],
                            'Колёса,Шины' => [
                                '__value' => 3949
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $this->assertTrue($tree == $wait, 'Bad flatten to tree');
    }
}
<?php

namespace App\Service;

use App\Entity\Attribute;
use App\Entity\Group;
use App\Repository\AttributesRepository;
use App\Repository\GroupRepository;
use App\Repository\ProductRepository;
use Faker\Generator;

class DataGenerator
{
    /** @description товаров в группе min */
    private const GROUP_MIN_CAPACITY = 99;
    /** @description товаров в группе max */
    private const GROUP_MAX_CAPACITY = 999;
    /** @description атрибутов у товара min */
    private const ATTR_IN_USE_MIN = 10;
    /** @description атрибутов у товара max */
    private const ATTR_IN_USE_MAX = 20;
    /** @description разных значений одного атрибута min */
    private const ATTR_VALUES_MIN_CAPACITY = 5 /*500*/;
    /** @description разных значений одного атрибута max */
    private const ATTR_VALUES_MAX_CAPACITY = 10/*1000*/;
    /** @description разных атрибутов в группу min */
    private const ATTR_CAPACITY_MIN = 10;
    /** @description разных атрибутов в группу max */
    private const ATTR_CAPACITY_MAX = 20;




    private const DELIVERY_VALUES = ["1 День", "2 Дня", "До 3 Деней", "До 5 Дней",];
    /**  ['id' => ["name"=>"", "attributes" => [id=> name=> values=>[] ]]] */
    private array $groups = [];

    private readonly Generator $faker;

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly AttributesRepository $attributesRepository,
        private readonly GroupRepository $groupRepository
    ) {
        $this->faker = \Faker\Factory::create();
    }

    public function generate(): void
    {
        $groups = $this->createGroups();
        $commonAttributes = $this->createCommonAttributes();
        foreach ($groups as $group) {
            $attributes = $this->createAttributes();
            $this->mapGroupAttribute($group, $commonAttributes, $attributes);
        }
        print_r(json_encode($this->groups, JSON_THROW_ON_ERROR));
    }

//    private function getCommonAttributes()
//    {
//    }

    /**
     * @return list<string>
     */
    private function getDefaultGroupNames(): array
    {
        return [
             'Автоэлектроника и навигация',
            'Гарнитуры и наушники',
            'Детская электроника',
            'Игровые консоли и игры',
            'Кабели и зарядные устройства',
            'Музыка и видео',
            'Ноутбуки и компьютеры',
            'Офисная техника',
            'Развлечения и гаджеты',
            'Сетевое оборудование',
            'Системы безопасности',
            'Смартфоны и телефоны',
            'часы и браслеты',
            'Видео техника',
            'Торговое оборудование',
            'Умный дом',
            'Электротранспорт и аксессуары',
        ];
    }

    /**
     * @return array<string, Closure(Generator):string>
     */
    private function getDefaultCommonAttributes(): array
    {
        return [
            "Бренд" => fn(Generator $f): string => $f->company(),
            "Продавец" => fn(Generator $f): string => $f->firstName(),
            "Цвет" => fn(Generator $f): string => $f->colorName(),
        ];
    }

    /**
     * @return list<Group>
     */
    private function createGroups(): array
    {
        $groups = [];
        foreach ($this->getDefaultGroupNames() as $name) {
            $groups[] = new Group(null, $name);
        }
        return $this->groupRepository->insertMany($groups);
    }

    /**
     * @return list<Attribute>
     */
    private function createCommonAttributes(): array
    {
        $attr = [];
        foreach ($this->getDefaultCommonAttributes() as $name => $_) {
            $attr[] = new Attribute(null, $name, '');
        }
        return $this->attributesRepository->insertMany($attr);
    }

    /**
     * @param list<Attribute> $commonAttributes
     * @param list<Attribute> $attributes
     */
    private function mapGroupAttribute(Group $group, array $commonAttributes, array $attributes): void
    {
        $commonResult = array_reduce(
            $commonAttributes,
            function (array $acc, Attribute $attr): array {
                $acc[$attr->getId() ?? throw new \Exception("attribute id must to be")] = [
                    'name'=> $attr->getName(),
                    'values' => $this->generateValuesByGenerator(
                        $this->getDefaultCommonAttributes()[$attr->getName()]
                            ?? throw new \Exception('unexpected no callable generator')
                    )
                ];
                return $acc;
            },
            []
        );

        $this->groups[$group->getId() ?? throw new \Exception("group id must to be")] = [
            'name' => $group->getName(),
            'attributes' => array_reduce(
                $attributes,
                function (array $acc, Attribute $attr): array {
                    $key = $attr->getId() ?? throw new \Exception("attribute id must to be");
                        $acc[$key] = ['name' => $attr->getName(), 'values' => $this->generateValues()];
                        return $acc;
                },
                $commonResult
            ),
        ];
    }

    /**
     * @param Closure(Generator):string $generator
     * @return list<string>
     * @throws \Exception
     */
    private function generateValuesByGenerator(\Closure $generator): array
    {
        $capacity = random_int(self::ATTR_VALUES_MIN_CAPACITY, self::ATTR_VALUES_MAX_CAPACITY);
        $result = [];

        for ($i = 0; $i< $capacity; $i++) {
            $result[] = $generator($this->faker);
        }
        return array_values(array_unique($result));
    }

    /**
     * @return list<string>
     * @throws \Exception
     */
    private function generateValues(): array
    {
        $capacity = random_int(self::ATTR_VALUES_MIN_CAPACITY, self::ATTR_VALUES_MAX_CAPACITY);
        $result = [];
        $variants = [
            fn(Generator $f): string => $f->hexColor(),
            fn(Generator $f): string => $f->jobTitle(),
            fn(Generator $f): string => $f->title(),
            fn(Generator $f): string => $f->word(),
            fn(Generator $f): string => (string)$f->randomNumber(2),
        ];

        for ($i = 0; $i< $capacity; $i++) {
            $variant = random_int(0, count($variants) - 1);
            $result[] = $variants[$variant]($this->faker);
        }
        return array_values(array_unique($result));
    }

    /**
     * @return list<Attribute>
     * @throws \Exception
     */
    private function createAttributes(): array
    {
        $attr = [];
        $capacity = random_int(self::ATTR_CAPACITY_MIN, self::ATTR_CAPACITY_MAX);
        for ($i = 0; $i < $capacity - 1; $i++) {
            $name = $this->faker->word();
            $attr[] = new Attribute(null, $name, '');
        }
        return $this->attributesRepository->insertMany($attr);
    }
}

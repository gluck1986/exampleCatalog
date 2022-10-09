<?php

namespace App\Service;

use App\Entity\Attribute;
use App\Entity\Group;
use App\Entity\Product;
use App\Repository\AttributesRepository;
use App\Repository\GroupRepository;
use App\Repository\ProductRepository;
use App\Service\Dto\AttributeDto;
use App\Service\Dto\GroupWithAttributesDto;
use Faker\Factory;
use Faker\Generator;
use Ramsey\Uuid\Uuid as UuidBuilder;

class DataGenerator
{
    /** @description цена товара min */
    private const COST_MIN = 1.0;
    /** @description цена товара max */
    private const COST_MAX = 99999.0;
    /** @description товаров в группе min */
    private const GROUP_MIN_CAPACITY = 999;
    /** @description товаров в группе max */
    private const GROUP_MAX_CAPACITY = 9_999_999;
    /** @description атрибутов у товара min */
    private const ATTR_IN_USE_MIN = 5;
    /** @description атрибутов у товара max */
    private const ATTR_IN_USE_MAX = 15;
    /** @description разных значений одного атрибута min */
    private const ATTR_VALUES_MIN_CAPACITY = 50;
    /** @description разных значений одного атрибута max */
    private const ATTR_VALUES_MAX_CAPACITY = 1000;
    /** @description разных атрибутов в группу min */
    private const ATTR_CAPACITY_MIN = 20;
    /** @description разных атрибутов в группу max */
    private const ATTR_CAPACITY_MAX = 35;
    /** @description писать товаров за раз */
    private const CHUNK_SIZE = 1000;

    private const DELIVERY_VALUES = [1, 2, 3, 4, 5];

    /**
     * @var list<Attribute>
     */
    private array $commonAttributes = [];

    private readonly Generator $faker;

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly AttributesRepository $attributesRepository,
        private readonly GroupRepository $groupRepository
    ) {
        $this->faker = Factory::create();
    }

    public function generate(): void
    {
        $groups = $this->createGroups();
        $this->commonAttributes = $this->createCommonAttributes();
        foreach ($groups as $group) {
            $attributes = $this->createAttributes();
            $groupWithAttributes = $this->mapGroupAttribute($group, $attributes);
            $this->generateProducts($groupWithAttributes);
        }
    }

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
            'Бренд' => fn(Generator $f): string => $f->company(),
            'Продавец' => fn(Generator $f): string => $f->firstName(),
            'Цвет' => fn(Generator $f): string => $f->colorName(),
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
     * @param list<Attribute> $attributes
     * @throws \Exception
     */
    private function mapGroupAttribute(Group $group, array $attributes): GroupWithAttributesDto
    {
        $commonResult = array_reduce(
            $this->commonAttributes,
            function (array $acc, Attribute $attr): array {
                $attrId = $attr->getId() ?? throw new \Exception('attribute id must to be');
                $acc[$attrId] =
                    new AttributeDto(
                        id: $attrId,
                        name: $attr->getName(),
                        values: $this->generateValuesByGenerator(
                            $this->getDefaultCommonAttributes()[$attr->getName()]
                            ?? throw new \Exception('unexpected no callable generator')
                        ),
                    );
                return $acc;
            },
            []
        );
        /** @var array<int, AttributeDto> $commonResult */
        $groupId = $group->getId() ?? throw  new \Exception('group id must be');
        /** @psalm-suppress MixedArgumentTypeCoercion */
        return new GroupWithAttributesDto(
            id: $groupId,
            name: $group->getName(),
            attributes: array_reduce(
                $attributes,
                function (array $acc, Attribute $attr): array {
                    $attrId = $attr->getId() ?? throw new \Exception('attribute id must to be');
                    $acc[$attrId] = new AttributeDto(
                        id: $attrId,
                        name: $attr->getName(),
                        values: $this->generateValues(),
                    );
                    return $acc;
                },
                $commonResult
            ),
        );
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

    /**
     * @throws \Exception
     */
    private function generateProducts(GroupWithAttributesDto $groupWithAttributes): void
    {
        $capacity = random_int(self::GROUP_MIN_CAPACITY, self::GROUP_MAX_CAPACITY);
        $buffer = [];
        print_r('group: ' . $groupWithAttributes->name . '; products: ' . $capacity);

        for ($i = 0; $i < $capacity - 1; $i++) {
            $product = new Product(
                guid: UuidBuilder::uuid4()->toString(),
                groupId: $groupWithAttributes->id,
                name: $this->faker->word . ' ' . $this->faker->word,
                cost: $this->faker->randomFloat(2, self::COST_MIN, self::COST_MAX),
                descr: $this->faker->sentence(),
                /** @psalm-suppress  InvalidArrayOffset */
                delivery: (int)$this->faker->randomElement(self::DELIVERY_VALUES),
                attr: $this->attributesFromExists($groupWithAttributes),
            );
            $buffer[] = $product;
            if (count($buffer) > self::CHUNK_SIZE) {
                $this->productRepository->write($buffer);
                $buffer = [];
            }
        }
        if (count($buffer) > 0) {
            $this->productRepository->write($buffer);
        }
    }

    /**
     * @return list<Attribute>
     * @throws \Exception
     */
    private function attributesFromExists(GroupWithAttributesDto $groupWithAttributes): array
    {
        $result = [];
        foreach ($this->commonAttributes as $attribute) {
            $attrId = $attribute->getId() ?? throw new \Exception('attribute id must to be');
            $attrDto = $groupWithAttributes->attributes[$attrId]
                ?? throw new \Exception('unexpected attribute no find in group');
            $result[$attrDto->id] = new Attribute(
                id: $attrDto->id,
                name: $attrDto->name,
                value: (string)$this->faker->randomElement($attrDto->values)
            );
        }

        $attrCapacity = random_int(self::ATTR_IN_USE_MIN, self::ATTR_IN_USE_MAX);
        /** @var AttributeDto[] $attrDtos */
        $attrDtos = $this->faker->randomElements($groupWithAttributes->attributes, $attrCapacity);
        foreach ($attrDtos as $attrDto) {
            $result[$attrDto->id] = new Attribute(
                id: $attrDto->id,
                name: $attrDto->name,
                value: (string)$this->faker->randomElement($attrDto->values)
            );
        }

        return array_values($result);
    }
}

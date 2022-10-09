<?php

namespace App\Repository;

use App\Entity\Attribute;

class AttributesRepository
{
    public function __construct(private readonly \PDO $pdo)
    {
    }

    /**
     * @param list<Attribute> $groups
     * @return list<Attribute>
     */
    public function insertMany(array $groups): array
    {
        return array_map(
            fn(Attribute $group) => $this->insert($group),
            $groups,
        );
    }

    private function insert(Attribute $attribute): Attribute
    {
        if (is_null($attribute->getId())) {
            $query = 'INSERT INTO `attributes`(`name`)'
                . 'VALUES(:attribute_name)';

            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':attribute_name' => $attribute->getName(),
            ]);
            $id = (int)$this->pdo->lastInsertId();
            $this->pdo->commit();

            return new Attribute($id, $attribute->getName(), $attribute->getValue());
        } else {
            $query = 'INSERT INTO `attributes`(`id`, `name`)'
                . 'VALUES(:attribute_id, :attribute_name)';
            $stmt = $this->pdo->prepare($query);

            $stmt->execute([
                ':attribute_id' => $attribute->getId(),
                ':attribute_name' => $attribute->getName(),
            ]);

            return $attribute;
        }
    }
}

<?php

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\GroupToAttribute;
use App\Repository\Mappers\AttributeMapper;
use PDO;

class GroupToAttributeRepository
{
    public function __construct(private readonly \PDO $pdo, private readonly AttributeMapper $attributeMapper)
    {
    }

    public function insert(GroupToAttribute $gta): GroupToAttribute
    {
        $query = 'INSERT IGNORE INTO `groups_to_attributes` (`group_id`, `attribute_id`)'
            . ' VALUES (:group_id, :attribute_id)';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':group_id' => $gta->getGroupId(),
            ':attribute_id' => $gta->getAttributeId(),
        ]);
        return $gta;
    }

    /**
     * @return list<GroupToAttribute>
     */
    public function getGroupToAttributeByGroup(int $groupId): array
    {
        $query = <<<SQL
SELECT * FROM `groups_to_attributes` WHERE `group_id` = :group_id
SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            return [];
        }
        return array_values(array_map(fn($row) => $this->makeEntity((array)$row), $rows));
    }

    /**
     * @return list<GroupToAttribute>
     */
    public function getGroupToAttributeByAttributeId(int $attributeId): array
    {
        $query = <<<SQL
SELECT * FROM `groups_to_attributes` WHERE `attribute_id` = :attribute_id
SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':attribute_id', $attributeId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            return [];
        }

        return array_values(array_map(fn($row) => $this->makeEntity((array)$row), $rows));
    }

    /**
     * @return list<Attribute>
     */
    public function getAttributesByGroup(int $groupId): array
    {
        $query = <<<SQL
SELECT
    a.*
FROM
    `groups_to_attributes` as gta
    left join `attributes` as a ON a.`id` = gta.`attribute_id`
WHERE
    gta.`group_id` = :group_id
SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            return [];
        }
        return array_values(array_map(fn(array$row) => $this->attributeMapper->rowToDomain($row), $rows));
    }

    private function makeEntity(array $row): GroupToAttribute
    {
        return new GroupToAttribute(
            groupId: empty($row['group_id'])
                ? throw new \Exception('group_id must be') : (int)$row['group_id'],
            attributeId: empty($row['attribute_id'])
                ? throw new \Exception('attribute_id must be') : (int)$row['attribute_id']
        );
    }
}

<?php

namespace App\Repository;

use App\Entity\Group;

class GroupRepository
{
    public function __construct(private readonly \PDO $pdo)
    {
    }

    /**
     * @param list<Group> $groups
     * @return list<Group>
     */
    public function insertMany(array $groups): array
    {
        return array_map(
            fn(Group $group) => $this->insert($group),
            $groups,
        );
    }

    private function insert(Group $group): Group
    {
        if (is_null($group->getId())) {
            $query = 'INSERT INTO `groups` (`name`)'
                . 'VALUES(:group_name)';
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':group_name' => $group->getName(),
            ]);
            $id = (int)$this->pdo->lastInsertId();
            $this->pdo->commit();

            return new Group($id, $group->getName());
        } else {
            $query = 'INSERT INTO `groups`(`id`, `name`)'
                . 'VALUES(:group_id, :group_name)';
            $stmt = $this->pdo->prepare($query);

            $stmt->execute([
                ':group_id' => $group->getId(),
                ':group_name' => $group->getName(),
            ]);

            return $group;
        }
    }
}

<?php

/**
 * (c) Evis Bregu <evis.bregu@gmail.com>.
 */

namespace AppBundle\Menu;

use AppBundle\Entity\Menu;

class MenuHelper
{
    /**
     * @var Menu[]|array
     */
    private $menuObjects;

    /**
     * MenuHelper constructor.
     *
     * @param Menu[] $menuObjects
     */
    public function __construct(array $menuObjects)
    {
        $this->menuObjects = $menuObjects;
    }

    /**
     * @return array
     */
    public function getMenuTree()
    {
        $menuTree = [];
        foreach ($this->menuObjects as $menuRecord) {
            if ($menuRecord->getParentId() === 0) {
                $menuTree[$menuRecord->getId()] = [
                    'icon' => $menuRecord->getIcon(),
                    'name' => $menuRecord->getName(),
                    'link' => $menuRecord->getLink(),
                    'hasChildren' => false,
                    'childrens' => [],
                ];
            } else {
                $menuTree[$menuRecord->getParentId()]['hasChildren'] = true;
                array_push(
                    $menuTree[$menuRecord->getParentId()]['childrens'],
                    [
                        'name' => $menuRecord->getName(),
                        'link' => $menuRecord->getLink(),
                        'icon' => $menuRecord->getIcon(),
                        'header' => $menuRecord->getNavHeader(),
                    ]
                );
            }
        }

        return $menuTree;
    }
}

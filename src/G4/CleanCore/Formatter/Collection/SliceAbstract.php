<?php

namespace G4\CleanCore\Formatter\Collection;

use G4\CleanCore\Formatter\Collection\CollectionAbstract;
use G4\CleanCore\Paginator\Adapter\Iterator;

abstract class SliceAbstract extends CollectionAbstract
{

    public function format()
    {
        if ($this->_hasItems()) {
            foreach ($this->_getResourceCollection() as $resource) {
                $this->_formatOneResource($resource);
            }
        }

        return $this->_getPaginatorResponse();
    }

    protected function _getPaginatorResponse()
    {
        $totalItems = $this->_getResourceCollection()->getTotalItemsCount();

        return array(
            'current_page_number' => $this->_resource['page'],
            'total_item_count'    => $totalItems,
            'item_count_per_page' => $this->_resource['per_page'],
            'current_item_count'  => count($this->_data),
            'page_count'          => ceil($totalItems / $this->_resource['per_page']),
            'current_items'       => $this->_data
        );
    }

    private function _hasItems()
    {
        return $this->_getResourceCollection()->count() > 0;
    }

}
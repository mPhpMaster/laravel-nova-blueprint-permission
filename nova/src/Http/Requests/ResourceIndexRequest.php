<?php

namespace Laravel\Nova\Http\Requests;

use Laravel\Nova\Contracts\QueryBuilder;

class ResourceIndexRequest extends NovaRequest
{
    use CountsResources, QueriesResources;

    /**
     * Get the paginator instance for the index request.
     *
     * @return array
     */
    public function searchIndex()
    {
//	    return app()->make(QueryBuilder::class, [$this->resource()])->search(
//		    $this, $this->newQuery(), $this->search,
//		    $this->filters()->all(), $this->orderings(), $this->trashed()
//	    )->paginate((int) $this->perPage());
        /** @var \App\Nova\Resource $resource */
        $resource = $this->resource();

        $result = app()->make(QueryBuilder::class, [ $resource ])->search(
            $this,
            $this->newQuery(),
            $this->search,
            $this->filters()->all(),
            $this->orderings(),
            $this->trashed()
        );
        $count = $this->toCount();
        $count = $count ?: -1;
        $perPage = (int) $this->perPage();
        $perPage = $perPage < 1 ? null : $perPage;
        $perPage = $perPage ?: $count;

        return $result->paginate($perPage);
    }

    /**
     * Get the count of the resources.
     *
     * @return int
     */
    public function toCount()
    {
//	    return app()->make(QueryBuilder::class, [$this->resource()])->search(
//		    $this, $this->newQuery(), $this->search,
//		    $this->filters()->all(), $this->orderings(), $this->trashed()
//	    )->toBaseQueryBuilder()->getCountForPagination();
        return app()->make(QueryBuilder::class, [ $this->resource() ])->search(
            $this,
            $this->newQuery(),
            $this->search,
            $this->filters()->all(),
            $this->orderings(),
            $this->trashed()
        )->toBaseQueryBuilder()->getCountForPagination();
    }

    /**
     * Get per page.
     *
     * @return int
     */
    public function perPage()
    {
        $resource = $this->resource();

        if ($this->viaRelationship()) {
            return (int) $resource::$perPageViaRelationship;
        }

        $perPageOptions = $resource::perPageOptions();

        if (empty($perPageOptions)) {
            $perPageOptions = [$resource::newModel()->getPerPage()];
        }

        $recCount = $this->toCount();
        if( intval($this->perPage) === -1 ) {
            $recCount = $recCount === 0 ? -1 : $recCount;
        }

	    return (int) intval($this->perPage) === $recCount || in_array($this->perPage, $perPageOptions) ? $this->perPage : $perPageOptions[ 0 ];
//	    return (int) in_array($this->perPage, $perPageOptions) ? $this->perPage : $perPageOptions[0];
    }
}

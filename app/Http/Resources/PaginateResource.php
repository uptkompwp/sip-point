<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginateResource extends JsonResource
{
    private $dataResource;
    public function __construct($resource, mixed  $dataResource = null)
    {
        $this->dataResource = $dataResource;
        parent::__construct($resource);
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->dataResource != null ? $this->dataResource::collection($this->getCollection()) : $this->getCollection(),
            'pagination' =>  [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'links' => $this->links(),
                'options' => [
                    'page_name' => $this->getOptions()['pageName'],
                    'prev' => $this->previousPageUrl() != null ?  $this->parsingNumberPagination($this->getOptions()['pageName'], $this->previousPageUrl()) : null,
                    'next' => $this->nextPageUrl() != null ? $this->parsingNumberPagination($this->getOptions()['pageName'], $this->nextPageUrl()) : null,
                    'links' => LinksPaginationResource::collection(parent::toArray($request)['links'])
                ],
            ],
        ];
    }

    private function parsingNumberPagination(string $pageKey, string|null $url)
    {
        preg_match_all("/$pageKey=(\d+)/", $url, $match);
        return (int) end($match)[0];
    }
}

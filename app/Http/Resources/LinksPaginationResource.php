<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinksPaginationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'label' => $this['label'], 'active' => $this['active'], 'page' =>  $this['url'] != null ? $this->parsingNumberPagination('page', $this['url']) : null, 'url' => $this['url']
        ];
    }
    private function parsingNumberPagination(string $pageKey, string|null $url)
    {
        preg_match_all("/$pageKey=(\d+)/", $url, $match);
        return (int) end($match)[0];
    }
}

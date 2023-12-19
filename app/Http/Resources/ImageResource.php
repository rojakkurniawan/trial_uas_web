<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'category'=>$this->category,
            'image_file_name'=>$this->image,
            'image_url'=>asset('api/image/'.$this->image),
            'created_at'=>$this->created_at->format('d-m-Y H:i:s'),
            'updated_at'=>$this->updated_at->format('d-m-Y H:i:s'),
        ];
    }
}

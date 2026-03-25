<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="AppUser",
 *     type="object",
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="user_name", type="string", example="Juan Perez"),
 *     @OA\Property(property="email_address", type="string", format="email", example="juan@example.com"),
 *     @OA\Property(property="phone", type="string", example="+34600000000"),
 *     @OA\Property(property="user_tag", type="string", example="indv"),
 *     @OA\Property(property="is_active", type="boolean", example=true)
 * )
 *
 * @OA\Schema(
 *     schema="CarAdvert",
 *     type="object",
 *     @OA\Property(property="ad_id", type="integer", example=10),
 *     @OA\Property(property="ad_title", type="string", example="BMW Serie 3 320d"),
 *     @OA\Property(property="ad_type", type="string", example="used"),
 *     @OA\Property(property="price", type="number", format="float", example=23990.00),
 *     @OA\Property(property="car_color", type="string", example="gris"),
 *     @OA\Property(property="region", type="string", example="Madrid"),
 *     @OA\Property(property="city", type="string", example="Alcobendas"),
 *     @OA\Property(property="visible", type="boolean", example=true)
 * )
 */
class Schemas
{
}

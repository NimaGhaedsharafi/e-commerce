<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/2/17
 * Time: 12:06
 */

namespace AppBundle\Controller;


use AppBundle\Contracts\Arrayable;
use AppBundle\Contracts\Collection;
use AppBundle\Contracts\Renderable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseController
 * @package AppBundle\Controller
 */
class BaseController extends Controller
{
    /**
     * Renders a view.
     *
     * @param Renderable|string $data
     * @param int $status
     * @param array $headers
     *
     * @return Response A Response instance
     */
    protected function response($data, $status = 200, $headers = [])
    {
        if (is_array($data) && $data[0] instanceof Renderable) {

            $data = json_encode(array_map(function ($value) {
                return $value instanceof Arrayable ? $value->toArray() : $value;
            }, $data));
        } elseif ($data instanceof Renderable) {
            $data = $data->render();
        }

        $headers = array_merge(['content-type' => 'application/json'], $headers);

        return new Response($data, $status, $headers);
    }
}
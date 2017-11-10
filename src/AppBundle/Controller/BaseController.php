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
     * @param Renderable|string|null $data
     * @param int $status
     * @param array $headers
     *
     * @return Response A Response instance
     */
    protected function response($data = null, $status = 200, $headers = [])
    {
        if (is_array($data) && isset($data[0]) && $data[0] instanceof Renderable) {
            $data = json_encode(array_map(function ($value) {
                return $value instanceof Arrayable ? $value->toArray() : $value;
            }, $data));
        } elseif ($data instanceof Renderable) {
            $data = $data->render();
        } elseif (is_array($data)) {
            $data = json_encode($data);
        } elseif ($data === null) {
            if (floor($status / 200) == 1) {
                $data = json_encode(['status' => 'ok']);
            } else {
                $data = json_encode(['status' => 'failed']);
            }
        }

        $headers = array_merge(['content-type' => 'application/json'], $headers);

        return new Response($data, $status, $headers);
    }

    /**
     * @param null $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    protected function abort($data = null, $status = 400, $headers = [])
    {
        return $this->response($data, $status, $headers);
    }

    /**
     * @return Response
     */
    protected function ack()
    {
        return $this->response(null, Response::HTTP_NO_CONTENT);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: nghaedsharafi
 * Date: 11/2/17
 * Time: 12:06
 */

namespace AppBundle\Controller;


use App\Contracts\Renderable;
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
     * @param string|Renderable   $view       The view name
     * @param array    $parameters An array of parameters to pass to the view
     * @param Response $response   A response instance
     *
     * @return Response A Response instance
     */
    protected function render($view, array $parameters = [], Response $response = null)
    {
        if ($view instanceof Renderable) {
            $view = $view->render();
        }

        return parent::render($view, $parameters, $response);
    }
}
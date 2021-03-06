<?php
/**
 * @copyright Copyright (c) 2020 Milos Petkovic <milos.petkovic@elb-solutions.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\ElbCalTypes\Controller;

use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Controller;

class PageController extends Controller
{
    /**
     * @var
     */
    private $userId;

    /**
     * PageController constructor.
     *
     * @param $AppName
     * @param IRequest $request
     * @param $UserId
     */
    public function __construct($AppName, IRequest $request, $UserId)
    {
        parent::__construct($AppName, $request);
        $this->userId = $UserId;
    }

    /**
     * Index page
     *
     * CAUTION: the @Stuff turns off security checks; for this page no admin is
     *          required and no CSRF check. If you don't know what CSRF is, read
     *          it up in the docs or you might create a security hole. This is
     *          basically the only required method to add this exemption, don't
     *          add it to any other method if you don't exactly know what it does
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index()
    {
        $response = new TemplateResponse(
            'elb_cal_types',
            'index',
            [   'data' => '',
                'active_route' => $this->getCurrentPageRoute() ] );
        $policy = new ContentSecurityPolicy();
        $policy->allowEvalScript(true);
        $response->setContentSecurityPolicy($policy);

        return $response;
    }


    /**
     * Get app's full current route
     *
     * @return string
     */
    private function getCurrentRoute()
    {
        return $this->request->parameters['_route'];
    }

    /**
     * Pull out app's current page route only
     *
     * @return string
     */
    private function getCurrentPageRoute()
    {
        $arr = explode('.', $this->getCurrentRoute());
        return end($arr);
    }

}

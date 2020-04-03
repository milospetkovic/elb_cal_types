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


use OCA\ElbCalTypes\CurrentUser;
use OCA\ElbCalTypes\Service\ElbGroupFolderUserService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;

class UserController extends Controller
{
    /**
     * @var CurrentUser
     */
    private $currentUser;
    /**
     * @var ElbGroupFolderUserService
     */
    private $groupFolderUser;

    /**
     * UserController constructor.
     * @param CurrentUser $currentUser
     * @param ElbGroupFolderUserService $groupFolderUser
     */
    public function __construct(CurrentUser $currentUser,
                                ElbGroupFolderUserService $groupFolderUser)
    {
        $this->currentUser = $currentUser;
        $this->groupFolderUser = $groupFolderUser;
    }

    /**
     * Check up if current logged in user is super admin
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function isusersuperadmin()
    {
        return new JSONResponse($this->currentUser->isCurrentUserSuperAdmin());
    }

    /**
     * Check up if current logged in user is assigned as admin for group folder
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function isuseradminforgroupfolder()
    {
        return new JSONResponse($this->groupFolderUser->isCurrentUserAdminForGroupFolder());
    }

}
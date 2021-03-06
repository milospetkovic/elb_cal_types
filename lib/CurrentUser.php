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

namespace OCA\ElbCalTypes;


use OCA\Activity\CurrentUser as CurrentUserAlias;
use OCP\IDBConnection;
use OCP\IRequest;

class CurrentUser
{
    /** @var IRequest */
    protected $request;

    /**
     * @var CurrentUserAlias
     */
    private $currentUser;

    /**
     * @var IDBConnection
     */
    private $connection;

    /**
     * CurrentUser constructor.
     * @param IRequest $request
     * @param CurrentUserAlias $currentUser
     */
    public function __construct(IRequest $request,
                                CurrentUserAlias $currentUser,
                                IDBConnection $connection)
    {
        $this->request = $request;
        $this->currentUser = $currentUser;
        $this->connection = $connection;
    }

    /**
     * Check up if current logged in user belongs to the super admin user group
     *
     * @return array
     */
    public function isCurrentUserSuperAdmin()
    {
        $stmt = $this->connection->prepare('SELECT 1 FROM `*PREFIX*group_user` as gu where `gu`.`gid`= "admin" and `gu`.`uid`= "'.$this->currentUser->getUserIdentifier().'"' );
        $stmt->execute();
        $isSuperAdmin = false;
        while ($row = $stmt->fetch()) {
            $isSuperAdmin = true;
        }
        return ['isSuperAdmin' => $isSuperAdmin];
    }

    /**
     * Get one super admin user id from super admin user group (needed for first migration)
     *
     * @return string
     */
    public function getOneUserFromAdminGroup()
    {
        $stmt = $this->connection->prepare('SELECT gu.uid as user_id FROM `*PREFIX*group_user` as gu where `gu`.`gid`= "admin" LIMIT 1' );
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['user_id'];
    }

}

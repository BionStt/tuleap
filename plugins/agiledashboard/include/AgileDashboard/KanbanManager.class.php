<?php
/**
 * Copyright (c) Enalean, 2014. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */
class AgileDashboard_KanbanManager {

    /**
     * @var AgileDashboard_KanbanDao
     */
    private $dao;

    public function __construct(AgileDashboard_KanbanDao $dao) {
        $this->dao = $dao;
    }

    public function isKanbanActivatedForProject($project_id) {
        $row = $this->dao->isActivated($project_id)->getRow();

        if (! $row) {
            return false;
        }

        return $row['kanban'];
    }

    public function doesKanbanExistForTracker(Tracker $tracker) {
        return $this->dao->getKanbanByTrackerId($tracker->getProject()->getID(), $tracker->getId())->count() > 0;
    }

    public function createKanban($project_id, $kanban_name, $tracker_id) {
        return $this->dao->create($project_id, $kanban_name, $tracker_id);
    }

    public function activateKanban($project_id) {
        return $this->dao->activate($project_id);
    }

    public function deactivateKanban($project_id) {
        return $this->dao->deactivate($project_id);
    }

    public function getTrackersWithKanbanUsage($project_id) {
        return $this->dao->getTrackersWithKanbanUsage($project_id);
    }

}

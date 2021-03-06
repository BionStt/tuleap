<?php
/**
 * Copyright (c) Enalean, 2017. All Rights Reserved.
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
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tuleap\Tracker\Admin;

use CSRFSynchronizerToken;
use Project;

class GlobalAdminPresenter
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $table_title;

    /**
     * @var string
     */
    public $switch_label;

    /**
     * @var string
     */
    public $form_url;

    /**
     * @var CSRFSynchronizerToken
     */
    public $csrf_token;

    /**
     * @var bool
     */
    public $are_artifact_link_types_enabled;

    /**
     * @var array
     */
    public $artifact_link_types;

    /**
     * @var string
     */
    public $switch_button_label;

    /**
     * @var string
     */
    public $form_type_url;

    /**
     * @var string
     */
    public $warning_message;

    /**
     * @var string
     */
    public $has_at_least_one_disabled_type;

    public function __construct(
        Project $project,
        CSRFSynchronizerToken $csrf_token,
        $are_artifact_link_types_enabled,
        array $artifact_link_types,
        $has_at_least_one_disabled_type
    ) {
        $this->title        = dgettext('tuleap-tracker', 'Tracker global admininistration');
        $this->table_title  = dgettext('tuleap-tracker', 'Artifact links types');
        $this->switch_label = dgettext('tuleap-tracker', 'Activate artifact links types for all the trackers of this project?');

        $this->form_url = TRACKER_BASE_URL . '/?' . http_build_query(array(
            'func'     => 'edit-global-admin',
            'group_id' => $project->getID()
        ));

        $this->csrf_token                      = $csrf_token;
        $this->are_artifact_link_types_enabled = $are_artifact_link_types_enabled;

        $this->available_types     = $GLOBALS['Language']->getText('plugin_tracker_artifact_links_natures', 'available_natures');
        $this->shortname_label     = $GLOBALS['Language']->getText('plugin_tracker_artifact_links_natures', 'shortname');
        $this->forward_label_label = $GLOBALS['Language']->getText('plugin_tracker_artifact_links_natures', 'forward_label');
        $this->reverse_label_label = $GLOBALS['Language']->getText('plugin_tracker_artifact_links_natures', 'reverse_label');
        $this->switch_button_label = dgettext('tuleap-tracker', 'Use');
        $this->artifact_link_types = $artifact_link_types;

        $this->form_type_url = TRACKER_BASE_URL . '/?' . http_build_query(array(
            'func'     => 'use-artifact-link-type',
            'group_id' => $project->getID()
        ));

        $this->warning_message = dgettext(
            'tuleap-tracker',
            'After artifact edition, all the disabled types used will be cleared without removing the existing link.'
        );

        $this->has_at_least_one_disabled_type = $has_at_least_one_disabled_type;
    }
}

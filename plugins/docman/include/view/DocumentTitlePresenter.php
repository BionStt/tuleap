<?php
/**
 * Copyright (c) Enalean, 2019. All Rights Reserved.
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

declare(strict_types=1);

namespace Tuleap\Docman;

use Tuleap\Docman\ExternalLinks\ExternalLinksManager;
use Tuleap\Docman\ExternalLinks\Link;

class DocumentTitlePresenter
{
    /**
     * @var string
     */
    public $title;
    /**
     * @var bool
     */
    public $should_display_external_link;
    /**
     * @var string
     */
    public $project_shortname;
    /**
     * @var Link[]
     */
    public $links;

    public function __construct(
        \Project $project,
        string $title,
        ExternalLinksManager $collector
    ) {
        $this->title                        = $title;
        $this->should_display_external_link = $collector->hasExternalLinks();
        $this->project_shortname            = $project->getUnixNameLowerCase();
        $this->links                        = $collector->getLinks();
    }
}
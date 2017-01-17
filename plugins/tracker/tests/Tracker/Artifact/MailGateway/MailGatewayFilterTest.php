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

namespace Tuleap\Tracker\Artifact\MailGateway;

use TuleapTestCase;

require_once TRACKER_BASE_DIR . '/../tests/bootstrap.php';

class MailGatewayFilterTest extends TuleapTestCase
{
    /**
     * @var MailGatewayFilter
     */
    private $mail_filter;

    public function setUp()
    {
        $this->mail_filter = new MailGatewayFilter();
    }
    public function itReturnsTrueWhenAutoReplyIsSetToAutoGenerated()
    {
        $raw_mail['headers']['auto-submitted'] = 'auto-generated';
        $this->assertTrue($this->mail_filter->isAnAutoReplyMail($raw_mail));
    }

    public function itReturnsFalseWhenAutoReplyIsSetToNoValue()
    {
        $raw_mail['headers']['auto-submitted'] = 'no';
        $this->assertFalse($this->mail_filter->isAnAutoReplyMail($raw_mail));
    }

    public function itReturnsTrueWhenReturnPathIsNotSet()
    {
        $raw_mail['headers']['auto-submitted'] = 'no';
        $raw_mail['headers']['return-path']    = '<>';
        $this->assertTrue($this->mail_filter->isAnAutoReplyMail($raw_mail));
    }

    public function itReturnsFalseWhenReturnPathIsSet()
    {
        $raw_mail['headers']['auto-submitted'] = 'no';
        $raw_mail['headers']['return-path']    = '<mail@example.com>';
        $this->assertFalse($this->mail_filter->isAnAutoReplyMail($raw_mail));
    }

    public function itReturnsTrueWhenAutoReplyIsAutoGeneratedAndPathIsSet()
    {
        $raw_mail['headers']['auto-submitted'] = 'auto-generated';
        $raw_mail['headers']['return-path']    = '<mail@example.com>';
        $this->assertTrue($this->mail_filter->isAnAutoReplyMail($raw_mail));
    }
}

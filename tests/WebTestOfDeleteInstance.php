<?php
/**
 *
 * ThinkUp/tests/WebTestOfDeleteInstance.php
 *
 * Copyright (c) 2009-2011 Gina Trapani
 *
 * LICENSE:
 *
 * This file is part of ThinkUp (http://thinkupapp.com).
 *
 * ThinkUp is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any
 * later version.
 *
 * ThinkUp is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with ThinkUp.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 *
 * @author Gina Trapani <ginatrapani[at]gmail[dot]com>
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2009-2011 Gina Trapani
 */
require_once dirname(__FILE__).'/init.tests.php';
require_once THINKUP_ROOT_PATH.'webapp/_lib/extlib/simpletest/autorun.php';
require_once THINKUP_ROOT_PATH.'webapp/_lib/extlib/simpletest/web_tester.php';

class WebTestOfDeleteInstance extends ThinkUpWebTestCase {

    public function setUp() {
        parent::setUp();
        $this->builders = self::buildData();
    }

    public function tearDown() {
        $this->builders = null;
        parent::tearDown();
    }

    public function testDeleteInstance() {
        $this->get($this->url.'/session/login.php');
        $this->setField('email', 'me@example.com');
        $this->setField('pwd', 'secretpassword');

        $this->click("Log In");
        $this->assertTitle("thinkupapp's Dashboard | ThinkUp");
        $this->assertText('Logged in as: me@example.com');

        $this->click("Settings");
        $this->click("Twitter");

        $this->assertLink('ev');
        $this->assertLink('thinkupapp');
        $this->assertLink('linkbaiter');
        $this->assertLink('shutterbug');
        $this->assertSubmit('delete');

        //delete existing instance
        $this->post($this->url.'/account/index.php?p=twitter', array('action'=>'delete', 'instance_id'=>'3'));
        $this->assertText('Account deleted.');
        $this->assertLink('thinkupapp');
        $this->assertLink('linkbaiter');
        $this->assertNoLink('shutterbug');
        $this->assertSubmit('delete');

        //delete non-existent instance
        $this->post($this->url.'/account/index.php?p=twitter', array('action'=>'delete', 'instance_id'=>'231'));
        $this->assertText("Instance doesn't exist.");
        $this->assertLink('thinkupapp');
        $this->assertLink('linkbaiter');
        $this->assertSubmit('delete');

        $this->click('Log Out');
        $this->assertText('You have successfully logged out');

        $this->get($this->url.'/session/login.php');
        $this->setField('email', 'me2@example.com');
        $this->setField('pwd', 'secretpassword');
        $this->click("Log In");

        //delete instance with no privileges
        $this->post($this->url.'/account/index.php?p=twitter', array('action'=>'delete', 'instance_id'=>'2'));

        $this->assertText("Insufficient privileges.");
    }
}
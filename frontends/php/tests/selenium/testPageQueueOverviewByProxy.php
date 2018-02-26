<?php
/*
** Zabbix
** Copyright (C) 2001-2018 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/

require_once dirname(__FILE__).'/../include/class.cwebtest.php';

class testPageQueueOverviewByProxy extends CWebTest {
	public static function allProxies() {
		return DBdata("select * from hosts where status in (".HOST_STATUS_PROXY_ACTIVE.','.HOST_STATUS_PROXY_PASSIVE.") order by hostid");
	}

	/**
	* @dataProvider allProxies
	*/
	public function testPageQueueOverviewByProxy_CheckLayout($proxy) {
		$this->zbxTestLogin('queue.php?config=1');
		$this->zbxTestCheckTitle('Queue [refreshed every 30 sec.]');
		$this->zbxTestTextNotPresent('Cannot display item queue.');
		$this->zbxTestCheckHeader('Queue of items to be updated');
		$this->zbxTestDropdownSelectWait('config', 'Overview by proxy');
		$this->zbxTestDropdownHasOptions('config', ['Overview', 'Overview by proxy', 'Details']);
		$this->zbxTestTextPresent(
			[
				'Proxy',
				'5 seconds',
				'10 seconds',
				'30 seconds',
				'1 minute',
				'5 minutes',
				'More than 10 minutes'
			]
		);
		$this->zbxTestTextPresent($proxy['host']);
		$this->zbxTestTextPresent('Server');
	}

}

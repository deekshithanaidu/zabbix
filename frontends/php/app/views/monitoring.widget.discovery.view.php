<?php
/*
** Zabbix
** Copyright (C) 2001-2017 Zabbix SIA
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
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
**/


$drules = API::DRule()->get([
	'output' => ['druleid', 'name'],
	'selectDHosts' => ['status'],
	'filter' => ['status' => DHOST_STATUS_ACTIVE]
]);
CArrayHelper::sort($drules, ['name']);

foreach ($drules as &$drule) {
	$drule['up'] = 0;
	$drule['down'] = 0;

	foreach ($drule['dhosts'] as $dhost){
		if (DRULE_STATUS_DISABLED == $dhost['status']) {
			$drule['down']++;
		}
		else {
			$drule['up']++;
		}
	}
}
unset($drule);

$table = (new CTableInfo())
	->setHeader([
		_('Discovery rule'),
		_x('Up', 'discovery results in dashboard'),
		_x('Down', 'discovery results in dashboard')
	]);

foreach ($drules as $drule) {
	$table->addRow([
		new CLink($drule['name'], 'zabbix.php?action=discovery.view&druleid='.$drule['druleid']),
		($drule['up'] != 0) ? (new CSpan($drule['up']))->addClass(ZBX_STYLE_GREEN) : '',
		($drule['down'] != 0) ? (new CSpan($drule['down']))->addClass(ZBX_STYLE_RED) : ''
	]);
}

$output = [
	'header' => $data['name'],
	'body' => $table->toString(),
	'footer' => (new CList([_s('Updated: %s', zbx_date2str(TIME_FORMAT_SECONDS))]))->toString()
];

if (($messages = getMessages()) !== null) {
	$output['messages'] = $messages->toString();
}

if ($data['user']['debug_mode'] == GROUP_DEBUG_MODE_ENABLED) {
	CProfiler::getInstance()->stop();
	$output['debug'] = CProfiler::getInstance()->make()->toString();
}

echo (new CJson())->encode($output);

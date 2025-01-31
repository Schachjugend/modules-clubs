<?php

/**
 * clubs module
 * form script: delete a weekly event
 *
 * Part of »Zugzwang Project«
 * https://www.zugzwang.org/modules/clubs
 *
 * @author Gustaf Mossakowski <gustaf@koenige.org>
 * @copyright Copyright © 2017, 2019, 2021, 2023-2024 Gustaf Mossakowski
 * @license http://opensource.org/licenses/lgpl-3.0.html LGPL-3.0
 */


if (count($brick['vars']) !== 2) wrap_quit(404);
if ($brick['vars'][1].'' !== intval($brick['vars'][1]).'') wrap_quit(404);
mf_clubs_editform($brick['data']);

$zz = zzform_include('wochentermine');
global $zz_page;
$zz['title'] = sprintf('%s<br>%s', $zz_page['db']['title'], $brick['data']['contact']);

$sql = 'SELECT wochentermin_id
	FROM wochentermine
	WHERE contact_id = %d
	AND wochentermin_id = %d';
$sql = sprintf($sql, $brick['data']['contact_id'], $brick['vars'][1]);
$zz['where']['wochentermin_id'] = wrap_db_fetch($sql, '', 'single value');
if (!$zz['where']['wochentermin_id']) {
	if (wrap_db_auto_increment('wochentermine') > $brick['vars'][1]) {
		wrap_quit(410, 'Der Eintrag wurde bereits gelöscht.');
	}
	wrap_quit(404);
}

// @todo: $zz['access'] = 'delete_only';
if (empty($_POST)) $_GET['mode'] = 'delete';
elseif (empty($_POST['zz_action']) OR $_POST['zz_action'] !== 'delete') wrap_quit(403);

$zz['page']['referer'] = '../../';
$zz['page']['dont_show_title_as_breadcrumb'] = true;
$zz['page']['meta'][] = ['name' => 'robots', 'content' => 'noindex, follow, noarchive'];
$zz['record']['redirect']['successful_delete'] = wrap_path('clubs_edit', $brick['data']['identifier']);
if (empty($_SESSION['login_id']))
	$zz['revisions_only'] = true;

$zz['record']['no_timeframe'] = true;

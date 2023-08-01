<?php

$homeSectionObj = new homeSection();

class homeSection
{

	var $mDb;
	var $mConfig;

	function __construct()
	{
		global $Config;
		$this->mDb = new iplus();
		$this->mConfig = $Config;
	}


	function convert_object_to_array($data)
	{

		if (is_object($data)) {
			$data = get_object_vars($data);
		}

		if (is_array($data)) {
			return array_map(__METHOD__, $data);
		} else {
			return $data;
		}
	}
	function getSomeSection($aStart, $aLimit, $sort, $type, $searchTitle)
	{
		$sql = "SELECT p.id, p.status, p.date_added, pl.title, u.full_name AS 'user_full_name' FROM home_section p";
		$sql .= " LEFT JOIN `users` u ON u.id = p.user_id ";
		$sql .= " LEFT JOIN `home_section_langs` pl ON p.id = pl.home_section_id ";
		$sql .= " WHERE p.id > 0 AND pl.lang_code = 'ar'";

		$sql .= $searchTitle ? ' AND pl.title like "%' . $searchTitle . '%"' : '';

		$sql .= " ORDER BY pl.{$sort} {$type}";
		$sql .= $aLimit ? " LIMIT {$aStart}, {$aLimit}" : '';

		//	 echo $sql; die();

		return $this->mDb->getAll($sql);
	}

	function getSearchSectionCount($sort, $type, $searchTitle)
	{
		$sql = "SELECT COUNT(p.`id`) as 'result_count' FROM home_section p";
		$sql .= " LEFT JOIN `home_section_langs` pl ON p.id = pl.home_section_id ";
		$sql .= " WHERE p.`id` > 0 AND pl.lang_code = 'ar'";

		$sql .= $searchTitle ? ' AND pl.`title` like "%' . $searchTitle . '%"' : '';

		$sql .= " ORDER BY pl.{$sort} {$type}";

		//echo $sql; die();

		return $this->mDb->getOne($sql);
	}

	function getSectionCount()
	{
		$sql = "SELECT COUNT(`id`) as 'count' FROM `pages`";
		return $this->mDb->getOne($sql);
	}

	function addEditSection($temp, $img, $ico)
	{
		$dataLangObj = json_decode($temp['langs']);
		$newDataLangObj = $this->convert_object_to_array($dataLangObj);

		// 	var_dump($temp); die();

		$user_id = $temp['user_id'];
		$status = $temp['status'] == 'true' ? "1" : "0";
		$id = $temp['id'];

		if ($id  < 1) {
			// add
			return;
		} else {
			// edit
			$check_query = "SELECT `id` FROM `home_section` WHERE `id` = '{$id}'";
			$check_result = $this->mDb->getOne($check_query);
			if ($check_result === false) {
				return 403;
			} else {
				$sql = "UPDATE `home_section` SET ";
				$sql .= " `user_id` = '{$user_id}', ";

				$sql .= " `status` = '{$status}' ";
				if ($img != '' || $img != null) {

					// Delete old image from the server
					$img_query = "SELECT `img` FROM `home_section` WHERE `id` = '{$id}'";
					$img_result = $this->mDb->getOne($img_query);

					$old_img = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'home_section/' . $img_result;

					if (file_exists($old_img)) {
						@unlink($old_img);
					}

					$sql .= ",`img`='{$img}' ";
				}
				if ($ico != '' || $ico != null) {

					// Delete old image from the server
					$ico_query = "SELECT `ico` FROM `home_section` WHERE `id` = '{$id}'";
					$ico_result = $this->mDb->getOne($ico_query);

					$old_ico = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->mConfig['uploads_path'] . 'home_section/' . $ico_result;

					if (file_exists($old_ico)) {
						@unlink($old_ico);
					}

					$sql .= ",`ico`='{$ico}' ";
				}
				$sql .= " WHERE `id` = '{$id}'";

				$this->mDb->query($sql);

				foreach ($newDataLangObj as $key => $value) {
					$description = addslashes($value['description']);
					$title = addslashes($value['title']);
					$stmt = "UPDATE `home_section_langs` SET ";
					$stmt .= " `title` = '{$title}', ";
					$stmt .= " `description` = '{$description}'";
					$stmt .= " WHERE `lang_code`='{$value['lang_code']}' AND `home_section_id` = '{$id}' ";
					//               echo $stmt;
					//               die();
					$res = $this->mDb->query($stmt);
				}

				return $res;
			}
		}
	}

	function getOneSection($id)
	{

		$result = array();

		$sql = "SELECT `id`, `user_id`, `img`, `ico`, `status` FROM `home_section`";
		$sql .= " WHERE `id` = '{$id}'";
		$result = $this->mDb->getRow($sql);

		$stmt = "SELECT pl.`lang_code`, pl.`title`, pl.`description` FROM `home_section_langs` pl";
		$stmt .= " WHERE pl.`home_section_id` = '{$id}'";
		$result['langs'] = $this->mDb->getAll($stmt);

		return $result;
	}
}

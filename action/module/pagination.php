<?php

/**
 * // require_once "../backheader.php";
 * => To.은비;
 * 이거 pagination에 꼭 필요한 거얌?? 권한 체크하고 로그 생성하는 php이던데...
 * =>To.지훈선배
 * 습관...
 */

//@Potatoeunbi
/**
 * Undocumented function
 *
 * @param [type] $write_pages : 페이지 인덱스 크기
 * @param [type] $page_size : 보여줄 row 수
 * @param [type] $cur_page : 현재 페이지 쪽수
 * @param [type] $total_count : 쿼리결과 전체 row의 수
 * @param string $add : 조건 풀리기 위함 방지
 * @return string $str : 페이징 UI(div 태그) 반환
 */
function Get_Pagenation($write_pages, $page_size, $cur_page, $total_count, $add = "")
{
	$url = "?page=";
	$total_page  = ceil($total_count / $page_size);

	$str = '';
	if ($cur_page > 1) {
		$str .= '<li class="page_item"><a href="' . $url . '1' . $add . '"><i class="xi-backward"></i></a></li>' . PHP_EOL;
	}

	$start_page = (((int)(($cur_page - 1) / $write_pages)) * $write_pages) + 1;
	$end_page = $start_page + $write_pages - 1;

	if ($end_page >= $total_page) $end_page = $total_page;

	if ($start_page > 1) $str .= '<li class="page_item"><a href="' . $url . ($start_page - 1) . $add . '"><i class="xi-angle-left-min xi-2x"></i></a></li>' . PHP_EOL;

	if ($total_page > 1) {
		for ($k = $start_page; $k <= $end_page; $k++) {
			if ($cur_page != $k)
				$str .= '<li class="page_item page_number"><a href="' . $url . $k . $add . '" data-dt-idx="'. $k . '"><em class="page_link">' . $k . '</em></a></li>' . PHP_EOL;
			else
				$str .= '<li class="page_item page_number"><a href="#" data-dt-idx="' . $k . '"><em class="page_link clicked">' . $k . '</em></a></li>' . PHP_EOL;
		}
	}

	if ($total_page > $end_page) $str .= '<li class="page_item"><a href="' . $url . ($end_page + 1) . $add . '"><i class="xi-angle-right-min xi-2x"></i></a></li>' . PHP_EOL;

	if ($cur_page < $total_page) {
		$str .= '<li class="page_item"><a href="' . $url . $total_page . $add . '"><i class="xi-forward"></i></a></li>' . PHP_EOL;
	}

	if ($str)
		return "<ul class=\"pagination\">{$str}</ul>";
	else
		return "";
}


//@Potatoeunbi
/**
 * @param mixed $col : uri 입력변수명
 * @param mixed $page : 페이지 쪽수
 * @param mixed $link_add : 현재 url
 * @param mixed $flag : asc이냐 desc이냐
 * @return string $qstr : 페이징 URL 반환 (a태그에 삽입됨)
 */
function Get_Sort_Link($col, $page, $link_add, $flag = 'asc')
{
	if ($flag == 'asc')
		$flag = 'desc';
	else
		$flag = 'asc';

	$qstr = "?page=" . $page . explode("&order", $link_add)[0] . "&order=" . $col . "&sc=" . $flag;

	return $qstr;
}

//@vanillacake369
/**
 * 페이지 쪽수 Getter
 * @param [type] $pageValue : 사용자가 클릭한 페이지 쪽수
 * @return string|integer
 */
function getPageValue($pageValue)
{
	if (isset($pageValue))
		return trim($pageValue);
	else
		return 1;
}

//@vanillacake369
/**
 * 사용자 검색값 Getter
 * @param [type] $searchValue : 사용자가 입력한 사용자 검색값
 * @return string|integer
 */
function getSearchValue($searchValue)
{
	if (isset($searchValue))
		return trim($searchValue);
	else
		return null;
}

//@vanillacake369
/**
 * 사용자 카테고리 선택값
 * @param [type] $categoryValue : 사용자가 선택한 카테고리 value 값
 * @return string|integer
 */
function getCategoryValue($categoryValue)
{
	if (isset($categoryValue))
		return trim($categoryValue);
	else
		return null;
}

//@vanillacake369
/**
 * 오름/내림차순선택값 Getter
 * @param [type] $orderValue : 사용자가 선택한 오름/내림차순 값
 * @return string|integer
 */
function getOrderValue($orderValue)
{
	if (isset($orderValue))
		return trim($orderValue);
	else
		return null;
}

//@Potatoeunbi
/**
 * 사용자 카테고리 선택값
 * @param [type] $pagesizeValue : 사용자가 선택한 페이지당 리스트 갯수
 * @return string|integer
 */
function getPageSizeValue($pagesizeValue)
{
	if (isset($pagesizeValue))
		return trim($pagesizeValue);
	else
		return 10;
}


//@vanillacake369
/**
 * Where절에 Like문 추가하는 함수
 *
 * @param [type] $sql_where : where 절
 * @param [type] $sql_like : 추가할 liek 절
 * @return string
 */
function addLikeToWhereStmt($sql_where, $sql_like)
{
	return ($sql_where .= $sql_like);
}

//@vanillacake369
/**
 * url에 uri 추가
 *
 * @param [type] $link : 현재 url
 * @param [type] $uri : 추가할 uri ex) &sc=
 * @param [type] $uriValue : uri value ex) asc
 * @return string
 */
function addToLink($link, $uri, $uriValue)
{
	if (is_array($uri) && is_array($uriValue)) {
		for ($i = 0; $i < count($uri); $i++) {
			$uriString = "&$uri[$i]=";
			$link .= $uriString . $uriValue[$uri[$i]];
		}
		return $link;
	} else {
		return ($link .= $uri . $uriValue);
	}
}

//@vanillacake369
/**
 * ORDER BY절 생성하여 반환하는 함수
 *	 
 * @param [type] $columnStartsWith : table 내 컬럼 시작명 
 * @param [type] $categoryValue : 선택한 카테고리
 * @param [type] $orderValue : 정렬방법 (ASC/DESC)
 * @return string
 */
function makeOrderBy($columnStartsWith, $categoryValue, $orderValue)
{
	$category = $columnStartsWith . $categoryValue;
	return " ORDER BY $category $orderValue ";
}

//@vanillacake369
/**
 * select box의 이전 선택값 유지하기 위한 함수
 *
 * @param [type] $value : get/post로 넘어온 값
 * @return array|NULL
 */
function maintainSelected($value)
{
	$isSelected = [];
	if (isset($value) && ($value != "non")) {;
		$isSelected[$value] = ' selected';
		return $isSelected;
	} else {
		return NULL;
	}
}

// @vanillacake369
/**
 * 어떤 페이지 내에 사용자가 검색값을 입력하였는지 체크하는 함수
 *  - 검색값 존재 시, true / 없다면 false
 *  - 함수의 파라미터가 검색값 배열이라면 검색값 배열 내에 값이 있는지 체크
 *
 * @param [type] $input : 검색값
 * @return boolean
 */
function hasSearchedValue($input)
{
	if (is_array($input)) {
		$isData = 0;
		foreach ($input as $data) {
			if ((!is_null($data)) && ($data != "non") && ($data != ""))
				$isData++;
		}
		return ($isData > 0) ? true : false;
	} else {
		return (!is_null($input)) && ($input != "non") && ($input != "");
	}
}
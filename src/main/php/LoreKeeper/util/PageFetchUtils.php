<?php

class PageFetchUtils {

	public static function fetchPagesByIds($pageids) {
		$blContentApi = new ApiMain( new FauxRequest(
				array(
						'action' => 'query',
						'prop' => 'revisions',
						'format' => 'xml',
						'rvprop' => 'content',
						'rvslots' => '*',
						'pageids' => implode("|", $pageids)),
				true
		) );
		$blContentApi->execute();
		$blContentData = & $blContentApi->getResult()->getResultData();
		return $blContentData["query"]["pages"];
	}
	
	public static function getBacklinkPagesIds($pageTitle) {
		$backlinksApi = new ApiMain( new FauxRequest(
				array(
						'action' => 'query',
						'list' => 'backlinks',
						'format' => 'xml',
						'bltitle' => $pageTitle,
						'blfilterredir' => 'all',
						'bllimit' => 500),
				true
		) );
		$backlinksApi->execute();
		$backlinksData = & $backlinksApi->getResult()->getResultData();
	
		$pageids = [];
		foreach($backlinksData["query"]["backlinks"] as $backlink) {
			if(is_array($backlink)) {
				array_push($pageids, $backlink["pageid"]);
			}
		}
	
		return $pageids;
	}
	
	/**
	 * Returns a subset of $categoryNames that are children of the "Knowledge" category.
	 * 
	 * @param unknown $categoryNames
	 * @return multitype:
	 */
	public static function filterKnowledgeCategories($categoryNames) {
		$prefixedCategories = array();
		foreach($categoryNames as $categoryName) {
			array_push($prefixedCategories, "Category:$categoryName");
		}
	
		$categoryInfoApi = new ApiMain( new FauxRequest(
				array(
						'action' => 'query',
						'prop' => 'categories',
						'format' => 'xml',
						'titles' => implode("|", $prefixedCategories)),
				true
		) );
		$categoryInfoApi->execute();
		$categoryInfoData = & $categoryInfoApi->getResult()->getResultData();
	
		$filtered = array();
		foreach($categoryInfoData["query"]["pages"] as $categoryPage) {
			if(is_array($categoryPage)) {
				// This is null for categories that are used but do not have its page createds yet
				if($categoryPage["categories"] != null) {
					foreach($categoryPage["categories"] as $category) {
						if(is_array($category)) {
							$superCategory = explode(":", $category["title"])[1];
							if(wfMessage("knowledgeCategory")->text() === $superCategory) {
								array_push($filtered, explode(":", $categoryPage["title"])[1]);
							}
						}
					}
				}
			}
		}
	
		return $filtered;
	}
}

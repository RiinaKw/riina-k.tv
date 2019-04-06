<?php

class Model_Track extends Model {

	function get_as_category()
	{
		$statement = $this->_dbh->prepare( <<<SELECT
	SELECT *
	FROM categories
	ORDER BY display_order ASC, id ASC;
SELECT
		);
		$statement->execute();

		$arr = array();
		foreach ($statement as $category) {
			$category_statement = $this->_dbh->prepare( <<<SELECT
	SELECT t.*, a.image_name
	FROM tracks AS t
		LEFT JOIN albums a
		ON t.album_id = a.id
	WHERE category_id = :category_id
	ORDER BY id ASC;
SELECT
			);
			$category_statement->bindValue(
				':category_id', $category['id'], PDO::PARAM_INT
			);
			$category_statement->execute();
			$arr[] = array(
				'count' => $category_statement->rowCount(),
				'slug' => $category['slug'],
				'title' => $category['title'],
				'tracks' => $category_statement->fetchAll(PDO::FETCH_ASSOC),
			);
		}
		return $arr;
	} // function get_as_category()

	public function get_by_slug($slug)
	{
		$statement = $this->_dbh->prepare( <<<SELECT
	SELECT t.*, a.image_name
	FROM tracks AS t
		LEFT JOIN albums a
		ON t.album_id = a.id
	WHERE t.slug = :slug;
SELECT
		);
		$statement->bindValue(':slug', $slug, PDO::PARAM_STR);
		$statement->execute();
		return $statement->fetch(PDO::FETCH_ASSOC);
	} // function get_by_slug()

} // class Model_Track

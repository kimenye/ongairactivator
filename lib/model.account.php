<?php
/**
 * Return an array containing ist of posts
 *
 * @return array
 */
function account_find_all()
{
	$db = option('db_conn');
	$sql = <<<SQL
	SELECT * 
	FROM accounts 
	ORDER BY modified_at DESC
SQL;
	$result = array();
	$stmt = $db->prepare($sql);
	if ($stmt->execute())
	{
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	return false;
}

/**
 * Return selected row from posts table
 *
 * @param int $id 
 * @return array
 */
function account_find($id)
{
	$db = option('db_conn');
	$sql = <<<SQL
	SELECT * 
	FROM accounts where id=:id
SQL;
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':id', $id, PDO::PARAM_INT);
	if ($stmt->execute() && $row = $stmt->fetch(PDO::FETCH_ASSOC))
	{
		return $row;
	}
	return null;
}


/**
 * Update an account table
 *
 * @param int $account_id
 * @param array $data
 * @return true or false
 */
function account_update($account_id, $data)
{
	$db = option('db_conn');
	$sql = <<<SQL
	UPDATE `accounts`
	SET setup = :setup
	WHERE id = :id
SQL;
	$stmt = $db->prepare($sql);
	$stmt->bindValue(':id', $post_id, PDO::PARAM_INT);
	$stmt->bindValue(':setup', $data['setup'], PDO::PARAM_BOOL);
	
	return $stmt->execute();
}

?>
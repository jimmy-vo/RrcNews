<?php
 /**
 * Sql.php
 *
 *	Sql interaction
 *
 * @author     Jimmy Vo
 * @project    Assignment 4
 */
 	define('Include_Sql', 'TRUE');
 	
	define('DB_DSN','mysql:host=localhost;dbname=serverside;charset=utf8');
	
	define('DB_USER','root');
	define('DB_PASS','');   
	
	
	define('FORMAT_DATE_SQL','Y-m-d h:i:s');
	define('FORMAT_DATE_VIEW','F d, Y, h:i a');
	
	class Sql
	{				
		public $error;		
		
		private $db_pdo = FALSE;
		
		protected  static $_instance;

		public static function getInstance()
		{
			if(is_null(self::$_instance))
			{
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		
		public function __construct()
		{
			try 
			{
				$this->db_pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
			} 
			catch (PDOException $e) 
			{
				$db_pdo = FALSE;
				$this->error = $e->getMessage();
			}
		}
		
		
		public function register($username, $password)
		{
			if ($this->db_pdo !== FALSE)
			{		
				try 
				{
					$statement = $this->db_pdo->prepare("INSERT INTO `users` (UserTypeId, UserName, Password) values (:UserTypeId, :UserName, :Password)");
					    
					$statement->bindValue(':UserTypeId', "1", PDO::PARAM_INT );  
					$statement->bindValue(':UserName', $username, PDO::PARAM_STR );
					$statement->bindValue(':Password', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR );					
					$statement->execute();
					return TRUE;
				} 
				catch(PDOException $e) 
				{
					return FALSE;
				}
			}
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}

		public function IsRegisterdUserName($username)
		{		
			//if database is not ready return fail
			if ($this->db_pdo !== FALSE)
			{
				//getting users database
				$statement = $this->db_pdo->prepare("
					SELECT 		UserName
					FROM 		users"); 
				$statement->execute(); 
				$authentications = $statement->fetchAll();

				foreach ($authentications as $item) 
				{
				  	if ($username === $item["UserName"])
				  	{
						return TRUE;
				  	}
				}
				return FALSE;
			}

			$this->error = "PDO: not initialize properly";
			return FALSE;
		}

		public function LoginAccount($username, $password)
		{		
			//make sure $_SERVER & $_SESSION are cleared
			$this->LoginAnonymous();

			//if database is not ready return fail
			if ($this->db_pdo !== FALSE)
			{
				//getting users database
				$statement = $this->db_pdo->prepare("
					SELECT 		Id ,
								UserTypeId,
								UserName,
								Password
					FROM 		users
					WHERE 		UserName = :UserName"); 
				$statement->bindValue(':UserName', $username, PDO::PARAM_STR );
				$statement->execute(); 
				$authentications = $statement->fetchAll();

				foreach ($authentications as $item) 
				{
				  	if (password_verify($password, $item["Password"]))
				  	{
						$_SESSION["User"] =  $item;
						return TRUE;
				  	}
				}
			}

			$this->error = "PDO: not initialize properly";
			return FALSE;
		}

		public function LoginAnonymous()
		{
			$_SESSION["User"] = [
									"Id"  => "-1",
									"UserName" => "Anonymous",
									"UserTypeId" => "1"
								];
		}

		public function authentication()
		{
			if (!isset( $_SESSION["User"]))
			{
				$this->LoginAnonymous();
			}

			return $_SESSION["User"];
		}

		public function LogOut()
		{
			session_destroy();
		}

		public function getNewsTitleAll()
		{
			if ($this->db_pdo !== FALSE)
			{				
				$statement = $this->db_pdo->prepare("
					SELECT 		Id 			id,
								Title 		title
					FROM 		news
					ORDER BY	TimeCreated DESC"); 
				$statement->execute(); 
				return $statement->fetchAll();
			}
			
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}
		
		public function getNewsTop($sortBy)
		{
			if ($this->db_pdo !== FALSE) 
			{			
				$statement = $this->db_pdo->prepare( "
					SELECT 		news.Id 											id,
								Title 												title,
								DATE_FORMAT(TimeCreated, '%M %d, %Y %h:%i %p') 		timecreated,
								DATE_FORMAT(TimeUpdated, '%M %d, %Y %h:%i %p') 		timeupdated,
								Content 											content,
								Image 												image,
								users.Id 											userid,
								users.UserName 										username
					FROM 		news
					JOIN 		users ON news.UserId = users.Id ".
					(($sortBy !== FALSE)? ("ORDER BY ". $sortBy ): " " ).
							"	LIMIT 		15 OFFSET 0 " ); 
				$statement->execute(); 
				return $statement->fetchAll();
			}
			
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}

		public function getNewsById($id)
		{
			if ($this->db_pdo !== FALSE)
			{				
				try
				{
					$statement = $this->db_pdo->prepare("
					SELECT 		news.Id 											id,
								Title 												title,
								DATE_FORMAT(TimeCreated, '%M %d, %Y %h:%i %p') 		timecreated,
								DATE_FORMAT(TimeUpdated, '%M %d, %Y %h:%i %p') 		timeupdated,
								Content 											content,
								Image 												image,
								users.Id 											userid,
								users.UserName 										username
					FROM 		news
					JOIN 		users ON news.UserId = users.Id
					WHERE 		news.Id=".$id); 
					$statement->execute(); 
					$result = $statement->fetchAll();
					if (count($result) !== 1)
					{
						return FALSE;
					}
					else
					{
						return $result[0];
					}
				}
				catch(PDOException $e) 
				{
					$this->error = "PDO: ".$e->getMessage();
					return FALSE;
				}
			}
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}
		
		
		public function deleteNewsById($id)
		{
			if ($this->db_pdo !== FALSE)
			{				
				try
				{
					$statement = $this->db_pdo->prepare("DELETE FROM `news` WHERE `news`.`Id` =".$id); 
					$statement->execute(); 
					return TRUE;
				}
				catch(PDOException $e) 
				{
					$this->error = "PDO: ".$e->getMessage();
					return FALSE;
				}
			}
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}
		
		public function updateNews($post, $userId)
		{
			if ($this->db_pdo !== FALSE)
			{		
				try 
				{
					$statement = $this->db_pdo->prepare("
						UPDATE 	news 
						SET 	Title = :Title, 
								Content = :Content,
								Image = :Image,
								TimeUpdated = CURRENT_TIMESTAMP
						WHERE 	Id = :Id
						AND 	UserId = :UserId");
					
					$statement->bindValue(':Title', $post["title"], PDO::PARAM_STR );      
					$statement->bindValue(':Content', $post["content"], PDO::PARAM_STR );
					$statement->bindValue(':Id', $post["id"], PDO::PARAM_INT);
					$statement->bindValue(':UserId', $userId, PDO::PARAM_INT );  
					if ($post["image"] === NULL)
					{
						$statement->bindValue(':Image', $post["image"], PDO::PARAM_NULL );
					}
					else
					{
						$statement->bindValue(':Image', $post["image"], PDO::PARAM_LOB );
					}
					
					$statement->execute();
					return TRUE;
				} 
				catch(PDOException $e) 
				{
					$this->error = "PDO: ".$e->getMessage();
					return FALSE;
				}
			}
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}
		
		public function insertNews($post, $userId)
		{
			if ($this->db_pdo !== FALSE)
			{		
				try 
				{
					$statement = $this->db_pdo->prepare("INSERT INTO `news` ( Title, Content, Image, UserId) values ( :Title, :Content, :Image, :UserId)");
					
					$statement->bindValue(':Title', $post["title"], PDO::PARAM_STR );       
					$statement->bindValue(':Content', $post["content"], PDO::PARAM_STR );
					if ($post["image"] === NULL)
					{
						$statement->bindValue(':Image', $post["image"], PDO::PARAM_NULL );
					}
					else
					{
						$statement->bindValue(':Image', $post["image"], PDO::PARAM_LOB );
					}
					$statement->bindValue(':UserId', $userId, PDO::PARAM_INT );					
					$statement->execute();
					return TRUE;
				} 
				catch(PDOException $e) 
				{
					return FALSE;
				}
			}
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}

		public function	deleteImage($post, $userId)
		{
			
			if ($this->db_pdo !== FALSE)
			{		
				try 
				{
					$statement = $this->db_pdo->prepare("
						UPDATE 	news 
						SET 	Image = NULL
						WHERE 	Id = :Id
						AND 	UserId = :UserId");
					
					$statement->bindValue(':Id', $post["id"], PDO::PARAM_INT);
					$statement->bindValue(':UserId', $userId, PDO::PARAM_INT );  
					
					$statement->execute();
					return TRUE;
				} 
				catch(PDOException $e) 
				{
					$this->error = "PDO: ".$e->getMessage();
					return FALSE;
				}
			}
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}

		public function getComments($NewsId)
		{
			if ($this->db_pdo !== FALSE) 
			{			
				$statement = $this->db_pdo->prepare( "
					SELECT 		Comments.Id 										id,
								DATE_FORMAT(TimeCreated, '%M %d, %Y %h:%i %p') 		timecreated,
								Content 											content,
								Hidden 												hidden,
								GuestName 											guestname,
								UserId												userid,
								users.UserName 										username
					FROM 		Comments
					LEFT JOIN	users ON Comments.UserId = users.Id
					WHERE 		Comments.NewsId = :Id
					ORDER BY 	TimeCreated");
				$statement->bindValue(':Id', $NewsId, PDO::PARAM_INT);
				$statement->execute(); 
				return $statement->fetchAll();
			}
			
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}


		
		public function deleteCommentById($id)
		{
			if ($this->db_pdo !== FALSE)
			{				
				try
				{
					$statement = $this->db_pdo->prepare("DELETE FROM `comments` WHERE `comments`.`Id` =".$id);
					$statement->execute(); 
					return TRUE;
				}
				catch(PDOException $e) 
				{
					$this->error = "PDO: ".$e->getMessage();
					return FALSE;
				}
			}
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}


		public function insertComment($post, $userId, $newsId)
		{
			if ($this->db_pdo !== FALSE)
			{		
				try 
				{
					$statement = $this->db_pdo->prepare("INSERT INTO `Comments` (  Content, UserId, GuestName, NewsId ) values (  :Content, :UserId, :GuestName, :NewsId)");
					 
					$statement->bindValue(':Content', $post["content"], PDO::PARAM_STR );
					$statement->bindValue(':GuestName', $post["guestname"], PDO::PARAM_STR );
					$statement->bindValue(':UserId', $userId, PDO::PARAM_INT );
					$statement->bindValue(':NewsId', $newsId, PDO::PARAM_INT );
					
					$statement->execute();
					return TRUE;
				} 
				catch(PDOException $e) 
				{
					return FALSE;
				}
			}
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}

		public function	setCommentVisibility($id, $value)
		{
			if ($this->db_pdo !== FALSE)
			{		
				try 
				{
					$statement = $this->db_pdo->prepare("
						UPDATE 	Comments 
						SET 	Hidden = :Hidden
						WHERE 	Id = :Id");
					
					$statement->bindValue(':Hidden', $value, PDO::PARAM_INT);
					$statement->bindValue(':Id', $id, PDO::PARAM_INT );  
					
					$statement->execute();
					return TRUE;
				} 
				catch(PDOException $e) 
				{
					$this->error = "PDO: ".$e->getMessage();
					return FALSE;
				}
			}
			$this->error = "PDO: not initialize properly";
			return FALSE;
		}
	}
?>
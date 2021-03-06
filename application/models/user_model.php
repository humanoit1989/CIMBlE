<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends Model
{
	/**
	 * The Constructor!
	 */
	public function __construct()
	{
		parent::Model();
	}
	
// ------------------------------------------------------------------------
	
	/**
	 * Get a list of all the registered users
	 *
	 * @access public
	 * @return array
	 **/
	public function get_users()
	{
		// try to get a list of users from the database
		$this->db->select('users.id, users.username, users.email');
		$query = $this->db->get('users');
		
		// if at least a user was retrieved, return the list of users...
		if ($query->num_rows() > 0)
			return $query->result();
		// ...else return NULL
		return NULL;
	} // End of get_users
	
// ------------------------------------------------------------------------
	
	/**
	 * Get one user from the database
	 *
	 * @access public
	 * @param int $user_id 
	 * @return object
	 **/
	public function get_user($user_id = NULL)
	{
		if(empty($user_id) || !is_valid_number($user_id))
			return NULL;
		
		$this->db->select('users.id, users.username, users.email');
		$query = $this->db->get_where('users', array('id' => $user_id), 1);
		
		if ($query->num_rows == 1)
			return $query->row();
		
		return NULL;
		
	} // End of get_user
	
// ------------------------------------------------------------------------
	
	/**
	 * Count the number of users registered
	 *
	 * @access public
	 * @param string $field 
	 * @param string|int $value 
	 * @return int
	 **/
	public function count_users($field = NULL, $value = NULL)
	{
		if ($field === NULL)
		{
			return $this->db->count_all_results('users');
		}
		else
		{
			$this->db->like($field,$value);
			$this->db->from('users');
			return $this->db->count_all_results();
			
		}
	} // End of count_users
	
// ------------------------------------------------------------------------
// Admin Methods
// ------------------------------------------------------------------------
	
	/**
	 * Add a new user to the database
	 *
	 * @access public
	 * @param string $username 
	 * @param string $password 
	 * @param string $email 
	 * @return bool
	 **/
	public function add_user($username, $password, $email)
	{
		if (empty($username) || empty($password) || empty($email))
			return FALSE;
		if ($this->azauth->register($username, $password, $email))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	} // End of add_user
	
// ------------------------------------------------------------------------
	
	/**
	 * Delete a registered user from the database
	 *
	 * @access public
	 * @param int $user_id 
	 * @return bool
	 **/
	public function delete_user($user_id)
	{
		// if there is only one user left, he can't be deleted
		if ($this->count_users() <= 1)
			return FALSE;
		
		// if there is no user with the same id as the one passed as parameter, return FALSE
		$match = $this->count_users('id',$user_id);
		if ($match != 1)
		{
			return FALSE;
		}
		else
		{
			// if everything went well, the user will get deleted
			$this->db->delete('users',array('id' => $user_id));
		}
	} // End of delete_user
	
} // End of User_model

/* End of file user_model.php */
/* Location: ./application/models/user_model.php */
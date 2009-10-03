<?php

class Posts extends Controller
{
	// constructor
	public function __construct()
	{
		parent::Controller();
		
		// load the post model
		$this->load->model('post_model');
		
		// load necessary helpers
		$this->load->helper('post_helper');
	}
	
	// php4 compatibility
	public function Posts()
	{
		self::__construct();
	}
	
	public function index()
	{
		// get the posts list and the posts count
		$posts = $this->post_model->get_posts();
		
		// if there are no posts we don't want to load the regular posts view file or we'll get an error
		$data['view_file'] = ($posts['count'] > 0) ? 'posts/index' : 'posts/no-posts';
		
		// put the posts list in the data array
		$data['posts'] = $posts['list'];
		
		/* ---------- */
		/* Pagination */
		/* ---------- */
		
		// config for the pagination of the content (posts)
		$data['posts_per_page'] = 3;
		$data['offset'] = $this->uri->segment(3);
		
		// If the offset is invalid or NULL (in which case the user goes back to the first page anyway)
		// the user is sent back to the first page and a feedback message is displayed
		if ((!is_valid_number($data['offset']) || !array_key_exists($data['offset'],$posts['list'])) && !empty($data['offset']))
		{	
			$this->session->set_flashdata('notice','Invalid Request');
			redirect('posts/index/0');
		}
		
		// load the pagination library
		$this->load->library('pagination');
		
		// config for the pagination links
		$config['base_url'] = site_url('/posts/index');
		$config['total_rows'] = $posts['count'];
		$config['per_page'] = $data['posts_per_page'];
		$config['num_links'] = 4;
		
		// initialize pagination with the configuration array above
		$this->pagination->initialize($config);
		
		// Create pagination links and store them in the data array
		$data['pagination_links'] = $this->pagination->create_links();
		
		// Dynamically generate the posts pagination everytime the user clicks on a pagination link
		$data['posts'] = paginate($posts['list'], $posts['count'], $data['posts_per_page'], $data['offset']);
		
		// Generate the dynamic breadcrumbs
		$data['section_name'] = array(
									array(
										'title' => 'Blog',
										'url' => 'posts/index'
									)
								);
		
		// the page number segment of the breadcrumbs will only appear if there is at least two pages
		if ($posts['count'] > $config['per_page'])
		{
			array_push($data['section_name'],	array(
													'title' => 'page ' . get_page_number($data['offset'],$data['posts_per_page']),
													'url' => 'posts/index/' . $data['offset']
												)
			);
		}
		
		$this->load->view('main', $data);
	} // End of index
	
	public function view($post_id)
	{
		$post = $this->post_model->get_post($post_id);
		$data['post'] = $post;
		
		if ($data['post'] === NULL)
		{
			$this->session->set_flashdata('notice','Invalid Request!');
			redirect(site_url());
		}
		
		$data['view_file'] = 'posts/view';
		$data['section_name'] = array(
									array(
										'title' => 'Blog',
										'url' => 'posts/index'
									),
									array(
										'title' => $post->title,
										'url' => 'posts/view/' . $post_id
									)
								);
		
		$this->load->view('main', $data);
	} // End of view
} // End of Posts controller

/* End of file posts.php */
/* Location: ./application/controller/posts.php */
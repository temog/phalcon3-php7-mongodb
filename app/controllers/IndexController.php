<?php

class IndexController extends ControllerBase {

	public function indexAction(){

		$page = $this->request->get('page') ?? 1;

		$latest = Blog::getLatestPost($page);
		$paginator = Blog::getPaginator($page);

		$this->view->setVar('latest', $latest);
		$this->view->setVar('paginator', $this->helper->paginator($paginator));
	}

	public function createAction(){

		if(! $this->request->isPost() || ! $this->security->checkToken()){
			return;
		}

		$title = $this->request->getPost('title');
		$body = $this->request->getPost('body');

		if(! Blog::createBlog($title, $body)){
			$this->flashSession->error('投稿失敗(´・ω・｀)');
			return;
		}

		$this->flashSession->success('成功(｀・ω・´)');
	}

	public function editAction($id){

		if(! $this->request->isPost() || ! $this->security->checkToken()){

			$blog = Blog::get($id);
			$_POST = (array) $blog;

			$this->view->setVar('blog', $blog);
			return;
		}

		$title = $this->request->getPost('title');
		$body = $this->request->getPost('body');

		if(! Blog::updateBlog($id, $title, $body)){
			$this->flashSession->error('編集失敗(´・ω・｀)');
			return;
		}

		$this->flashSession->success('成功(｀・ω・´)');
	}

	public function deleteAction($id){

		if(! $this->request->isPost() || ! $this->security->checkToken()){

			$blog = Blog::get($id);
			$_POST = (array) $blog;

			$this->view->setVar('blog', $blog);
			return;
		}

		if(! Blog::deleteBlog($id)){
			$this->flashSession->error('削除失敗(´・ω・｀)');
			return;
		}

		$this->flashSession->success('成功(｀・ω・´)');
		$this->response->redirect();
	}
}

